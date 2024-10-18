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

        if (!$wardrobe) {
            return response()->json(['message' => 'Your wardrobe is empty'], 200);
        }

        $wardrobeItems = $this->wardrobeService->getWardrobeItems($wardrobe->id);

        return response()->json($wardrobeItems, 200);
    }

    /**
     * Show all available clothing options to choose from.
     *
     * @return \Illuminate\Http\Response
     */
    public function availableClothing()
    {
        $wardrobe = $this->wardrobeService->getUserWardrobe(auth()->id());

        if ($wardrobe) {
            $clothingItems = $this->wardrobeService->getAvailableClothing($wardrobe->id);
        } else {
            $clothingItems = $this->wardrobeService->getAllClothingItems();
        }

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

        return response()->json($result, $result['status']);
    }

    /**
     * Remove a clothing item from the user's wardrobe.
     *
     * @param  int  $clothingId
     * @return \Illuminate\Http\Response
     */
    public function removeFromWardrobe($clothingId)
    {
        $result = $this->wardrobeService->removeClothingFromWardrobe($clothingId, auth()->id());

        return response()->json($result, $result['status']);
    }
}
