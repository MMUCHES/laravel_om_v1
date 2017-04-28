<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    	'name', 'description', 'image'
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function details()
    {
    	return $this->hasMany(ProductDetail::class);
    }

    public function explains()
    {
    	return $this->hasMany(ProductExplain::class);
    }
    

    public static function form()
    {
        return [
            'name' => '',
            'image' => '',
            'description' => '',
            'details' => [
                ProductDetail::form()
            ],
            'explains' => [
                ProductExplain::form(),
                ProductExplain::form()
            ]
        ];
    }
}
