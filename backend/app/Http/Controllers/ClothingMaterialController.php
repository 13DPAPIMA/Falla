<?php

namespace App\Http\Controllers;

use App\Models\ClothingMaterial;
use Illuminate\Http\Request;

class ClothingMaterialController extends Controller
{
    /**
     * Display a listing of all clothing materials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all clothing materials from the database
        $materials = ClothingMaterial::all();
        return response()->json($materials, 200);
    }

    /**
     * Display the specified clothing material.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the clothing material by ID
        $material = ClothingMaterial::find($id);

        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        return response()->json($material, 200);
    }
}
