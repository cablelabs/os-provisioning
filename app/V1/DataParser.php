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

/**
 * Data Parser
 *
 * @author     Esben Petersen
 *
 * @link       https://github.com/esbenp/architect/blob/master/src/Architect.php
 */

namespace App\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DataParser
{
    /**
     * Parse a collection using given modes.
     *
     * @param  mixed  $data  The collection to be parsed
     * @param  array  $modes  The modes to be used, format is ['relation' => 'mode']
     * @param  string  $key  A key to hoist the collection into the root array
     * @return array
     */
    public function parseData($data, array $modes, $key = null)
    {
        $return = [];

        uksort($modes, function ($a, $b) {
            return substr_count($b, '.') - substr_count($a, '.');
        });

        if (self::isCollection($data)) {
            $parsed = $this->parseCollection($modes, $data, $return);
        } else {
            $parsed = $this->parseResource($modes, $data, $return);
        }

        if ($key !== null) {
            $return[$key] = $parsed;
        } else {
            $return = $parsed;
        }

        return $return;
    }

    /**
     * Parse a collection using given modes
     *
     * @param  array  $modes
     * @param  mixed  $collection
     * @param  array  $root
     * @param  string  $fullPropertyPath
     * @return mixed
     */
    private function parseCollection(array $modes, $collection, &$root, $fullPropertyPath = '')
    {
        if (is_array($collection)) {
            foreach ($collection as $i => $resource) {
                $collection[$i] = $this->parseResource($modes, $resource, $root, $fullPropertyPath);
            }
        } elseif ($collection instanceof Collection) {
            $collection = $collection->map(function ($resource) use ($modes, &$root, $fullPropertyPath) {
                return $this->parseResource($modes, $resource, $root, $fullPropertyPath);
            });
        }

        return $collection;
    }

    /**
     * Parse a single resource using given modes
     *
     * @param  array  $modes
     * @param  mixed  $resource
     * @param  array  $root
     * @param  string  $fullPropertyPath
     * @return mixed
     */
    private function parseResource(array $modes, &$resource, &$root, $fullPropertyPath = '')
    {
        foreach ($modes as $relation => $mode) {
            $steps = explode('.', $relation);

            $property = array_shift($steps);
            if (is_array($resource)) {
                if ($resource[$property] === null) {
                    continue;
                }

                $object = &$resource[$property];
            } else {
                if ($resource->{$property} === null) {
                    continue;
                }

                $object = &$resource->{$property};
            }

            if (! empty($steps)) {
                // More levels exist in this relation.
                // We want a drill down and resolve the deepest level first.

                $path = implode('.', $steps);
                $modes = [
                    $path => $mode,
                ];

                // Add the previous levels to the full path so it can be used
                // to populate the root level properly.
                $fullPropertyPath .= $property.'.';

                if (self::isCollection($object)) {
                    $object = $this->parseCollection($modes, $object, $root, $fullPropertyPath);
                } else {
                    $object = $this->parseResource($modes, $object, $root, $fullPropertyPath);
                }
            }

            // Reset the full property path after running a full relation
            $fullPropertyPath = '';
            self::setProperty($resource, $property, $object);
        }

        return $resource;
    }

    /**
     * @param  $objectOrArray
     * @param  $property
     * @param  $value
     */
    public static function setProperty(&$objectOrArray, $property, $value)
    {
        if ($objectOrArray instanceof Model) {
            if ($property) {
                if ($objectOrArray->relationLoaded($property) && ! self::isPrimitive($value)) {
                    $objectOrArray->setRelation($property, $value);
                } else {
                    unset($objectOrArray[$property]);
                    $objectOrArray->setAttribute($property, $value);
                }
            }
        } elseif (is_array($objectOrArray)) {
            $objectOrArray[$property] = $value;
        } else {
            $objectOrArray->{$property} = $value;
        }
    }

    /**
     * Is the variable a primitive type
     *
     * @param  mixed  $input
     * @return bool
     */
    public static function isPrimitive($input)
    {
        return ! is_array($input) && ! ($input instanceof Model) && ! ($input instanceof Collection);
    }

    /**
     * Is the input a collection of resources?
     *
     * @param  mixed  $input
     * @return bool
     */
    public static function isCollection($input)
    {
        return is_array($input) || $input instanceof Collection;
    }
}
