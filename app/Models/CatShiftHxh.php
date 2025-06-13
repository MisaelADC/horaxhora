<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatShiftHxh extends Model
{
    protected $table = 'CatShiftHxh';
    protected $primaryKey = 'IdShiftHxh';
    public $timestamps = false;

    protected $fillable = [
        'HStart', 'HEnd', 'Meta',"Real","Scrap","IdProduction","IdTimeOut"
    ];
}
