<?php

namespace App\Models;

use App\Http\Cache\CacheDesignation;
use App\Http\Cache\CacheSalary;
use App\Http\Cache\CacheUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $appends = [
        'designation_name',
        'salary_info',
        'in_user',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function salary()
    {
        return $this->hasOne(Salary::class);
    }

    /**
     * Get the designation name.
     */
    public function designationName(): Attribute
    {
        $designation_id = $this->designation_id;

        return Attribute::get(fn () => CacheDesignation::find($designation_id)?->name);
    }

    /**
     * Get the salary name.
     */
    public function salaryInfo(): Attribute
    {
        $employee_id = $this->id ?? null;

        return Attribute::get(fn () => CacheSalary::find($employee_id, 'employee_id'));
    }

    public function inUser(): Attribute
    {
        $users = CacheUser::get()->map(fn ($user) => $user->email ? strtolower($user->email) : '')->toArray();

        return Attribute::get(fn () => $this->email ? in_array(strtolower($this->email), $users) : false);
    }
}
