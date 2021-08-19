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

return [
    /*
     * DataTables search options.
     */
    'search' => [
        /*
         * Smart search will enclose search keyword with wildcard string "%keyword%".
         * SQL: column LIKE "%keyword%"
         *
         * NOTE: With Datatables v9.18.0 Datatables adds two % symbols to search string in
         * DataTableAbstract::setupKeyword() and QueryDataTable::prepareKeyword()
         * This actually should be resolved from v6.16.1 - see https://github.com/yajra/laravel-datatables/issues/665
         */
        'smart' => true,

        /*
         * Multi-term search will explode search keyword using spaces resulting into multiple term search.
         */
        'multi_term' => true,

        /*
         * Case insensitive will search the keyword in lower case format.
         * SQL: LOWER(column) LIKE LOWER(keyword)
         */
        'case_insensitive' => true,

        /*
         * Wild card will add "%" in between every characters of the keyword.
         * SQL: column LIKE "%k%e%y%w%o%r%d%"
         */
        'use_wildcards' => false,
    ],

    /*
     * DataTables internal index id response column name.
     */
    'index_column' => 'DT_RowIndex',

    /*
     * List of available builders for DataTables.
     * This is where you can register your custom dataTables builder.
     */
    'engines' => [
        'eloquent'                    => \Yajra\DataTables\EloquentDataTable::class,
        'query'                       => \Yajra\DataTables\QueryDataTable::class,
        'collection'                  => \Yajra\DataTables\CollectionDataTable::class,
        'resource'                    => \Yajra\DataTables\ApiResourceDataTable::class,
    ],

    /*
     * DataTables accepted builder to engine mapping.
     * This is where you can override which engine a builder should use
     * Note, only change this if you know what you are doing!
     */
    'builders' => [
        //Illuminate\Database\Eloquent\Relations\Relation::class => 'eloquent',
        //Illuminate\Database\Eloquent\Builder::class            => 'eloquent',
        //Illuminate\Database\Query\Builder::class               => 'query',
        //Illuminate\Support\Collection::class                   => 'collection',
    ],

    /*
     * Nulls last sql pattern for Posgresql & Oracle.
     * For MySQL, use '-%s %s'
     */
    'nulls_last_sql' => '%s %s NULLS LAST',

    /*
     * User friendly message to be displayed on user if error occurs.
     * Possible values:
     * null             - The exception message will be used on error response.
     * 'throw'          - Throws a \Yajra\DataTables\Exceptions\Exception. Use your custom error handler if needed.
     * 'custom message' - Any friendly message to be displayed to the user. You can also use translation key.
     */
    'error' => env('DATATABLES_ERROR', null),

    /*
     * Default columns definition of dataTable utility functions.
     */
    'columns' => [
        /*
         * List of columns hidden/removed on json response.
         */
        'excess' => ['rn', 'row_num'],

        /*
         * List of columns to be escaped. If set to *, all columns are escape.
         * Note: You can set the value to empty array to disable XSS protection.
         */
        'escape' => '*',

        /*
         * List of columns that are allowed to display html content.
         * Note: Adding columns to list will make us available to XSS attacks.
         */
        'raw' => ['action'],

        /*
         * List of columns are are forbidden from being searched/sorted.
         */
        'blacklist' => ['password', 'remember_token'],

        /*
         * List of columns that are only allowed fo search/sort.
         * If set to *, all columns are allowed.
         */
        'whitelist' => '*',
    ],

    /*
     * JsonResponse header and options config.
     */
    'json' => [
        'header'  => [],
        'options' => 0,
    ],

    /**
     * This defines the threshhold from which we talk about a large dataset.
     * Index tables of large datasets are handled a bit different to not dramatically decrease performance.
     * This currently affects only sorting. As sorting decreases the performance the most, sorting is removed
     * when the user initially opens the index page and no filter is set. Everytime a/the filter is changed the
     * sorting (order by) is initially removed
     */
    'hugeTableThreshhold' => env('DATATABLES_HUGE_TABLE_THRESHHOLD', 500000),

    /**
     * For certain
     */
    'relationThreshhold' => env('DATATABLES_RELATION_THRESHHOLD', 25),
];
