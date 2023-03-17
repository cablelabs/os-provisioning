<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\extensions\geocoding;

use App\GlobalConfig;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
     * Geocode address of a model to a geoposition and update the x,y values of
     * that model..
     *
     * @param  bool  $save
     * @return void|array
     */
    public function geocode($save = true): ?array
    {
        // don't ask API in testing mode (=faked data)
        if (config('app.env') == 'testing') {
            Log::debug('Testing mode – will not ask Geo-APIs with faked data');

            return $this->noGeoPositionFound($save);
        }

        // first try to get geocoding from OSM
        $geodata = $this->tryGeocode('OSM Nominatim');

        // fallback: ask here
        if (! $geodata) {
            $geodata = $this->tryGeocode('HERE GeoCoding');
        }

        // fallback: ask google maps
        if (! $geodata) {
            $geodata = $this->tryGeocode('Google GeoCoding');
        }

        if (! $geodata) {
            return $this->noGeoPositionFound($save);
        }

        $this->updateGeoPosition($geodata);

        if ($save) {
            $this->save();
        }

        return $geodata;
    }

    /**
     * Call the given GeoCode API and query the position of the given address.
     *
     * @param  string  $api
     * @return void|array
     */
    protected function tryGeocode(string $api): ?array
    {
        try {
            return $this->geocodeVia($api);
        } catch (\Exception $ex) {
            $msg = 'Error in geocoding against '.$api.' API: '.$ex->getMessage();
            Session::push('tmp_error_above_form', $msg);
            Log::error("$msg (".get_class($ex).' in '.$ex->getFile().' line '.$ex->getLine().')');
        }

        return null;
    }

    /**
     * Generator method to not use dynamic method names as this is easier found via grep and find.
     *
     * @param  string  $api
     * @return void|array
     */
    public function geocodeVia(string $api): ?array
    {
        $api = explode(' ', $api)[0];

        if (strtolower($api) === 'here') {
            return $this->geocodeHere();
        }

        if (strtolower($api) === 'google') {
            return $this->geocodeGoogle();
        }

        return $this->geocodeOsm();
    }

    /**
     * Handle the case when no geocosing service provider was able to determine
     * a position for the given address.
     *
     * @param  bool  $save
     * @return void
     */
    protected function noGeoPositionFound(bool $save): void
    {
        if (in_array($this->geocode_state, ['OK', 'n/a', 'HERE API NO RESULTS', 'DATA_VERIFICATION_FAILED'])) {
            $reason = trans("view.geocoding.error.{$this->geocode_state}");
        }

        $message = trans('view.geocoding.failed', ['reason' => $reason ?? $this->geocode_state]);
        Log::warning("geocoding failed: {$this->geocode_state}"); //logs should stay in English

        // if running from console: preserve existing geodata (could have been be imported or manually set in older times)
        if (\App::runningInConsole()) {
            $this->geocode_source = 'n/a (unchanged existing data)';
        } else {
            // if running interactively: delete probably outdated geodata and inform user
            $this->lat = null;
            $this->lng = null;
            $this->geocode_source = 'n/a';

            Session::push('tmp_error_above_form', $message);
        }

        if ($save) {
            $this->save();
        }
    }

    /**
     * Update the position data of the given model.
     *
     * @param  array  $geodata
     * @return void
     */
    protected function updateGeoPosition(array $geodata): void
    {
        $this->lat = $geodata['latitude'];
        $this->lng = $geodata['longitude'];
        $this->geocode_source = $geodata['source'];
        $this->geocode_state = 'OK';

        Log::info('Geocoding successful, result: '.$this->lat.','.$this->lng.' (source: '.$geodata['source'].')');
    }

    /**
     * Some housenumbers need special handling. This method splits them to the needed parts.
     *
     * @author Patrick Reichel
     */
    protected function geocodingSplitHouseNumber($house_number)
    {
        // regex from https://stackoverflow.com/questions/10180730/splitting-string-containing-letters-and-numbers-not-separated-by-any-particular
        return preg_split("/(,?\s+)|((?<=[-\/a-z])(?=\d))|((?<=\d)(?=[-\/a-z]))/i", strtolower($house_number));
    }

    /**
     * Get geodata from OpenStreetMap Nominatiom API
     *
     * @return void|array
     *
     * @author Patrick Reichel
     */
    protected function geocodeOsm(): ?array
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        $geodata = null;
        $base_url = 'https://nominatim.openstreetmap.org/search';

        if (! filter_var(config('app.osmNominatimApiMail'), FILTER_VALIDATE_EMAIL)) {
            $message = 'Unable to ask OpenStreetMap Nominatim API for geocoding – OSM_NOMINATIM_EMAIL not set';
            Session::push('tmp_warning_above_form', $message);
            Log::warning($message);

            return null;
        }

        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;

        // problem: data is inconsistent in OSM – housenumbers with additional character can have two formats:
        // “104 a” or “104a”; there is an 3-years-open bug report: https://trac.openstreetmap.org/ticket/5256
        // so we have to try both variants if the first one does not return a result
        $houseNr = $this->house_number ?? $this->house_nr;
        $parts = $this->geocodingSplitHouseNumber($houseNr);

        if (count($parts) < 2) {
            $housenumber_variants = [$parts[0]];
        } else {
            $housenumber_variants = [
                implode('', $parts),    // more often used according to bug report
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
                'country_code' => $country_code,
                'email' => config('app.osmNominatimApiMail'),  // has to be set (https://operations.osmfoundation.org/policies/nominatim); else 403 Forbidden
                'format' => 'json',         // return format
                'dedupe' => '1',            // only one geolocation (even if address is split to multiple places)?
                'polygon' => '0',           // include surrounding polygons?
                'addressdetails' => '0',    // not available using API
                'limit' => '1',             // only request one result
            ];

            $url = $base_url.'?';

            $tmp_params = [];
            foreach ($params as $key => $value) {
                array_push($tmp_params, urlencode($key).'='.urlencode($value));
            }
            $url .= implode('&', $tmp_params);

            $className = (new \ReflectionClass($this))->getShortName();
            Log::info("Trying to geocode {$className} {$this->id} against $url");

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
                        if (\Str::contains(strtolower($display_name), $variant)) {  // don't check for startswith; sometimes a company name is added before the house number
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
            if (count($housenumber_variants) > 1) {
                sleep(1);
            }
        }

        if (! $geodata) {
            Log::warning("OSM Nominatim geocoding for {$className} {$this->id} failed");

            return null;
        }

        return $geodata;
    }

    /**
     * Get geodata from HERE GeoCoding API
     *
     * @return array|null
     */
    protected function geocodeHere(): ?array
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        if (! config('app.hereApiKey', false)) {
            $message = 'Unable to ask Here Geocoding API – HERE_API_KEY not set';
            Session::push('tmp_warning_above_form', $message);
            Log::warning($message);

            return null;
        }

        $key = config('app.hereApiKey');
        $className = (new \ReflectionClass($this))->getShortName();

        $houseNr = $this->house_number ?? $this->house_nr;
        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;
        $address = urlencode($houseNr.' '.$this->street.', '.$this->zip.', '.$country_code);
        $url = "https://geocode.search.hereapi.com/v1/geocode?apikey=$key&limit=1&q={$address}";

        Log::info("Trying to geocode {$className} {$this->id} against $url");
        $resp_json = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 3]]));
        $resp = json_decode($resp_json, true);

        if (isset($resp['status']) || ! count($resp['items'])) {
            $this->geocode_state = $resp['status'] ?? 'HERE API NO RESULTS';
            Log::warning("HERE geocoding for {$className} {$this->id} failed: {$this->geocode_state}");

            return null;
        }

        $result = $resp['items'][0];

        if (
            ! $result['scoring']['queryScore'] == 1.0 ||
            ! in_array($result['resultType'], ['houseNumber', 'place', 'locality'])
        ) {
            $this->geocode_state = 'DATA_VERIFICATION_FAILED';
            Log::warning("HERE geocoding for {$className} {$this->id} failed: {$this->geocode_state}");

            return null;
        }

        return [
            'latitude' => $result['position']['lat'],
            'longitude' => $result['position']['lng'],
            'source' => 'HERE Geolocation API',
        ];
    }

    /**
     * Get geodata from Google Maps GeoCoding API
     *
     * @return void
     *
     * @author Torsten Schmidt, Patrick Reichel
     */
    protected function geocodeGoogle(): ?array
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        if (! $key = config('app.googleApiKey')) {
            $message = 'Unable to ask Google Geocoding API – GOOGLE_API_KEY not set';
            Session::push('tmp_warning_above_form', $message);
            Log::warning($message);

            return null;
        }

        $className = (new \ReflectionClass($this))->getShortName();
        $houseNr = $this->house_number ?? $this->house_nr;
        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;
        $address = urlencode($houseNr.' '.$this->street.', '.$this->zip.', '.$country_code);

        // google map geocode api url
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}&key={$key}";
        Log::info("Trying to geocode {$className} {$this->id} against {$url}");

        // get the json response
        $resp_json = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 3]]));
        $resp = json_decode($resp_json, true);
        $status = Arr::get($resp, 'status', 'n/a');

        // response status will be 'OK', if able to geocode given address
        if ($status !== 'OK') {
            $this->geocode_state = $status;
            Log::warning("Google geocoding for {$className} {$this->id} failed: {$this->geocode_state}");

            return null;
        }

        // get the important data
        $lati = Arr::get($resp, 'results.0.geometry.location.lat', null);
        $longi = Arr::get($resp, 'results.0.geometry.location.lng', null);
        $formatted_address = Arr::get($resp, 'results.0.formatted_address', null);
        $location_type = Arr::get($resp, 'results.0.geometry.location_type', null);
        $partial_match = Arr::get($resp, 'results.0.partial_match', null);

        $matches = ['ROOFTOP'];
        $interpolated_matches = ['ROOFTOP', 'RANGE_INTERPOLATED'];

        if (! $lati || ! $longi || ! $formatted_address || ! in_array($location_type, $interpolated_matches)) {
            $this->geocode_state = 'DATA_VERIFICATION_FAILED';
            Log::warning("Google geocoding for {$className} {$this->id} failed: {$this->geocode_state}");

            return null;
        }

        // verify if data is complete and a real match
        if (! $partial_match && in_array($location_type, $matches)) {
            return [
                'latitude' => $lati,
                'longitude' => $longi,
                'source' => 'Google Geocoding API',
            ];
        }

        // check if partial match (interpolated geocoords seem to be pretty good!)
        // mark source as tainted to give the user a hint
        return [
            'latitude' => $lati,
            'longitude' => $longi,
            'source' => 'Google Geocoding API (interpolated)',
        ];
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

        if ((is_null($this->lng) && is_null($this->lat)) || ($this->exists && multi_array_key_exists(['street', 'house_nr', 'zip', 'city'], $changes))) {
            $this->geocode(false);
        } elseif (multi_array_key_exists(['lng', 'lat'], $changes)) {
            $this->geocode_source = \App\BaseModel::getUser();
        }
    }
}
