<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gross',
        'basic',
        'rent',
        'medical',
        'transport',
        'food',
        'other',
        'bonus_rate',
        'bonus',
        'employee_id',
    ];

    /**
     * Get the parent salaryable model (Salary).
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
