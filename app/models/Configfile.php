<?php

namespace Models;

class Configfile extends \Eloquent {

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
            'name' => 'required|unique:configfiles,name,'.$id
        );
    }

	protected $table = 'configfiles';

	// Don't forget to fill this array
	protected $fillable = ['name', 'text', 'device', 'type', 'parent'];


	public function endpoints ()
	{
		return $this->hasMany('Modem');
	}
}