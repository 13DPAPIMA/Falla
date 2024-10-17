<?php

namespace App\Http\Controllers;

use App\Services\WardrobeService;
use Illuminate\Http\Request;

class WardrobeController extends Controller
{
    protected $wardrobeService;

    public function __construct(WardrobeService $wardrobeService)
    {
        $this->wardrobeService = $wardrobeService;
    }

    /**
     * Display the user's wardrobe.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wardrobe = $this->wardrobeService->getUserWardrobe(auth()->id());

        // Load wardrobe items and their associated clothing
        $wardrobeItems = $wardrobe->wardrobeItems()->with('clothing')->get();

        if ($wardrobeItems->isEmpty()) {
            return response()->json(['message' => 'Your wardrobe is empty'], 200);
        }

        return response()->json($wardrobeItems, 200);
    }

    /**
     * Show all available clothing options to choose from.
     *
     * @return \Illuminate\Http\Response
     */
    public function availableClothing()
    {
        $clothingItems = $this->wardrobeService->getAllClothingItems();

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

        $result = $this->wardrobeService->addClothingToWardrobe(auth()->id(), $request->clothing_id);

        return response()->json($result['message'], $result['status']);
    }

    /**
     * Remove a clothing item from the user's wardrobe.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeFromWardrobe($id)
    {
        $result = $this->wardrobeService->removeClothingFromWardrobe($id, auth()->id());

        return response()->json($result['message'], $result['status']);
    }
}
