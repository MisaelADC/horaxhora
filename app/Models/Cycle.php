<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    protected $table = 'Cycles';
    protected $primaryKey = 'IdCycle';
    public $timestamps = false;

    protected $fillable = [
        'duration', 'IdMachine', 'IdProduct',"UpdateDate"
    ];
}
