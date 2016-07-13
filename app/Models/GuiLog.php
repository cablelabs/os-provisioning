<?php

namespace App;

class GuiLog extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'guilog';


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			// 'mail' => 'email',
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'Logs';
	}

	// link title in index view
	public function view_index_label()
	{
        $bsclass = 'info';

        if ($this->method == 'created')
            $bsclass = 'success';
        if ($this->method == 'deleted')
            $bsclass = 'danger';

        return ['index' => [$this->created_at, $this->username, $this->method, $this->model, $this->model_id],
                'index_header' => ['Time', 'User', 'Action', 'Model', 'ID'],
                'bsclass' => $bsclass,
                'header' => $this->username.': '.$this->method.' '.$this->model];
	}


	public function index_list()
	{
		return $this->orderBy('id', 'desc')->get();
	}


	/**
	 * Delete all LogEntries older than a specific timespan - default 3 months
	 * Hard Delete all Entries older than 6 months
	 */
	public static function cleanup($days = 90)
	{
		\Log::notice('GuiLog: Execute cleanup() - Delete Log entries older than '.$days.' days - (hard delete older than 180 days)');

		GuiLog::where('created_at', '<', \DB::raw('DATE_SUB(NOW(), INTERVAL '.$days.' DAY)'))->delete();
		GuiLog::where('created_at', '<', \DB::raw('DATE_SUB(NOW(), INTERVAL 180 DAY)'))->forceDelete();
		// GuiLog::where('created_at', '<', \DB::raw('DATE_SUB(NOW(), INTERVAL 3 MINUTE)'))->delete();
	}

}