<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use Illuminate\Http\Request;

class WardrobeController extends Controller
{
    /**
     * Display the user's wardrobe.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the user's wardrobe
        $wardrobe = Wardrobe::where('user_id', auth()->id())->first();

        if (!$wardrobe) {
            return response()->json(['message' => 'Your wardrobe is empty'], 200);
        }

        // Load wardrobe items and their associated clothing
        $wardrobeItems = WardrobeItem::with('clothing')->where('wardrobe_id', $wardrobe->id)->get();

        return response()->json($wardrobeItems, 200);
    }

    /**
     * Show all available clothing options to choose from.
     *
     * @return \Illuminate\Http\Response
     */
    public function availableClothing() // TODO: sort by clothes that are not already in wardrobe
    {
        // Retrieve all clothing items from the database
        $clothingItems = Clothing::all();

        return response()->json($clothingItems, 200);
    }

    /**
     * Add a clothing item to the user's wardrobe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToWardrobe(Request $request)
    {
        $request->validate([
            'clothing_id' => 'required|exists:clothing,id',
        ]);

        $wardrobe = Wardrobe::firstOrCreate(['user_id' => auth()->id()]);

        // Check if the clothing item is already in the wardrobe
        $exists = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $request->clothing_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Clothing item already in wardrobe'], 409);
        }

        WardrobeItem::create([
            'wardrobe_id' => $wardrobe->id,
            'clothing_id' => $request->clothing_id,
        ]);

        return response()->json(['message' => 'Clothing item added to wardrobe successfully'], 201);
    }

    /**
     * Remove a clothing item from the user's wardrobe.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeFromWardrobe($clothingId)
    {
        // Find the wardrobe item by the clothing ID and ensure it belongs to the authenticated user's wardrobe
        $wardrobe = Wardrobe::where('user_id', auth()->id())->first();

        if (!$wardrobe) {
            return response()->json(['message' => 'Wardrobe not found'], 404);
        }

        // Find the wardrobe item with the provided clothing ID
        $wardrobeItem = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $clothingId)
            ->first();

        if (!$wardrobeItem) {
            return response()->json(['message' => 'Clothing item not found in wardrobe'], 404);
        }

        // Delete the wardrobe item
        $wardrobeItem->delete();

        return response()->json(['message' => 'Clothing item removed from wardrobe successfully'], 200);
    }
}
