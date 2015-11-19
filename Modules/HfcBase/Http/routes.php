<?php


Route::resource('Tree', 'Modules\HfcBase\Http\Controllers\TreeController');
Route::get('tree/fulltextSearch', array('as' => 'Tree.fulltextSearch', 'uses' => 'Modules\HfcBase\Http\Controllers\TreeController@fulltextSearch'));
