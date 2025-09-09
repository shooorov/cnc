<?php

namespace App\Models;

use App\Http\Cache\CacheAccount;
use App\Http\Cache\CacheUser;
use App\Traits\RemarkTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use RemarkTrait, SoftDeletes;

    protected $fillable = [
        'date',
        'check_no',
        'direction',
        'amount',
        'account_id',
        'creator_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * The accessors to append to the Project's array form.
     *
     * @var array
     */
    protected $appends = [
        'account_name',
        'creator_name',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the item account_name.
     */
    public function accountName(): Attribute
    {
        $account_id = $this->account_id;

        return Attribute::get(fn () => CacheAccount::find($account_id)?->extended_name);
    }

    /**
     * Get the item creator_name.
     */
    public function creatorName(): Attribute
    {
        $creator_id = $this->creator_id;

        return Attribute::get(fn () => CacheUser::find($creator_id)?->name);
    }
}
