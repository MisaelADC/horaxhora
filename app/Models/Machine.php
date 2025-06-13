<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'Machines';
    protected $primaryKey = 'IdMachine';
    public $timestamps = false;

    protected $fillable = [
        'MachineCode', 'Description', 'UpdateDate',"Status"
    ];



    public function timeWorked()
    {
        return $this->hasMany(TimeWorked::class, 'IdMachine');
    }
    

}
