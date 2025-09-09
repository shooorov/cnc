<?php

namespace App\Models;

use App\Http\Cache\CacheAccount;
use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheOtherSaleCategory;
use App\Http\Cache\CacheTransaction;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherSale extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'amount',
        'account_id',
        'transaction_id',
        'other_sale_category_id',
        'branch_id',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'check_no',
        'account_name',
        'branch_name',
        'category_name',
        'datetime_format',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UseBranchScope);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function other_sale_category()
    {
        return $this->belongsTo(OtherSaleCategory::class);
    }

    /**
     * Get the item check_no.
     */
    public function checkNo(): Attribute
    {
        $transaction_id = $this->transaction_id;

        return Attribute::get(fn () => CacheTransaction::find($transaction_id)?->check_no);
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
     * Get the item branch_name.
     */
    public function branchName(): Attribute
    {
        $branch_id = $this->branch_id;

        return Attribute::get(fn () => CacheBranch::find($branch_id)?->name);
    }

    /**
     * Get the item category_name.
     */
    public function categoryName(): Attribute
    {
        $category_id = $this->other_sale_category_id;

        return Attribute::get(fn () => CacheOtherSaleCategory::find($category_id)?->name);
    }

    /**
     * Get the datetime format.
     */
    public function datetimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('d/m/Y h:i A'));
    }
}
