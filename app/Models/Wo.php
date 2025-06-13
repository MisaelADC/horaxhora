<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wo extends Model
{
    protected $table = 'Wo';
    protected $primaryKey = 'IdWo';
    public $timestamps = false;

    protected $fillable = [
        'Wo',"IdProduct", "Design"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'IdProduct', 'IdProduct');
    }

    public function design()
    {
        return $this->belongsTo(Design::class, "Design",'IdDesign');
    }

    public function TotalReal()
    {
        return $this->hasOne(TotalReal::class, 'IdWo');
    }
}
