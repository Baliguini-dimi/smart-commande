<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Dish;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('gerant.restaurant.edit')
                   ->with('error', 'Créez d\'abord votre restaurant.');
        }

        $menus = Menu::where('restaurant_id', $restaurant->id)
            ->with(['categories.dishes'])
            ->latest()
            ->get();

        return view('gerant.menus.index', compact('menus', 'restaurant'));
    }

    public function create()
    {
        return view('gerant.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ], ['name.required' => 'Le nom du menu est obligatoire.']);

        Menu::create([
            'restaurant_id' => auth()->user()->restaurant->id,
            'name'          => $request->name,
            'description'   => $request->description,
            'is_active'     => true,
        ]);

        return redirect()->route('gerant.menus.index')
               ->with('success', 'Menu créé avec succès.');
    }

    public function show(Menu $menu)
    {
        $this->authorize('view', $menu);
        $menu->load(['categories.dishes']);
        return view('gerant.menus.show', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);
        $menu->update(['is_active' => !$menu->is_active]);
        $status = $menu->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Menu $status.");
    }

    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);
        $menu->delete();
        return redirect()->route('gerant.menus.index')
               ->with('success', 'Menu supprimé.');
    }

    public function storeCategory(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);
        $request->validate(['name' => 'required|string|max:100']);

        Category::create([
            'menu_id'    => $menu->id,
            'name'       => $request->name,
            'icon'       => $request->icon ?? 'tag',
            'sort_order' => Category::where('menu_id', $menu->id)->count(),
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function destroyCategory(Category $category)
    {
        $this->authorize('update', $category->menu);
        $category->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }

    public function storeDish(Request $request, Category $category)
    {
        $this->authorize('update', $category->menu);

        $request->validate([
            'name'  => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = $this->fileUpload->uploadImage($request->file('image'), 'dishes');
            } catch (\Exception $e) {
                return back()->withErrors(['image' => $e->getMessage()]);
            }
        }

        Dish::create([
            'category_id'  => $category->id,
            'name'         => $request->name,
            'description'  => $request->description,
            'price'        => $request->price,
            'image'        => $imagePath,
            'is_available' => true,
            'sort_order'   => Dish::where('category_id', $category->id)->count(),
        ]);

        return back()->with('success', 'Plat ajouté.');
    }

    public function toggleDish(Dish $dish)
    {
        $this->authorize('update', $dish->category->menu);
        $dish->update(['is_available' => !$dish->is_available]);
        $status = $dish->is_available ? 'disponible' : 'indisponible';
        return back()->with('success', "Plat marqué $status.");
    }

    public function destroyDish(Dish $dish)
    {
        $this->authorize('update', $dish->category->menu);
        if ($dish->image) $this->fileUpload->delete($dish->image);
        $dish->delete();
        return back()->with('success', 'Plat supprimé.');
    }
}