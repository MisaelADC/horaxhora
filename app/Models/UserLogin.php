<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLogin extends Model
{
    protected $table = 'UserLogins';
    public $timestamps = false;

    protected $fillable = [
        'IdUser', 'LoginAt',
    ];
}
