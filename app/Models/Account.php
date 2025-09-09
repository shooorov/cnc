<?php

namespace App\Models;

use App\Traits\RemarkTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use RemarkTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'opening_date' => 'datetime',
    ];

    /**
     * The accessors to append to the Account's array form.
     *
     * @var array
     */
    protected $appends = [
        'balance',
        'extended_name',
        'opening_check',
    ];

    /**
     * Get the balance.
     *
     * @return string
     */
    public function getBalanceAttribute()
    {
        return 0;
    }

    /**
     * Get the opening check.
     *
     * @return string
     */
    public function getOpeningCheckAttribute()
    {
        return ($this->opening_balance > 0 && $this->opening_date) ? true : false;
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function other_sales()
    {
        return $this->hasMany(OtherSale::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the item account_extended_name.
     */
    public function extendedName(): Attribute
    {
        return Attribute::get(fn () => $this->name.($this->branch ? ' - '.$this->branch : ''));
    }
}
