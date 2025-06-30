<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'IdUser';
    public $timestamps = true;

    protected $fillable = [
        'name', 'email', 'phonenumber', 'area', 'type', 'image', 'status', 'password', 'remember_token',
    ];

    public function timeWorked()
{
    return $this->hasMany(TimeWorked::class, 'IdUser');
}

}

