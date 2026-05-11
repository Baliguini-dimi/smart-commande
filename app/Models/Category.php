<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['menu_id', 'name', 'icon', 'sort_order'];

    public function menu()   { return $this->belongsTo(Menu::class); }
    public function dishes() { return $this->hasMany(Dish::class)->orderBy('sort_order'); }
}