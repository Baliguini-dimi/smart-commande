<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price_monthly', 'max_tables', 'max_menus', 'features', 'is_active'];
    protected $casts    = ['features' => 'array', 'is_active' => 'boolean'];

    public function restaurants() { return $this->hasMany(Restaurant::class); }
}