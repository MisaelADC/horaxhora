<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'Shifts';
    protected $primaryKey = 'IdShift';
    public $timestamps = false;

    protected $fillable = [
        'StartTime', 'Shift',"EndTime", 'UpdateDate',"Status"
    ];
}
