<?php

namespace App\Http\Controllers;

use geoPHP\geoPHP;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;
use Modules\Ticketsystem\Entities\Ticket;

abstract class BaseTopographyController extends BaseController
{
    /**
     * Determines the range of intensity of the Heatmap, for the amplitude values of the modem
     *
     * @var int minHeatIntensity is the minimum for the database query
     * @var int maxHeatIntensity is the max for the database query
     */
    protected $minHeatIntensity = 0;
    protected $maxHeatIntensity = 5;

    /**
     * Generate GeoJson Data from Netelement Files
     *
     * @param  Collection  $netelements
     * @return array GPS files, like ['file', 'descr']
     *
     * @author Christian Schramm
     */
    protected function parseGeoFiles(Collection $netElements): array
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
     * @return Collection
     */
    protected function getTicketMapData(): Collection
    {
        if (! Module::collections()->has('Ticketsystem')) {
            return collect();
        }

        $lookup = [
            Ticket::STATES['New'] => 0,
            Ticket::STATES['Paused'] => 1,
            Ticket::STATES['In Progress'] => 1,
            Ticket::STATES['Closed'] => 2,
        ];

        return Ticket::with('ticketable', 'users:first_name,last_name')
            ->where('state', '!=', Ticket::STATES['Closed'])
            ->orWhere(function ($query) {
                $query->where('state', Ticket::STATES['Closed'])
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

    /**
     * Set view bounds for initial map view
     *
     * @param  Collection  $elements
     * @return void
     */
    public function mapFitBounds(Collection $elements): array
    {
        return [
            'maxLat' => $elements->max('lat'),
            'maxLng' => $elements->max('lng'),
            'minLat' => $elements->min('lat'),
            'minLng' => $elements->min('lng'),
        ];
    }
}
