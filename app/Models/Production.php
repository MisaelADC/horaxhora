<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $table = 'Production';
    protected $primaryKey = 'IdProduction';
    public $timestamps = false;

    protected $fillable = [
        'Date', 'Wo', 'Meta',"Real","Scrap","RawMaterial","BatchNumber",
        "WeightKg","IdWo","IdMachine","IdShift","IdUser"
    ];

    public function wo()
    {
        return $this->belongsTo(Wo::class, 'IdWo', 'IdWo');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'IdShift', 'IdShift');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'IdMachine', 'IdMachine');
    }

    public function WorkTimeView()
    {
        return $this->hasOne(WorkTimeView::class, 'IdProduction');
    }

    public function ReportEmployee()
    {
        return $this->hasOne(ReportEmployee::class, 'IdProduction', 'IdProduction');
    } 
    public function ReportEmployees()
    {
          return $this->hasMany(\App\Models\ReportEmployee::class, 'IdProduction', 'IdProduction');
    }
    public function TimeOutView()
    {
        return $this->hasOne(TimeOutView::class, 'IdProduction');
    }
    public function timeouts()
    {
        return $this->hasMany(\App\Models\CatTimeOut::class, 'IdProduction', 'IdProduction');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'IdUser', 'IdUser');
    }
    public function downtimeReason()
    {
        return $this->belongsTo(DowntimeReason::class, 'IdDowntime', 'IdDowntime');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'IdProduct', 'IdProduct');
    }

}
