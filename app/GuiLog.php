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

namespace App;

class GuiLog extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'guilog';

    public $index_delete_disabled = false;

    // Name of View
    public static function view_headline()
    {
        return 'Logs';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-history"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return [
            'table' => $this->table,
            'index_header' => ['id', $this->table.'.created_at', $this->table.'.username', $this->table.'.method', $this->table.'.model', $this->table.'.model_id', $this->table.'.text'],
            'bsclass' => $this->get_bsclass(),
            'header' => $this->id.' - '.$this->mac.($this->name ? ' - '.$this->name : ''),
            'edit'	=> ['model_id' => 'generate_model_link'],
            'order_by' => ['0' => 'desc'],
            'raw_columns' => ['model_id'],
        ];
    }

    public function get_bsclass()
    {
        $bsclass = 'info';

        if ($this->method == 'created') {
            $bsclass = 'success';
        }
        if ($this->method == 'deleted') {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    public function generate_model_link()
    {
        // if there is a route to a changed model: create hyperlink to the edit blade
        // goal: easier to track changes
        if (\Route::has($this->model.'.edit')) {
            $model_link = '<a href="'.\URL::route($this->model.'.edit', [$this->model_id]).'" target="_blank">'.$this->model_id.'</a>';
        }
        // if there is no route (e.g. CccUser): show only ID
        else {
            $model_link = $this->model_id;
        }

        return $model_link;
    }

    /**
     * Delete all LogEntries older than a specific timespan - default 4 years
     * Hard Delete all Entries older than 6 years
     */
    public static function cleanup($months = 48)
    {
        \Log::notice('GuiLog: Execute cleanup() - Delete Log entries older than '.$months.' months - (hard delete older than '.(($months + 24) / 12).' years)');

        $softDeleteDate = \Carbon\Carbon::now()->subMonths($months);
        $hardDeleteDate = $softDeleteDate->subMonths(24);

        self::where('created_at', '<', $softDeleteDate->toDateString())->delete();
        self::where('created_at', '<', $hardDeleteDate->toDateString())->forceDelete();
    }

    public static function log_changes($data)
    {
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
 */
class GuiLogWriter
{
    // the reference to the singleton object
    private static $instance = null;

    private static $changes_logged = [];

    /**
     * Constructor.
     * Declared private to disable creation of GuiLogWriter objects using the “new” keyword
     */
    private function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     */
    private function __clone()
    {
    }

    /**
     * Public unserialize method to prevent unserializing of the *Singleton*
     * instance.
     */
    public function __wakeup()
    {
    }

    /**
     * Getter for the GuiLogWriter “object“
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Check if the current data has to be logged.
     * As the log message for ONE CHANGE can arrive multiple times we have to check if the change has been
     * already logged. Also we have to check if the data has been CHANGED multiple times – then of course
     * we have to log it more than once.
     *
     * @author Patrick Reichel
     *
     * @return true if we have to log, false else
     */
    protected static function _have_to_log($data)
    {

        // work on a copy for easier use (e.g. array_pop)
        $changes_logged = static::$changes_logged;

        // if there is no log entry: log
        if (! $changes_logged) {
            return true;
        }

        // search backwards through the stack and
        // get the latest log message for this model-ID-method
        while ($entry = array_pop($changes_logged)) {
            if (
                ($entry['model'] == $data['model']) &&
                ($entry['model_id'] == $data['model_id']) &&
                ($entry['method'] == $data['method'])
            ) {
                // see if the changes are the same again
                if ($entry['text'] == $data['text']) {
                    // the latest entry is the same as the current: do nothing
                    return false;
                } else {
                    // multiple changes detected: log
                    return true;
                }
            }
        }

        // nothing found: log
        return true;
    }

    /**
     * This makes the log entry.
     *
     * @author Patrick Reichel
     */
    public static function log_changes($data)
    {

        // if we have already logged this event: do nothing
        if (! self::_have_to_log($data)) {
            return;
        }

        // generate creation and updating timestamp
        $datetime = date('c');
        $datetime = strtolower($datetime);
        $datetime = str_replace('t', ' ', $datetime);
        $datetime = explode('+', $datetime)[0];
        // add to logging; we work with a copy because in case of multiple entries
        // we check for equality – but without timestamps!
        $log_entry = $data;
        $log_entry['created_at'] = $datetime;
        $log_entry['updated_at'] = $datetime;

        // log changes to database
        \DB::table('guilog')->insert($log_entry);

        // store data as saved to prevent multiple entries for the same event
        array_push(static::$changes_logged, $data);
    }
}
