<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportEmployee extends Model
{
    protected $table = 'ReportEmployee';
    protected $primaryKey = 'IdReporte';
    public $timestamps = false;


    public function Production()
    {
        return $this->belongsTo(Production::class, 'IdProduction');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'IdUser', 'IdUser');
    }
    
}
