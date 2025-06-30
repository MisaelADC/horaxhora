<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeOutView extends Model
{
    protected $table = 'TimeOutView';
    public $timestamps = false;

    public function Production()
    {
        return $this->belongsTo(Production::class, 'IdProduction');
    }
        
}
