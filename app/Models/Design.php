<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $table = 'Designs';
    protected $primaryKey = 'IdDesign';
    public $timestamps = false;

    protected $fillable = [
        'IdDesign', 'IdProduct',"Image", 'Status'
    ];

    public function producto()
    {
        return $this->belongsTo(Product::class, 'IdProduct');
    }

    public function wo()
    {
        return $this->hasMany(Wo::class, 'IdWo');
    }
}
