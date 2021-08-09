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

        return [
            'points' => $points,
            'lines' => $lines,
        ];
    }

    /**
     * retrieve file if existent, this can be only used by authenticated and
     * authorized users (see corresponding Route::get in Http/routes.php)
     *
     * @author Ole Ernst
     *
     * @param string $type filetype, either kml or svg
     * @param string $filename name of the file
     * @return mixed
     */
    public function getKML($type, $filename)
    {
        $path = storage_path("app/data/hfcbase/$type/$filename");
        if (file_exists($path)) {
            return \Response::file($path);
        } else {
            return \App::abort(404);
        }
    }
}
