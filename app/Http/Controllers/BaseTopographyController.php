<?php

namespace App\Http\Controllers;

use geoPHP\geoPHP;
use Nwidart\Modules\Facades\Module;

abstract class BaseTopographyController extends BaseController
{
    /**
     * HTML link Target
     *
     * @var string
     */
    protected $htmlTarget = 'blank';

    protected $edit_left_md_size = 12;

    /**
     * Determines the range of intensity of the Heatmap, for the amplitude values of the modem
     *
     * @var int minHeatIntensity is the minimum for the database query
     * @var int maxHeatIntensity is the max for the database query
     */
    protected $minHeatIntensity = 0;
    protected $maxHeatIntensity = 5;

    /*
     * Constructor: Set local vars
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Generate GeoJson Data from Netelement Files
     *
     * @param  \Illumnate\Database\Eloqunt\Collection  $netelements
     * @return Collection KML files, like ['file', 'descr']
     *
     * @author Christian Schramm
     */
    protected function parseGeoFiles($netElements)
    {
        $points = [];
        $lines = ['type' => 'FeatureCollection', 'features' => []];

        $netElements->whereNotNull('geojson')
            ->unique('geojson')
            ->each(function ($netElement) use (&$points, &$lines) {
                $kml = geoPHP::load(file_get_contents(storage_path('app/data/hfcbase/gpsData/'.$netElement->geojson)), 'kml');

                foreach ($kml->asArray() as $shape) {
                    if (! isset($shape['type']) || ($shape['type'] == 'LineString' && in_array(count($shape['components']), [0, 1]))) {
                        \Log::info('Skipping corrupted shape of KML', $shape);
                        continue;
                    }

                    if ($shape['type'] == 'GeometryCollection') {
                        foreach ($shape['components'] as $component) {
                            array_push($lines['features'], ['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'LineString', 'coordinates' => $component['components']]]);
                        }

                        continue;
                    }

                    if ($shape['type'] == 'LineString') {
                        array_push($lines['features'], ['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'LineString', 'coordinates' => $shape['components']]]);
                        continue;
                    }

                    if ($shape['type'] == 'Point') {
                        array_push($points, $shape['components']);
                        continue;
                    }

                    array_push($lines['features']['geometry']['coordinates'], $shape['components']);
                }
            });

        return [
            'points' => $points,
            'lines' => $lines,
        ];
    }

    /**
     * Get position data for all Tickets with a valid position.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTicketMapData()
    {
        if (! Module::collections()->has('Ticketsystem')) {
            return collect();
        }

        $lookup = [
            \Modules\Ticketsystem\Entities\Ticket::STATES['New'] => 0,
            \Modules\Ticketsystem\Entities\Ticket::STATES['Paused'] => 1,
            \Modules\Ticketsystem\Entities\Ticket::STATES['In Progress'] => 1,
            \Modules\Ticketsystem\Entities\Ticket::STATES['Closed'] => 2,
        ];

        return \Modules\Ticketsystem\Entities\Ticket::with('ticketable', 'users:first_name,last_name')
            ->where('state', '!=', \Modules\Ticketsystem\Entities\Ticket::STATES['Closed'])
            ->orWhere(function ($query) {
                $query->where('state', \Modules\Ticketsystem\Entities\Ticket::STATES['Closed'])
                    ->where('updated_at', '>=', now()->subMinutes(5));
            })
            ->get(['id', 'name', 'priority', 'description', 'state', 'ticketable_id', 'ticketable_type'])
            ->filter->hasValidPositionData()
            ->map->setPositionData()
            ->map(function ($ticket) use ($lookup) {
                $ticket->icon = $lookup[$ticket->state];
                $ticket->link = route('Ticket.edit', $ticket['id']);

                return $ticket;
            });
    }

    public function mapFitBounds($elements)
    {
        return [
            'maxLat' => $elements->max('lat'),
            'maxLng' => $elements->max('lng'),
            'minLat' => $elements->min('lat'),
            'minLng' => $elements->min('lng'),
        ];
    }
}
