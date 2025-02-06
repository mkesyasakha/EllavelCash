<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_items', 'transaction_id', 'item_id')->withPivot('quantity');
    }
}
