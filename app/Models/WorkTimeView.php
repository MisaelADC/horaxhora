<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimeView extends Model
{
    protected $table = 'WorkTimeView';
    public $timestamps = false;

    public function Production()
    {
        return $this->belongsTo(Production::class, 'IdProduction');
    }
    
}
