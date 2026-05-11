<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function edit()
    {
        $restaurant = auth()->user()->restaurant;
        return view('gerant.restaurant.edit', compact('restaurant'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Le nom du restaurant est obligatoire.',
        ]);

        $restaurant = auth()->user()->restaurant;
        $logoPath   = $restaurant?->logo;

        if ($request->hasFile('logo')) {
            try {
                if ($logoPath) $this->fileUpload->delete($logoPath);
                $logoPath = $this->fileUpload->uploadImage($request->file('logo'), 'logos');
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => $e->getMessage()]);
            }
        }

        if ($restaurant) {
            $restaurant->update([
                'name'        => $request->name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description,
                'logo'        => $logoPath,
            ]);
        } else {
            Restaurant::create([
                'user_id'     => auth()->id(),
                'name'        => $request->name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description,
                'logo'        => $logoPath,
            ]);
        }

        return redirect()->route('gerant.dashboard')
               ->with('success', 'Restaurant enregistré avec succès.');
    }
}