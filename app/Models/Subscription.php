<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'restaurant_id', 'plan_id', 'starts_at',
        'expires_at', 'status', 'payment_ref', 'amount_paid'
    ];
    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function plan()       { return $this->belongsTo(Plan::class); }
}