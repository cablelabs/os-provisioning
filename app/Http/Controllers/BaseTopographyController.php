<?php

namespace App\Http\Controllers;

use geoPHP\geoPHP;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\HfcReq\Entities\NetElement;
use Nwidart\Modules\Facades\Module;
use SplFileInfo;

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
     * Convert Infrastructure Data from Netelement Files to GeoJSON
     *
     * @param  Collection  $netelements
     * @return array GeoJSON Data seperated in Lines and points
     *
     * @author Christian Schramm
     */
    protected function parseGeoFiles(Collection $netElements): array
    {
        $points = [];
        $lines = ['type' => 'FeatureCollection', 'features' => []];

        $netElements->whereNotNull('infrastructure_file')
            ->unique('infrastructure_file')
            ->each(function ($netElement) use (&$points, &$lines) {
                $infrastructureFile = new SplFileInfo(storage_path(NetElement::GPS_FILE_PATH."/{$netElement->infrastructure_file}"));

                if (! $infrastructureFile->isFile()) {
                    return;
                }

                foreach (geoPHP::load(file_get_contents($infrastructureFile), $infrastructureFile->getExtension())->asArray() as $shape) {
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

                    if ($shape['type'] == 'Polygon') {
                        Log::info('Ignore Polygon shapes from Infrastructure file in Topography. Currently not supported!', $shape);
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
