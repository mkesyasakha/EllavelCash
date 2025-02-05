<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];

    public function customers(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
