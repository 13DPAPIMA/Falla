<?php

namespace App\Services;

use App\Models\Clothing;
use App\Models\Wardrobe;
use App\Models\WardrobeItem;

class WardrobeService
{
    public function getUserWardrobe($userId)
    {
        return Wardrobe::firstOrCreate(['user_id' => $userId]);
    }

    public function addClothingToWardrobe($userId, $clothingId)
    {
        $wardrobe = $this->getUserWardrobe($userId);

        // Check if the clothing item is already in the wardrobe
        $exists = WardrobeItem::where('wardrobe_id', $wardrobe->id)
            ->where('clothing_id', $clothingId)
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'Clothing item already in wardrobe', 'status' => 409];
        }

        // Add clothing item to the wardrobe
        $wardrobeItem = WardrobeItem::create([
            'wardrobe_id' => $wardrobe->id,
            'clothing_id' => $clothingId,
        ]);

        return ['success' => true, 'data' => $wardrobeItem, 'status' => 201];
    }

    public function removeClothingFromWardrobe($wardrobeItemId, $userId)
    {
        // Find the wardrobe item and check if it belongs to the user
        $wardrobeItem = WardrobeItem::where('id', $wardrobeItemId)
            ->whereHas('wardrobe', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        if (!$wardrobeItem) {
            return ['success' => false, 'message' => 'Wardrobe item not found', 'status' => 404];
        }

        // Delete the item from the wardrobe
        $wardrobeItem->delete();

        return ['success' => true, 'message' => 'Clothing item removed from wardrobe', 'status' => 200];
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
