<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function categories() { return $this->hasMany(Category::class)->orderBy('sort_order'); }
}