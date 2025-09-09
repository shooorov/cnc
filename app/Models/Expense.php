<?php

namespace App\Models;

use App\Http\Cache\CacheAccount;
use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheExpenseCategory;
use App\Http\Cache\CacheRemark;
use App\Http\Cache\CacheTransaction;
use App\Models\Scopes\UseBranchScope;
use App\Traits\RemarkTrait;
use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use RemarkTrait, SoftDeletes, StatusTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'check_no',
        'branch_name',
        'account_name',
        'category_name',
        'datetime_format',
        'remark',
        'created_at_format',
    ];

    public $statuses = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
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
     * Get the item branch_name.
     */
    public function branchName(): Attribute
    {
        $branch_id = $this->branch_id;

        return Attribute::get(fn () => CacheBranch::find($branch_id)?->name);
    }

    /**
     * Get the item account_name.
     */
    public function accountName(): Attribute
    {
        $account_id = $this->account_id;

        return Attribute::get(fn () => CacheAccount::find($account_id)?->name);
    }

    /**
     * Get the item category_name.
     */
    public function categoryName(): Attribute
    {
        $category_id = $this->expense_category_id;

        return Attribute::get(fn () => CacheExpenseCategory::find($category_id)?->name);
    }

    /**
     * Get the datetime format.
     */
    public function datetimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('d/m/Y'));
    }

    /**
     * Get the datetime format.
     */
    public function createdAtFormat(): Attribute
    {
        return Attribute::get(fn () => $this->created_at->format('d/m/Y h:i A'));
    }

    /**
     * Get the remark.
     */
    public function remark(): Attribute
    {
        $expense_id = $this->id;
        $remark = CacheRemark::get()->first(fn ($i) => $i->remarkable_type == 'App\Models\Expense' && $i->remarkable_id == $expense_id);

        return Attribute::get(fn () => $remark?->remark);
    }
}
