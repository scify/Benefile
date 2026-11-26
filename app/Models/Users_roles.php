<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users_roles extends Model
{
    protected $table = 'users_roles';
    protected $primaryKey = 'id';
    protected $fillable = ['role'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(\App\Models\User::class);
    }
}
