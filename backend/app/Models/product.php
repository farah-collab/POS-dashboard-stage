<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    //use HasFactory;//c est pour crrer des fake data avec les factories

    protected $fillable=[
        'name',
        'category_id',
        'price',
        'stock_quantity',
        'image',
        'qr_code',
        
    ];


    public function category(){
        return $this->belongsTo(Category::class);
    }//un produit appartient a une categories we hetha bel category_id fel table product

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
