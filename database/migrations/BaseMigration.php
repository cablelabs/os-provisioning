<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BaseMigration extends Migration
{
    function __construct() {

		echo "Migrating ".get_class($this)."\n";
        // get and instanciate of index maker
        require_once(getcwd()."/app/extensions/database/FulltextIndexMaker.php");
        $this->fim = new FulltextIndexMaker($this->tablename);
    }

    public function up_table_generic (&$table)
    {
        $table->increments('id');
        //$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
        $table->timestamps();
        $table->softDeletes();
    }

    public function up()
    {

    }

    protected function set_auto_increment ($i)
    {
        DB::update("ALTER TABLE ".$this->tablename." AUTO_INCREMENT = $i;");
    }

	/**
	 * All columnes to be fulltext indexed.
	 * Attention on updating an existing index: The index will be dropped and re-created – therefore you have to give ALL columns (again)!!
	 */
    protected function set_fim_fields($fields)
    {
        foreach ($fields as $field)
            $this->fim->add($field);

        // create FULLTEXT index including the given
        $this->fim->make_index();
    }
}
