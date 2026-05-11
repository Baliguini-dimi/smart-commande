<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function view(User $user, Menu $menu): bool
    {
        return $user->restaurant?->id === $menu->restaurant_id;
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->restaurant?->id === $menu->restaurant_id;
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->restaurant?->id === $menu->restaurant_id;
    }
}