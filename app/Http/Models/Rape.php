<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rape extends Product
{
    use SoftDeletes;
   	protected $guarded = ['id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];
    // protected $dates = ['deleted_at'];


    public function scopeFilter($query, $params) {
        if(isset($params['type']) && $params['type'] !== 'all') {
            $query->where('type', $params['type']);
        }

    	switch ($params['sort']) {
		    case 'price-descendent':
        		$query->orderBy('price_pp', 'desc');
		        break;
		    case 'price-ascendent':
        		$query->orderBy('price_pp');
		        break;
		    default:
        		$query->orderBy('created_at');
		        break;
		}

        return $query;
    }
}
