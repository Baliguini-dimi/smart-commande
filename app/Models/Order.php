<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'restaurant_id', 'restaurant_table_id', 'status',
        'total_amount', 'payment_method', 'payment_status', 'client_note'
    ];

    public function restaurant()      { return $this->belongsTo(Restaurant::class); }
    public function restaurantTable() { return $this->belongsTo(RestaurantTable::class); }
    public function orderItems()      { return $this->hasMany(OrderItem::class); }
    public function payment()         { return $this->hasOne(Payment::class); }
}