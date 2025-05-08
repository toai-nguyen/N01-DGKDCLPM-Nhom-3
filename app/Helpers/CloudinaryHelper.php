<?php

// filepath: d:\CodePratice\PHP\project\26.UAT_QLTS_Laravel\app\Helpers\CloudinaryHelper.php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Exception;

class CloudinaryHelper
{
    /**
     * Upload an image to Cloudinary and return the public ID and secure path
     * 
     * @param UploadedFile $image The image file to upload
     * @param string $folder The folder to upload the image to
     * @param array $options Additional upload options
     * @return array|null Returns ['public_id' => string, 'secure_url' => string] or null if upload fails
     * @throws Exception If upload fails
     */
    public static function uploadImageToCloudinary(UploadedFile $image, string $folder = 'novel_project/cover_image')
    {
        try {
            // Default transformation options
            $defaultOptions = [
                'folder' => $folder,
                'transformation' => [
                    'width' => 440,
                    'height' => 620,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ],
            ];
            
            // Upload image to Cloudinary
            $cloudinaryImage = Cloudinary::upload($image->getRealPath(), $defaultOptions);
            
            // Return public ID and secure URL
            return [
                'public_id' => $cloudinaryImage->getPublicId(),
                'secure_url' => $cloudinaryImage->getSecurePath()
            ];
        } catch (Exception $e) {
            throw new Exception("Error uploading image to Cloudinary: " . $e->getMessage());
        }
    }
    public static function updateImage(string $publicId, UploadedFile $image)
    {
        if (!$publicId) {
            throw new Exception("Public ID is required to update the image.");
        } else {
            try {
                // Delete the old image from Cloudinary
                Cloudinary::destroy($publicId);
                //upload new image
                $cloudinaryImage = Cloudinary::upload($image->getRealPath(), [
                    'folder' => 'novel_project/cover_image',
                    'transformation' => [
                        'width' => 440,
                        'height' => 620,
                        'crop' => 'fill',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ]);
                
                // Return public ID and secure URL
                return [
                    'public_id' => $cloudinaryImage->getPublicId(),
                    'secure_url' => $cloudinaryImage->getSecurePath()
                ];
            } catch (Exception $e) {
                throw new Exception("Error updating image on Cloudinary: " . $e->getMessage());
            }
        }

    }
    public function deleteImage(string $publicId)
    {
        try {
            // Delete image from Cloudinary
            Cloudinary::destroy($publicId);
        } catch (Exception $e) {
            throw new Exception("Error deleting image from Cloudinary: " . $e->getMessage());
        }
    }
}