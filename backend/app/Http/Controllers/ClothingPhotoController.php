<?php

namespace App\Http\Controllers;

use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use App\Models\ClothingPhoto;
use Illuminate\Http\Request;

class ClothingPhotoController extends Controller
{
    /**
     * Display the clothing photos in the user's wardrobe.
     *
     * @return \Illuminate\Http\Response
     */
    public function showWardrobePhotos()
    {
        // Get the user's wardrobe
        $wardrobe = Wardrobe::where('user_id', auth()->id())->first();

        if (!$wardrobe) {
            return response()->json(['message' => 'Your wardrobe is empty'], 200);
        }

        // Load all clothing items in the wardrobe
        $wardrobeItems = WardrobeItem::where('wardrobe_id', $wardrobe->id)->get();

        // Map the wardrobe items to get their associated photos
        $photos = $wardrobeItems->map(function ($item) {
            $clothingPhoto = ClothingPhoto::where('id', $item->clothing_id)->first();
            return [
                'clothing_id' => $item->clothing_id,
                'photo_url' => $clothingPhoto ? $clothingPhoto->photo_url : null,
            ];
        });

        return response()->json($photos, 200);
    }
}
