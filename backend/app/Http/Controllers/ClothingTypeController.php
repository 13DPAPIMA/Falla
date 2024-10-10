<?php

namespace App\Http\Controllers;

use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use Illuminate\Http\Request;

class WardrobeManagementController extends Controller
{
    /**
     * Display the contents of the user's wardrobe.
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
}
