<?php


Route::resource('Tree', 'Modules\HfcBase\Http\Controllers\TreeController');
Route::get('tree/fulltextSearch', array('as' => 'Tree.fulltextSearch', 'uses' => 'Modules\HfcBase\Http\Controllers\TreeController@fulltextSearch'));

Route::get('Tree/erd/{field}/{search}', array('as' => 'TreeErd.show', 'uses' => 'Modules\HfcBase\Http\Controllers\TreeErdController@show'));
Route::get('Tree/topo/{field}/{search}', array('as' => 'TreeTopo.show', 'uses' => 'Modules\HfcBase\Http\Controllers\TreeTopographyController@show'));


Route::get('Tree/{id}/delete', array('as' => 'Tree.delete', 'uses' => 'Modules\HfcBase\Http\Controllers\TreeController@delete'));
