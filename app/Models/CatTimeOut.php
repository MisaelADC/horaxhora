<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTimeOut extends Model
{
    protected $table = 'CatTimeOut';
    protected $primaryKey = 'IdTimeOut';
    public $timestamps = false;

    protected $fillable = [
        'StartTime', 'EndTime','IdUser',"IdProduction"
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'IdProduction', 'IdProduction');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'IdUser', 'IdUser');
    }

    public function downtimeReason()
    {
        return $this->belongsTo(DowntimeReason::class, 'IdDowntimeReason', 'IdDowntimeReason');
    }
}
