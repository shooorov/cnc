<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheBranchAccess;
use App\Http\Cache\CacheRole;
use App\Traits\ImageTrait;
use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, StatusTrait, ImageTrait;

	public function __construct()
    {
		$this->default_status = 'active';
		$this->default_image = '/img/avatar.jpg';
		$this->directory = 'users';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'name',
        'email',
		'mobile',
		'address',
		'image',
        'password',
        'is_admin',
        'is_waiter',
        'is_chef',
        'is_barista',
        'is_rider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'access' => 'array',
        'is_admin' => 'boolean',
        'is_waiter' => 'boolean',
        'is_barista' => 'boolean',
        'is_chef' => 'boolean',
        'is_rider' => 'boolean',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'type',
        'types',
        'is_cook',
        'role_name',
        'branch_name',
        'default_image_url',
        'image_url',
        // 'statuses',
        // 'image_default',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public $types = [
        'waiter' => 'Waiter',
        'barista' => 'Barista',
        'chef' => 'Chef',
        'rider' => 'Rider',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the is_cook.
     */
    public function isCook(): Attribute
    {
        return Attribute::get(fn () => $this->is_chef || $this->is_barista);
    }

    /**
     * Get the role_name.
     */
    public function roleName(): Attribute
    {
        $role_id = $this->role_id;
        $role_name = CacheRole::find($role_id)?->name;

        return Attribute::get(fn () => empty($this->type) ? strtolower($role_name) : $this->type);
    }

    /**
     * Get the branch_name.
     */
    public function branchName(): Attribute
    {
        $branch_ids = CacheBranchAccess::get()->where('user_id', $this->id)->where('is_checked', true)->pluck('branch_id');
        $name = CacheBranch::get()->whereIn('id', $branch_ids)->pluck('name')->join(', ');

        return Attribute::get(fn () => $name);
    }

    /**
     * Get the image_default.
     */
    public function imageDefault(): Attribute
    {
        return Attribute::get(fn () => asset('img/avatar.jpg'));
    }

    /**
     * Get the User's types.
     */
    protected function types(): Attribute
    {
        return Attribute::get(fn () => $this->types);
    }

    /**
     * Get the type.
     */
    public function type(): Attribute
    {
        $type = '';

		if($this->is_admin) {
			$type = 'super admin';
		} else if($this->is_waiter) {
			$type = 'waiter';
		} else if($this->is_barista) {
			$type = 'barista';
		} else if($this->is_chef) {
			$type = 'chef';
		} else if($this->is_rider) {
			$type = 'rider';
		}

		return Attribute::get(fn () => $type);
    }

}
