<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description',
        'price', 'image', 'allergens', 'is_available', 'sort_order'
    ];
    protected $casts = [
        'allergens'    => 'array',
        'is_available' => 'boolean',
        'price'        => 'decimal:0',
    ];

    public function category()   { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}