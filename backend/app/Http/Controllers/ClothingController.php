<?php

namespace App\Http\Controllers;

use App\Services\WardrobeService;
use Illuminate\Http\Request;

class ClothingController extends Controller
{
    protected $wardrobeService;

    public function __construct(WardrobeService $wardrobeService)
    {
        $this->wardrobeService = $wardrobeService;
    }

    /**
     * Display a listing of the available clothing in the system.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothing = $this->wardrobeService->getAllClothingItems();
        return response()->json($clothing, 200);
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

        $result = $this->wardrobeService->addClothingToWardrobe($request->user()->id, $request->clothing_id);

        return response()->json($result['message'], $result['status']);
    }

    /**
     * Display the specified clothing item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clothing = $this->wardrobeService->getClothingItem($id);

        if (!$clothing) {
            return response()->json(['message' => 'Clothing item not found'], 404);
        }

        return response()->json($clothing, 200);
    }
}
