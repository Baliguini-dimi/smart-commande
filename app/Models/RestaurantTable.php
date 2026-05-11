<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['restaurant_id', 'number', 'zone', 'qr_code_path', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function orders()     { return $this->hasMany(Order::class); }
}