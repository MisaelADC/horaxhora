<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTime extends Model
{
    protected $table = 'ReportTime';
    public $timestamps = false;

    public function Production()
    {
        return $this->belongsTo(Production::class, 'IdProduction');
    }
    
}
