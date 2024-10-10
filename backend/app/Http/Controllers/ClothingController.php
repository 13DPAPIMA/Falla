<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use App\Models\ClothingType;
use Illuminate\Http\Request;

class ClothingController extends Controller
{
    /**
     * Display a listing of the available clothing in the system.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all predefined clothing items with their type, material, etc.
        $clothing = Clothing::with(['type', 'photo', 'material'])->get();
        return response()->json($clothing, 200);
    }

    /**
     * Add a clothing item to the user's wardrobe.
     * The user can only add existing clothing items from the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToWardrobe(Request $request)
    {
        // Validate that the selected clothing item exists in the database
        $request->validate([
            'clothing_id' => 'required|exists:clothing,id',
        ]);

        // Get the user's wardrobe or create one if it doesn't exist
        $wardrobe = Wardrobe::firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        // Check if the clothing is already in the user's wardrobe
        $existingItem = WardrobeItem::where('wardrobe_id', $wardrobe->id)
                                    ->where('clothing_id', $request->clothing_id)
                                    ->first();
        
        if ($existingItem) {
            return response()->json(['message' => 'This item is already in your wardrobe.'], 400);
        }

        // Add the clothing item to the user's wardrobe
        $wardrobeItem = WardrobeItem::create([
            'wardrobe_id' => $wardrobe->id,
            'clothing_id' => $request->clothing_id,
        ]);

        return response()->json($wardrobeItem, 201);
    }

    /**
     * Display the specified clothing item from the user's wardrobe.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the clothing item by ID
        $clothing = Clothing::with(['type', 'photo', 'material'])->find($id);

        if (!$clothing) {
            return response()->json(['message' => 'Clothing item not found'], 404);
        }

        return response()->json($clothing, 200);
    }


}
