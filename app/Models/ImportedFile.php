<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedFile extends Model
{
    protected $table = 'import_csv_basic_info';

    protected $fillable = ['csv_name'];
}
