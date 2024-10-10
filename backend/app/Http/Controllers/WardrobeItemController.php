<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use Illuminate\Http\Request;

class WardrobeItemController extends Controller
{
    /**
     * Display all wardrobe items for the user.
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
     * Add a clothing item to the user's wardrobe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToWardrobe(Request $request)
    {
        // Validate the request data
        $request->validate([
            'clothing_id' => 'required|exists:clothing,id',
        ]);

        // Get or create the user's wardrobe
        $wardrobe = Wardrobe::firstOrCreate(['user_id' => auth()->id()]);

        // Check if the clothing item is already in the wardrobe
        $exists = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $request->clothing_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Clothing item already in wardrobe'], 409);
        }

        // Add clothing item to the user's wardrobe
        WardrobeItem::create([
            'wardrobe_id' => $wardrobe->id,
            'clothing_id' => $request->clothing_id,
        ]);

        return response()->json(['message' => 'Clothing item added to wardrobe successfully'], 201);
    }

    /**
     * Update a wardrobe item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWardrobeItem(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'clothing_id' => 'required|exists:clothing,id',
        ]);

        // Find the wardrobe item by ID
        $wardrobeItem = WardrobeItem::find($id);

        if (!$wardrobeItem) {
            return response()->json(['message' => 'Wardrobe item not found'], 404);
        }

        // Update the clothing_id of the wardrobe item
        $wardrobeItem->clothing_id = $request->clothing_id;
        $wardrobeItem->save();

        return response()->json(['message' => 'Wardrobe item updated successfully'], 200);
    }

/**
     * Remove the specified clothing item from the user's wardrobe.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteWardrobeItem($id)
    {
        // Find the wardrobe item by ID and ensure it belongs to the authenticated user
        $wardrobeItem = WardrobeItem::where('id', $id)
                                    ->whereHas('wardrobe', function ($query) {
                                        $query->where('user_id', auth()->id());
                                    })
                                    ->first();

        if (!$wardrobeItem) {
            return response()->json(['message' => 'Wardrobe item not found'], 404);
        }

        // Delete the item from the wardrobe
        $wardrobeItem->delete();

        return response()->json(['message' => 'Wardrobe item deleted'], 200);
    }
}