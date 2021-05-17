<?php
namespace App\Service;

use Cloudinary;
use Cloudinary\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    public function uploadImageToCloudinary(UploadedFile $file)
    {
        $fileName = $file->getRealPath();

        Cloudinary::config([
            'cloud_name' => $_ENV['CLOUD_NAME'],
            'api_key' => $_ENV['API_KEY'],
            'api_secret' => $_ENV['API_SECRET'],
        ]);

        $imageUploaded = Uploader::upload($fileName, [
            'folder' => 'upload',
        ]);

        return $imageUploaded['secure_url'];
    }
}