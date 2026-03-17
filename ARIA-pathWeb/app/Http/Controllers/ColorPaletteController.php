<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ColorPalette; 
use Illuminate\Support\Facades\Auth;

class ColorPaletteController extends Controller
{

    public function savePalette(Request $request)
    {
        $request->validate([
            'image_id' => 'required|string',
            'color_palette' => 'required|array',
        ]);

        $colorPalette = ColorPalette::updateOrCreate(
            [
                'image_id' => $request->image_id,
                'user_id' => Auth::id(),
            ],
            [
                'color_palette' => $request->color_palette,
            ]
        );

        return response()->json(['success' => true, 'data' => $colorPalette]);
    }

    public function getPalette($imageId, $userId)
    {
        $palette = ColorPalette::where('image_id', $imageId)
                                ->where('user_id', $userId)
                                ->first();

        return response()->json([
            'colorPalette' => $palette ? $palette -> color_palette : null
        ]);
    }

}