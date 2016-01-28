<?php

class FulltextIndexMaker {

	/**
	 * include this in your migrations file to build FULLTEXT indexes
	 * @author Patrick Reichel
	 */

	// array for fields to be in the FULLTEXT index (only types char, string and text!)
	private $index = array();

	/**
	 * Constructor.
	 * @param $tablename database table to build the index in
	 */
	function __construct($tablename) {
		$this->tablename = $tablename;
	}

	/**
	 * Mark a column to be added to the index.
	 * Call this for every column you want to be FULLTEXT indexed.
	 */
	public function add($col) {
	/* private function __to_index($col) { */
		array_push($this->index, $col);
	}

	/**
	 * Create the fulltext index.
	 */
	public function make_index() {

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_fulltext_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}
	}

}
