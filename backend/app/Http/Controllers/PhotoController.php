<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\ClothingPhoto;

class PhotoController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        // Валидация загружаемого файла
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Загрузка фотографии в Cloudinary с отключенной проверкой SSL
            $uploadedFile = $request->file('photo');
            $result = Cloudinary::upload($uploadedFile->getRealPath(), [
                'opt' => [
                    CURLOPT_SSL_VERIFYPEER => false,  // Отключение проверки SSL сертификата
                ]
            ]);

            // Проверка, что загрузка успешна
            if (!$result) {
                return response()->json(['message' => 'Upload failed'], 500);
            }

            $uploadedFileUrl = $result->getSecurePath();
            $publicId = $result->getPublicId();

            // Проверка, что URL и public_id получены
            if (!$uploadedFileUrl || !$publicId) {
                return response()->json(['message' => 'Failed to retrieve upload details'], 500);
            }

            // Сохранение данных о фотографии в базу данных
            $photo = ClothingPhoto::create([
                'cloudinary_public_id' => $publicId,
                'url' => $uploadedFileUrl,
            ]);

            return response()->json(['message' => 'Photo uploaded successfully', 'photo' => $photo]);
        } catch (\Exception $e) {
            // Логирование ошибки
            \Log::error('Photo upload failed: ' . $e->getMessage());

            return response()->json(['message' => 'Upload failed', 'error' => $e->getMessage()], 500);
        }
    }
}
