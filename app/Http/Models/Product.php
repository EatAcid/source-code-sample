<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $model;
    public $namespace;

    /**
     * Create product with all needed methods, has variable $this->model with class of the specified product
     * find by namespace and it's id. If is set parameter slug, it's found by slug instead of id
     **/
    public function __construct($namespace, $id, $slug = NULL) {
    	$model_name = 'App\\' . $namespace;
    	if(!class_exists($model_name)) 
            return;
        if($slug)
			$product = $model_name::whereRaw("BINARY `slug`= ?",[$slug])->first();
    	else
    		$product= $model_name::find($id);
		
		if($product === null) 
    		return;
		
    	$this->namespace = $namespace;
        $this->model = $product;
    }

    /**
     * Return full url path to product detail. 
     **/
    public function getProductUrl() {
        // switch ($this->namespace) {
        //     case 'Pipe':
        //         $pathNm = 'foukacky';
        //         break;
        //     default:
        //         $pathNm = lcfirst($this->namespace);
        //         break;
        // }
        $pathNm = $this->namespace === 'Pipe' ? 'foukacky' : lcfirst($this->namespace);
        return env('APP_URL').$pathNm.'/'.$this->model->slug;
    }

    /**
     * Return namespace of the product in translate
     **/
    public function getProductName() {
        switch ($this->namespace) {
            case 'Rape':
                $name = 'rapé';
                break;
            case 'Pipe':
                $name = 'foukačka';
                break;
            default:
                $name = lcfirst($this->namespace);
                break;
        }
        return $name;
    }

    /**
     * Return collection of the ratings of this product item 
     **/
    public function ratings() {
        $ratings = Rating::where('model_name', $this->namespace)->where('product_id', $this->model->id)->orderBy('created_at', 'desc')->get();
        foreach ($ratings as $key => $rating) {
            $ratings[$key]->nick_name = User::find($rating->user_id)->nick_name;
            $ratings[$key]->date = $ratings[$key]->created_at->format('d.m.Y');
            $ratings[$key]->date_comment = $ratings[$key]->commented_at ? $ratings[$key]->commented_at->format('d.m.Y') : '';
            $ratings[$key]->name = $ratings[$key]->name();
            // $ratings[$key]->stars = $ratings[$key]->stars();
        }
        return $ratings;
    }


    /**
     * Decrease product quantity from the stock. Can set stock sum to negative number. Return 1 if OK, return 0 if stock sum is negative.
     **/
    public function takeFromStock($quantity) {
        // // $this->model->fresh();
        // if($this->model->order_max >= $quantity) {
        //     $this->model->decrement('order_max', $quantity);
        //     return 1;
        // } else {
        //     return 0;
        // }
        $product = $this->model->fresh();                      // take fresh data (deadlock shitty solved)
        $product->decrement('order_max', $quantity);
        return $product->order_max >= 0 ? 1 : 0;
    }

    /**
     * Increase quantity of the product to the stock.
     **/
    public function addToStock($quantity) {
        // $this->model->fresh();
        // $product->update(['order_max' => $this->model->order_max + $quantity]);
        $product = $this->model->fresh();         
        $product->increment('order_max', $quantity);
    }

    /**
     * Return pictures of the product
     **/
    public function pictures() {
        // if empty, return generic picture????
        return Picture::where('model_name', $this->namespace)->where('product_id', $this->model->id)->orderBy('order')->get();
    }

    /**
     * Return path of the main picture of the product
     **/
    public function mainPicture() {
        // if empty, return generic picture????
        return Picture::where('model_name', $this->namespace)->where('product_id', $this->model->id)->orderBy('order')->first()->full_path;
    }





}
