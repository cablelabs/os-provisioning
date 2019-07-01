<?php

namespace App;

class CsvData extends BaseModel
{
    protected $table = 'csv_data';

    protected $fillable = ['csv_filename', 'csv_header', 'csv_data'];
}
