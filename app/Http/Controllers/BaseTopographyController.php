<?php

namespace App\Http\Controllers;

use geoPHP;
use Spinen\Geometry\GeometryFacade as Geometry;

abstract class BaseTopographyController extends BaseController
{
    /**
     * HTML link Target
     *
     * @var string
     */
    protected $htmlTarget = 'blank';

    protected $edit_left_md_size = 12;

    /*
    @author: John Adebayo
    Private property determines the range of intensity of the Heatmap, for the amplitude values
    of the modem
    $min_value is the minimum for the database query
    $max_value is the max for the database query
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
     * KML Upload Array: Generate the KML file array
     *
     * @param \Illumnate\Database\Eloqunt\Collection $netelements
     * @return Collection KML files, like ['file', 'descr']
     *
     * @author Torsten Schmidt, Christian Schramm
     */
    protected function parseKmlFiles($netElements)
    {
        $points = [];
        $lines = ['type' => 'FeatureCollection', 'features' => []];

        $netElements->where('kml_file', '!=', '')
            ->unique('kml_file')
            ->each(function ($netElement) use (&$points, &$lines) {
                $kml = geoPHP::load(file_get_contents(storage_path("app/data/hfcbase/kml_static/".basename($netElement->kml_file))), 'kml');

                foreach ($kml->asArray() as $shape) {
                    if (!isset($shape['type']) || ($shape['type'] == 'LineString' && count($shape['components']) < 2)) {
                        continue;
                    }

                    if ($shape['type'] == 'LineString') {
                        array_push($lines['features'], ['type' => 'Feature', 'properties' => ['test' => '1'], 'geometry' => ['type' => 'LineString', "coordinates" => $shape['components']]]);
                        continue;
                    }

                    if ($shape['type'] == 'Point') {
                        array_push($points, $shape['components']);
                        continue;
                    }

                    array_push($lines['features']['geometry']['coordinates'], $shape['components']);
                }
            });

        return cache(['kml' => [
            'points' => $points,
            'lines' => $lines,
        ]]);
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
}
