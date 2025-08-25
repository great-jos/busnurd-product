<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'image', 'description', 'user_id'];

    protected static function booted(){
        static::addGlobalScope('user_product', function (Builder $builder) {
            if(auth()->check()){
                $builder->where('user_id', auth()->user()->id);
            }
        });
    }
}
