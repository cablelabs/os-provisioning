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

namespace Modules\HfcReq\Jobs;

use geoPHP;
use Exception;
use SplFileInfo;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertGpsFilesToGeoJsonJob //implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Iterable Array or Collection of Files to convert
     *
     * @var \Illuminate\Support\Collection of SplFileInfo
     */
    protected $files;

    /**
     * All Supported GPS File Formats
     *
     * @var array
     */
    protected $supportedFileTypes = [
        'WKT',
        'EWKT',
        'WKB',
        'EWKB',
        'GeoJSON',
        'KML',
        'GPX',
        'GeoRSS',
    ];

    /**
     * Create a new job instance.
     *
     * @param  \SplFileInfo|array|\Illuminate\Support\Collection  $files
     *
     * @return void
     */
    public function __construct($files)
    {
        if (! $files instanceof \Illuminate\Support\Collection) {
            $files = collect($files);
        }

        $files = $files->filter(function ($file) {
            return $file instanceof SplFileInfo;
        });

        if ($files->isEmpty()) {
            throw new InvalidArgumentException('Please provide one or more items of class SplFileInfo as parameter!');
        }

        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->each(function ($file) {
            $this->convertToGeoJson($file);
        });
    }

    protected function convertToGeoJson(SplFileInfo $file)
    {
        if (! in_array($file->getExtension(), $this->supportedFileTypes)) {
            throw new Exception('The provided File'.$file->getFilename().' has a not supported filetype!');
        }

        $gpsData = geoPHP::load(file_get_contents($file->getPath()), $file->getExtension());
        $output = ['type' => 'FeatureCollection', 'features' => [
            //['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'MultiLineString', 'coordinates' => []]],
            //['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'MultiPoint', 'coordinates' => []]],
            //['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'MultiPolygon', 'coordinates' => []]],
        ]];

        foreach ($gpsData->asArray() as $shape) {
            if (! isset($shape['type']) || ($shape['type'] == 'LineString' && count($shape['components']))) {
                dd($shape);
                continue;
            }

            if ($shape['type'] == 'LineString') {
                array_push($output['features'], ['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'LineString', 'coordinates' => $shape['components']]]);
                continue;
            }

            if ($shape['type'] == 'Point') {
                array_push($output['features'], ['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'Point', 'coordinates' => $shape['components']]]);
                continue;
            }

            array_push($output['features']['geometry']['coordinates'], $shape['components']);
        }


    }
}
