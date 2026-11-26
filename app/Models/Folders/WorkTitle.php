<?php

namespace App\Models\Folders;

use Illuminate\Database\Eloquent\Model;

/**
 * A work title. The basic information form adds the titles it does not find.
 */
class WorkTitle extends Model
{
    protected $table = 'work_title_list_lookup';

    /** @var list<string> */
    protected $fillable = ['work_title'];
}
