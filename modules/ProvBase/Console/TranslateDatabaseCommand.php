<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\DocumentType;

class TranslateDatabaseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:translateDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate certain database entries to given language (call with en (default) or de) and optional database table';

    /**
     * The signature (defining the optional argument)
     */
    protected $signature = 'nms:translateDatabase
                            {lang=en : The language the database entries to translate to.}
                            {table? : The database table to be translated (e.g. if called from migrations.}';

    protected $lang = null;
    protected $table = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @author Patrick Reichel
     *
     * @return mixed
     */
    public function handle()
    {
        $this->lang = $this->argument('lang');
        $this->table = $this->argument('table');

        $this->line('');
        $this->info('Chosen language: '.$this->lang);
        if ($this->table) {
            $this->info('Database table(s) to translate: '.$this->table);
        }
        else {
            $this->info('No table argument given – will translate all tables.');
        }

        if ((! $this->table) || ($this->table == 'documenttype')) {
            $this->_translateDocumenttype();
            $this->line('');
        }
    }

    /**
     * Replaces values in documenttype:type_view by translation of documenttype:type.
     *
     * @author Patrick Reichel
     */
    protected function _translateDocumenttype()
    {
        $this->line('');
        $this->line('Translating entries for DocumentType');
        $this->line('');
        $documenttypes = DocumentType::all();
        foreach ($documenttypes as $documenttype) {
            $type_raw = $documenttype->type;
            $type_view = trans('provbase::view.documentType.viewType.'.$type_raw, [], $this->lang);
            $this->line("Setting value for $type_raw to $type_view");
            $documenttype->type_view = $type_view;
            $documenttype->save();
        }

    }

}
