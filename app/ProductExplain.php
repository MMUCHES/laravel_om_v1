<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductExplain extends Model
{
    protected $fillable = [
    	'description'
    ];

    public $timestamps = false;

    public static function form()
    {
    	return [
    		'description' => ''
    	];
    }
}
