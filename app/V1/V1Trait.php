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
 * V1 Trait
 *
 * @author     Esben Petersen
 *
 * @link       https://github.com/esbenp/bruno/blob/master/src/LaravelController.php
 */

namespace App\V1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait V1Trait
{
    /**
     * Defaults
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * Create a json response
     *
     * @param  mixed  $data
     * @param  int  $statusCode
     * @param  array  $headers
     * @return JsonResponse
     */
    protected function response($data, $statusCode = 200, array $headers = [])
    {
        if ($data instanceof Arrayable && ! $data instanceof JsonSerializable) {
            $data = $data->toArray();
        }

        return new JsonResponse($data, $statusCode, $headers);
    }

    /**
     * Parse data using Data Parser
     *
     * @param  mixed  $data
     * @param  array  $options
     * @param  string  $key
     * @return array|LengthAwarePaginator
     */
    protected function parseData($data, array $options, $key = null)
    {
        $dataParser = new DataParser();
        if ($data instanceof LengthAwarePaginator) {
            $paginationData = $data;
            $data = collect($paginationData->getIterator() ?? $data);
            $parsedData = $dataParser->parseData($data, $options['modes'], $key);
            $paginationData->data = $parsedData;

            return $paginationData;
        }

        return $dataParser->parseData($data, $options['modes'], $key);
    }

    /**
     * Page sort
     *
     * @param  array  $sort
     * @return array
     */
    protected function parseSort(array $sort): array
    {
        return array_map(function ($sort) {
            if (is_string($sort)) {
                $sort = json_decode($sort, true);
            }
            if (! isset($sort['direction'])) {
                $sort['direction'] = 'asc';
            }

            return $sort;
        }, $sort);
    }

    /**
     * Parse include strings into resource and modes
     *
     * @param  array  $includes
     * @return array The parsed resources and their respective modes
     */
    protected function parseIncludes(array $includes): array
    {
        $return = [
            'includes' => [],
            'modes' => [],
        ];

        foreach ($includes as $include) {
            $explode = explode(':', $include);

            if (! isset($explode[1])) {
                $explode[1] = $this->defaults['mode'];
            }

            $return['includes'][] = $explode[0];
            $return['modes'][$explode[0]] = $explode[1];
        }

        return $return;
    }

    /**
     * @param  array  $filter_groups
     * @return array
     */
    protected function parseFilterGroups(array $filter_groups): array
    {
        $return = [];
        foreach ($filter_groups as $group) {
            if (is_string($group)) {
                $group = json_decode($group, true);
            }
            if (! array_key_exists('filters', $group)) {
                throw new InvalidArgumentException('Filter group does not have the \'filters\' key.');
            }

            $filters = array_map(function ($filter) {
                if (! isset($filter['not'])) {
                    $filter['not'] = false;
                }

                return $filter;
            }, $group['filters']);

            $return[] = [
                'filters' => $filters,
                'or' => isset($group['or']) ? $group['or'] : false,
            ];
        }

        return $return;
    }

    /**
     * @param  null  $request
     * @return array
     */
    protected function parseResourceOptions($request = null): array
    {
        if ($request === null) {
            $request = request();
        }

        $this->defaults = array_merge([
            'includes' => [],
            'sort' => [],
            'limit' => null,
            'page' => null,
            'mode' => 'embed',
            'filter_groups' => [],
            'paginate'=> false,
            'as_tree'=> false,
        ], $this->defaults);
        $includes = $this->parseIncludes($request->get('includes', $this->defaults['includes']));
        $sort = $this->parseSort($request->get('sort', $this->defaults['sort']));
        $limit = $request->get('limit', $this->defaults['limit']);
        $page = $request->get('page', $this->defaults['page']);
        $filter_groups = $this->parseFilterGroups($request->get('filter_groups', $this->defaults['filter_groups']));
        $paginate = boolval($request->get('paginate', $this->defaults['paginate']));
        $as_tree = boolval($request->get('as_tree', $this->defaults['as_tree']));

        if ($page !== null && $limit === null) {
            throw new BadRequestHttpException('Cannot use page option without limit option');
        }

        return [
            'includes' => $includes['includes'],
            'modes' => $includes['modes'],
            'sort' => $sort,
            'limit' => $limit,
            'page' => $page,
            'filter_groups' => $filter_groups,
            'paginate'=> $paginate,
            'as_tree'=> $as_tree,
        ];
    }
}
