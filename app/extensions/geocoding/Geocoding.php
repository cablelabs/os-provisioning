<?php

namespace App\extensions\geocoding;

use Log;
use Session;
use App\GlobalConfig;
use Illuminate\Support\Arr;

/**
 * Geocoding API
 * Translate an address (like: Deutschland, Marienberg, Waldrand 4) into a geoposition (x,y)
 *
 * Used in Modem, Node, Realty
 *
 * @author: Torsten Schmidt, Patrick Reichel, Nino Ryschawy
 */
trait Geocoding
{
    // private variable to hold the last Geocoding response state
    // use geocode_last_status()
    private $geocode_state = null;

    /**
     * Modem Geocoding Function
     * Geocode the modem address value in a geoposition and update values to x,y. Please
     * note that the function is working in object context, so no addr parameters are required.
     *
     * @param save: Update Modem x,y value with a save() to DB. Notice this calls Observer !
     * @return: true on success, false if coding fails. For error log see geocode_last_status()
     * @author: Torsten Schmidt
     *
     * TODO: split in a general geocoding function and a modem specific one
     */
    public function geocode($save = true)
    {
        $geodata = null;

        // first try to get geocoding from OSM
        try {
            $geodata = $this->_geocode_osm_nominatim();
        } catch (\Exception $ex) {
            $msg = 'Error in geocoding against OSM Nominatim: '.$ex->getMessage();
            Session::push('tmp_error_above_form', $msg);
            Log::error("$msg (".get_class($ex).' in '.$ex->getFile().' line '.$ex->getLine().')');
        }

        // fallback: ask google maps
        if (! $geodata) {
            try {
                $geodata = $this->_geocode_google_maps($save);
            } catch (\Exception $ex) {
                $msg = 'Error in geocoding against google maps: '.$ex->getMessage();
                Session::push('tmp_error_above_form', $msg);
                Log::error("$msg (".get_class($ex).' in '.$ex->getFile().' line '.$ex->getLine().')');
            }
        }

        if ($geodata) {
            $this->y = $geodata['latitude'];
            $this->x = $geodata['longitude'];
            $this->geocode_source = $geodata['source'];
            $this->geocode_state = 'OK';

            Log::info('Geocoding successful, result: '.$this->y.','.$this->x.' (source: '.$geodata['source'].')');
        } else {
            // no geodata determined
            if (! \App::runningInConsole()) {
                // if running interactively: delete probably outdated geodata and inform user
                $this->y = '';
                $this->x = '';
                $this->geocode_source = 'n/a';
                $message = "Could not determine geo coordinates ($this->geocode_state) – please add manually";
                Session::push('tmp_error_above_form', $message);
            } else {
                // if running from console: preserve existing geodata (could have been be imported or manually set in older times)
                $this->geocode_source = 'n/a (unchanged existing data)';
            }
            Log::warning('geocoding failed');
        }

        if ($save) {
            $this->save();
        }

        return $geodata;
    }

    /**
     * Some housenumbers need special handling. This method splits them to the needed parts.
     *
     * @author Patrick Reichel
     */
    protected function _split_housenumber_for_geocoding($house_number)
    {
        // regex from https://stackoverflow.com/questions/10180730/splitting-string-containing-letters-and-numbers-not-separated-by-any-particular
        return preg_split("/(,?\s+)|((?<=[-\/a-z])(?=\d))|((?<=\d)(?=[-\/a-z]))/i", strtolower($house_number));
    }

    /**
     * Get geodata from OpenStreetMap
     *
     * @author Patrick Reichel
     */
    protected function _geocode_osm_nominatim()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // don't ask API in testing mode (=faked data)
        if (env('APP_ENV') == 'testing') {
            Log::debug('Testing mode – will not ask OSM Nominatim with faked data');

            return;
        }

        $geodata = null;
        $base_url = 'https://nominatim.openstreetmap.org/search';

        if (! filter_var(env('OSM_NOMINATIM_EMAIL'), FILTER_VALIDATE_EMAIL)) {
            $message = 'Unable to ask OpenStreetMap Nominatim API for geocoding – OSM_NOMINATIM_EMAIL not set';
            Session::push('tmp_warning_above_form', $message);
            Log::warning($message);

            return false;
        }

        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;

        // problem: data is inconsistent in OSM – housenumbers with additional character can have two formats:
        // “104 a” or “104a”; there is an 3-years-open bug report: https://trac.openstreetmap.org/ticket/5256
        // so we have to try both variants if the first one does not return a result
        $houseNr = $this->house_number ?? $this->house_nr;
        $parts = $this->_split_housenumber_for_geocoding($houseNr);

        if (count($parts) < 2) {
            $housenumber_variants = [$parts[0]];
        } else {
            $housenumber_variants = [
                implode('', $parts),	// more often used according to bug report
                implode(' ', $parts),
            ];
        }

        foreach ($housenumber_variants as $housenumber_prepared) {
            // see https://wiki.openstreetmap.org/wiki/DE:Nominatim#Parameter for details
            // we are using the structured format (faster, saves server ressources – but marked experimental)
            $params = [
                'street' => "$housenumber_prepared $this->street",
                'postalcode' => $this->zip,
                'city' => $this->city,
                'country' => $country_code,
                'email' => env('OSM_NOMINATIM_EMAIL'),	// has to be set (https://operations.osmfoundation.org/policies/nominatim); else 403 Forbidden
                'format' => 'json',			// return format
                'dedupe' => '1',			// only one geolocation (even if address is split to multiple places)?
                'polygon' => '0',			// include surrounding polygons?
                'addressdetails' => '0',	// not available using API
                'limit' => '1',				// only request one result
            ];

            $url = $base_url.'?';
            if ($params) {
                $tmp_params = [];
                foreach ($params as $key => $value) {
                    array_push($tmp_params, (urlencode($key).'='.urlencode($value)));
                }
                $url .= implode('&', $tmp_params);
            }

            $className = (new \ReflectionClass($this))->getShortName();
            Log::info("Trying to geocode $className $this->id against $url");

            $geojson = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 3]]));

            $geodata_raw = json_decode($geojson, true);

            $matches = ['building', 'house', 'amenity', 'shop', 'tourism'];
            foreach ($geodata_raw as $entry) {
                $class = Arr::get($entry, 'class', '');
                $type = Arr::get($entry, 'type', '');
                $display_name = Arr::get($entry, 'display_name', '');
                $lat = Arr::get($entry, 'lat', null);
                $lon = Arr::get($entry, 'lon', null);

                // check if returned entry is of certain type (e.g. “highway” indicates fuzzy match)
                if ((in_array($class, $matches) || in_array($type, $matches)) && $lat && $lon) {

                    // as both variants can appear in resulting address: check for all of them
                    foreach ($housenumber_variants as $variant) {
                        if (\Str::contains(strtolower($display_name), $variant)) {	// don't check for startswith; sometimes a company name is added before the house number
                            $geodata = [
                                'latitude' => $lat,
                                'longitude' => $lon,
                                'source' => 'OSM Nominatim',
                            ];
                            break;
                        }
                    }
                }
            }

            // if this try results in a match: exit the loop
            if ($geodata) {
                break;
            }

            // sleep to respect usage policy
            if (count($housenumber_variants > 1)) {
                sleep(1);
            }
        }

        if (! $geodata) {
            Log::warning("OSM Nominatim geocoding for $className $this->id failed");

            return false;
        }

        return $geodata;
    }

    /**
     * Get geodata from google maps
     *
     * @author Torsten Schmidt, Patrick Reichel
     * @return array 	Geodata [lat, lon, source]
     */
    protected function _geocode_google_maps()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // don't ask API in testing mode (=faked data)
        if (env('APP_ENV') == 'testing') {
            Log::debug('Testing mode – will not ask Google Geocoding API with faked data');

            return;
        }

        $geodata = null;

        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;

        // beginning on 2018-06-11 geocode api can only be used with an api key (otherwise returning error)
        // ⇒ https://cloud.google.com/maps-platform/user-guide
        if (date('c') > '2018-06-10') {
            if (! env('GOOGLE_API_KEY')) {
                $message = 'Unable to ask Google Geocoding API – GOOGLE_API_KEY not set';
                Session::push('tmp_warning_above_form', $message);
                Log::warning($message);

                return false;
            }
            $key = '&key='.$_ENV['GOOGLE_API_KEY'];
        } else {
            // Load google key if .ENV is set
            $key = '';
            if (env('GOOGLE_API_KEY')) {
                $key = '&key='.$_ENV['GOOGLE_API_KEY'];
            }
        }

        $className = (new \ReflectionClass($this))->getShortName();
        $houseNr = $this->house_number ?? $this->house_nr;

        // url encode the address

        $address = urlencode($this->street.' '.$houseNr.', '.$this->zip.', '.$country_code);

        // google map geocode api url
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}$key";
        Log::info("Trying to geocode $className $this->id against $url");

        // get the json response
        $resp_json = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 3]]));

        $resp = json_decode($resp_json, true);

        $status = Arr::get($resp, 'status', 'n/a');

        // response status will be 'OK', if able to geocode given address
        if ($status == 'OK') {

            // get the important data
            $lati = Arr::get($resp, 'results.0.geometry.location.lat', null);
            $longi = Arr::get($resp, 'results.0.geometry.location.lng', null);
            $formatted_address = Arr::get($resp, 'results.0.formatted_address', null);
            $location_type = Arr::get($resp, 'results.0.geometry.location_type', null);
            $partial_match = Arr::get($resp, 'results.0.partial_match', null);

            $matches = ['ROOFTOP'];
            $interpolated_matches = ['ROOFTOP', 'RANGE_INTERPOLATED'];
            // verify if data is complete and a real match
            if (
                $lati &&
                $longi &&
                $formatted_address &&
                ! $partial_match &&
                in_array($location_type, $matches)
            ) {
                $geodata = [
                    'latitude' => $lati,
                    'longitude' => $longi,
                    'source' => 'Google Geocoding API',
                ];

                return $geodata;
            }
            // check if partial match (interpolated geocoords seem to be pretty good!)
            // mark source as tainted to give the user a hint
            elseif (
                $lati &&
                $longi &&
                $formatted_address &&
                $partial_match &&
                in_array($location_type, $interpolated_matches)
            ) {
                $geodata = [
                    'latitude' => $lati,
                    'longitude' => $longi,
                    'source' => 'Google Geocoding API (interpolated)',
                ];

                return $geodata;
            } else {
                $this->geocode_state = 'DATA_VERIFICATION_FAILED';
                Log::warning("Google geocoding for $className $this->id failed: $this->geocode_state");

                return;
            }
        } else {
            $this->geocode_state = $status;
            Log::warning("Google geocoding for $className $this->id failed: $this->geocode_state");

            return;
        }
    }

    /**
     * Set geocode data dependent of the changed attributes of the model
     *
     * This function is intended for use in models Observer updating
     *
     * @author Nino Ryschawy
     */
    public function setGeocodes()
    {
        $changes = $this->getDirty();

        if ((is_null($this->x) && is_null($this->y)) || ($this->exists && multi_array_key_exists(['street', 'house_nr', 'zip', 'city'], $changes))) {
            $this->geocode(false);
        } elseif (multi_array_key_exists(['x', 'y'], $changes)) {
            $this->geocode_source = \App\BaseModel::getUser();
        }
    }
}
