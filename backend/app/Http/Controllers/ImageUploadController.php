<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Проверка на наличие файла
        $request->validate([
            'file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        // Загрузка файла на Cloudinary без трансформации
        $uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

        // Возвращаем URL загруженного изображения
        return response()->json(['url' => $uploadedFileUrl]);
    }

    public function uploadWithTransformation(Request $request)
    {
        // Проверка на наличие файла
        $request->validate([
            'file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        // Загрузка с трансформацией (например, изменение размера изображения до 400x400)
        $uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath(), [
            'folder' => 'uploads',
            'transformation' => [
                'width' => 400,
                'height' => 400,
                'crop' => 'fill',
            ],
        ])->getSecurePath();

        // Возвращаем URL загруженного изображения
        return response()->json(['url' => $uploadedFileUrl]);
    }

    public function getUrl($publicId)
    {
        // Получаем URL изображения по publicId
        $url = cloudinary()->getUrl($publicId);

        return response()->json(['url' => $url]);
    }

    public function fileExists($publicId)
    {
        // Проверка, существует ли файл на Cloudinary
        $exists = Storage::disk('cloudinary')->fileExists($publicId);

        return response()->json(['exists' => $exists]);
    }
}
