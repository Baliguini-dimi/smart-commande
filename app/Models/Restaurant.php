<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'name', 'slug', 'logo',
        'address', 'phone', 'description',
        'primary_color', 'is_active', 'subscription_expires_at',
    ];
    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'is_active'               => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($r) {
            if (empty($r->slug)) {
                $r->slug = Str::slug($r->name) . '-' . Str::random(5);
            }
        });
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function plan()         { return $this->belongsTo(Plan::class); }
    public function menus()        { return $this->hasMany(Menu::class); }
    public function tables()       { return $this->hasMany(RestaurantTable::class); }
    public function orders()       { return $this->hasMany(Order::class); }
    public function subscriptions(){ return $this->hasMany(Subscription::class); }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }
}