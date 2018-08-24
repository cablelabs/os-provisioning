<?php

class FulltextIndexMaker
{
    /**
     * include this in your migrations file to build FULLTEXT indexes
     * @author Patrick Reichel
     */

    // array for fields to be in the FULLTEXT index (only types char, string and text!)
    private $index = [];

    /**
     * Constructor.
     * @param $tablename database table to build the index in
     */
    public function __construct($tablename)
    {
        $this->tablename = $tablename;
    }

    /**
     * Mark a column to be added to the index.
     * Call this for every column you want to be FULLTEXT indexed.
     * Attention: if updating an existing index: every column to be indexed have to be given (again)!!
     *
     * @param $col column to be indexed
     */
    public function add($col)
    {
        array_push($this->index, $col);
    }

    /**
     * Create the fulltext index.
     */
    public function make_index()
    {

        // if there alreadỳ is the fulltext index: drop it
        $col_count = DB::affectingStatement('SHOW INDEX FROM '.$this->tablename." WHERE KEY_NAME='".$this->tablename."_fulltext_all'");
        if ($col_count > 0) {
            DB::statement('DROP INDEX '.$this->tablename.'_fulltext_all ON '.$this->tablename);
        }

        // add fulltext index for all given fields
        if (isset($this->index) && (count($this->index) > 0)) {
            DB::statement('CREATE FULLTEXT INDEX '.$this->tablename.'_fulltext_all ON '.$this->tablename.' ('.implode(', ', $this->index).')');
        }
    }

    /**
     * Re-create the fulltext index.
     * As an index can not be altered we have to delete it and build it new. Therefore we again have to give all index fields…
     */
    public function rebuild_index()
    {

        // add fulltext index for all given fields
        if (isset($this->index) && (count($this->index) > 0)) {
            DB::statement('ALTER TABLE '.$this->tablename.' DROP INDEX '.$this->tablename.'_fulltext_all, ADD FULLTEXT '.$this->tablename.'_fulltext_all ('.implode(', ', $this->index).')');
        }
    }
}
