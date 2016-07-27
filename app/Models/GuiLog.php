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

	public static function log_changes($data) {

		$writer = GuiLogWriter::getInstance();
		$writer::log_changes($data);
	}

}


/**
 * This class is used to write log entries to database.
 * Implemented as singleton to avoid duplicate entries of the same change.
 * Implementation following http://www.phptherightway.com/pages/Design-Patterns.html
 *
 * @author Patrick Reichel
 *
 */
class GuiLogWriter {

	// the reference to the singleton object
	private static $instance = null;

	private static $changes_logged = array();

	/**
	 * Constructor.
	 * Declared private to disable creation of GuiLogWriter objects using the “new” keyword
	 *
	 */
	private function __construct() {
	}

	/**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     */
    private function __wakeup()
    {
    }

	/**
	 * Getter for the GuiLogWriter “object“
	 */
	public static function getInstance() {

		if (is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public static function log_changes($data) {

		// if this entry has already be written: do nothing
		if (in_array($data, static::$changes_logged)) {
			return;
		}

		// add creation and updating timestamp
		$datetime = date('c');
		$datetime = strtolower($datetime);
		$datetime = str_replace('t', ' ', $datetime);
		$datetime = explode('+', $datetime)[0];
		$data['created_at'] = $datetime;
		$data['updated_at'] = $datetime;

		// log changes to database
		\DB::table('guilog')->insert($data);

		// store data as saved to prevent multiple entries
		array_push(static::$changes_logged, $data);

	}


}
