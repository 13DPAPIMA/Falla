<?php

namespace App\Services;

use App\Models\Clothing;
use App\Models\Wardrobe;
use App\Models\WardrobeItem;
use Illuminate\Support\Facades\Auth;

class WardrobeService
{
    public function getUserWardrobe($userId)
    {
        return Wardrobe::where('user_id', $userId)->first();
    }

    public function getWardrobeItems($wardrobeId)
    {
        return WardrobeItem::with(['clothing.type', 'clothing.material', 'clothing.style'])->where('wardrobe_id', $wardrobeId)->get();
    }

    public function getAvailableClothing($wardrobeId)
    {
        return Clothing::with(['type', 'material', 'style'])->whereNotIn('id', function ($query) use ($wardrobeId) {
            $query->select('clothing_id')
                ->from('wardrobe_items')
                ->where('wardrobe_id', $wardrobeId);
        })->get();
    }

    public function addClothingToWardrobe($userId, $clothingId)
    {
        $wardrobe = $this->getUserWardrobe($userId);

        // Check if the wardrobe exists
        if (!$wardrobe) {
            $wardrobe = Wardrobe::create(['user_id' => $userId]);
        }

        // Check if the clothing item is already in the wardrobe
        $exists = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $clothingId)
            ->exists();

        if ($exists) {
            return ['message' => 'Clothing item already in wardrobe', 'status' => 409];
        }

        // Add clothing item to the wardrobe
        $wardrobeItem = WardrobeItem::create([
            'wardrobe_id' => $wardrobe->id,
            'clothing_id' => $clothingId,
        ]);

        $wardrobeItem->load(['clothing.type', 'clothing.material', 'clothing.style']);

        return ['data' => $wardrobeItem, 'status' => 201];
    }

    public function removeClothingFromWardrobe($clothingId, $userId)
    {
        $wardrobe = $this->getUserWardrobe($userId);

        if (!$wardrobe) {
            return ['message' => 'Wardrobe not found', 'status' => 404];
        }

        // Find the wardrobe item with the provided clothing ID
        $wardrobeItem = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $clothingId)
            ->first();

        if (!$wardrobeItem) {
            return ['message' => 'Clothing item not found in wardrobe', 'status' => 404];
        }

        // Delete the wardrobe item
        $wardrobeItem->delete();

        return ['message' => 'Clothing item removed from wardrobe successfully', 'status' => 200];
    }

    public function getAllClothingItems()
    {
        return Clothing::with(['type', 'material', 'style'])->get();
    }

    public function getClothingItem($id)
    {
        return Clothing::with(['type', 'material', 'style'])->find($id);
    }
}
