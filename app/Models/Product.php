<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'Products';
    protected $primaryKey = 'IdProduct';
    public $timestamps = false;

    protected $fillable = [
        'ItemCode', 'Description',"Quantity", 'UpdateDate',"Status"
    ];

    public function design()
    {
        return $this->hasMany(Design::class, 'IdDesign');
    }
}
