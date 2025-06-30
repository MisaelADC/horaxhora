<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalReal extends Model
{
    protected $table = 'TotalReal';
    public $timestamps = false;


    public function Wo()
    {
        return $this->belongsTo(Wo::class, 'IdWo');
    }
    
}
