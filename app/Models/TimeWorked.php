<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeWorked extends Model
{
    protected $table = 'TimeWorked';
    protected $primaryKey = 'IdTimeWorked';
    public $timestamps = false;

    protected $fillable = [
        'HStart', 'HEnd', "IdUser", "IdMachine"
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'IdUser');
}

public function machine()
{
    return $this->belongsTo(Machine::class, 'IdMachine');
}


    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'IdUser', 'IdUser');
    // }
}

