<?php

namespace App\Http\Controllers;

use App\Services\WardrobeService;
use Illuminate\Http\Request;

class WardrobeItemController extends Controller
{
    protected $wardrobeService;

    public function __construct(WardrobeService $wardrobeService)
    {
        $this->wardrobeService = $wardrobeService;
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

        $result = $this->wardrobeService->addClothingToWardrobe(auth()->id(), $request->clothing_id);

        return response()->json($result['message'], $result['status']);
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
        $request->validate([
            'clothing_id' => 'required|exists:clothing,id',
        ]);

        $wardrobeItem = WardrobeItem::find($id);

        if (!$wardrobeItem) {
            return response()->json(['message' => 'Wardrobe item not found'], 404);
        }

        // Update the clothing item in the wardrobe
        $wardrobeItem->clothing_id = $request->clothing_id;
        $wardrobeItem->save();

        return response()->json(['message' => 'Wardrobe item updated successfully'], 200);
    }

    /**
     * Remove a clothing item from the user's wardrobe.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteWardrobeItem($id)
    {
        $result = $this->wardrobeService->removeClothingFromWardrobe($id, auth()->id());

        return response()->json($result['message'], $result['status']);
    }
}
