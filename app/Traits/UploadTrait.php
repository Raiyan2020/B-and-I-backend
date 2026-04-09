<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

trait UploadTrait
{
    public function uploadAllTypes($file, $directory, $width = null, $height = null)
    {
        $this->ensureDirectoryExists('storage/images/' . $directory);

        $fileMimeType = $file->getClientMimeType();
        $isImage = str_starts_with($fileMimeType, 'image/');

        if ($isImage) {
            return $this->handleImageUpload($file, $directory, $width, $height, $fileMimeType);
        }

        return $this->handleFileUpload($file, $directory, $fileMimeType);
    }

    private function ensureDirectoryExists($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    private function handleImageUpload($file, $directory, $width, $height, $fileMimeType)
    {
        $allowedImagesMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
        ];

        if (!in_array($fileMimeType, $allowedImagesMimeTypes)) {
            return 'default.webp';
        }

        return $this->uploadImage($file, $directory, $width, $height);
    }

    private function handleFileUpload($file, $directory, $fileMimeType)
    {
        $allowedMimeTypes = [
            // Documents - PDF
            'application/pdf',

            // Word Documents
            'application/msword', // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx

            // Excel Documents
            'application/vnd.ms-excel', // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-office', // generic for office
            'application/excel',
            'application/x-excel',
            'application/x-msexcel',

            // PowerPoint
            'application/vnd.ms-powerpoint', // .ppt
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx

            // Text formats (optionally)
            'text/plain', // .txt
            'application/rtf', // .rtf

            // OpenOffice / LibreOffice
            'application/vnd.oasis.opendocument.text', // .odt
            'application/vnd.oasis.opendocument.spreadsheet', // .ods
            'application/vnd.oasis.opendocument.presentation', // .odp

            // Compressed Office files (optional)
            'application/zip', // sometimes office files can come zipped
            'application/octet-stream', // fallback (used by some older systems)

            // Audio
            'audio/mpeg', // .mp3
            'audio/mp3',
            'audio/mp4',
            'audio/x-m4a',
            'audio/m4a',
            'audio/aac',
            'audio/wav',
            'audio/x-wav',
            'audio/webm',
            'audio/ogg',
            'audio/3gpp',
            'audio/3gpp2',

            // Video
            'video/mp4',
            'video/x-m4v',
            'video/webm',
            'video/ogg',
            'video/x-msvideo', // .avi
            'video/x-ms-wmv',
            'video/quicktime', // .mov
            'video/x-flv',
            'video/x-matroska', // .mkv
            'video/3gpp',
            'video/3gpp2',
            'video/mp2t', // .ts
            'video/mpeg',
            'video/x-f4v',
            'video/x-f4p',
            'audio/x-f4a', // technically audio, but often included in video packages
            'audio/x-f4b',
        ];


        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return 'default.webp';
        }

        return $this->uploadFile($file, $directory);
    }

    public function uploadFile($file, $directory)
    {
        $filename = time() . rand(1000000, 9999999) . '.' . $file->getClientOriginalExtension();
        $path = 'images/' . $directory;
        $file->storeAs($path, $filename);
        return $filename;
    }

    public function uploadImage($file, $directory, $width = null, $height = null)
    {
        $manager = ImageManager::gd(autoOrientation: false);

        // read the image
        $img = $manager->read($file);

        // orient image according to exif data
        $img->orient();

        $thumbsPath = 'storage/images/' . $directory;
        if (!File::exists($thumbsPath)) {
            File::makeDirectory($thumbsPath, 0777, true, true);
        }
        $name = time() . '_' . rand(1111, 9999) . '.webp';

        if (null != $width && null != $height)
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });

        $img->save($thumbsPath . '/' . $name, 90, 'webp');
        return (string)$name;
    }

    //    public function deleteFile($file_name, $directory = 'unknown'): void
    //    {
    //        if ($file_name && $file_name !== 'default.png') {
    //            $path = "images/$directory/$file_name";
    //
    //            if (Storage::disk('public')->exists($path)) {
    //                Storage::disk('public')->delete($path);
    //            }
    //        }
    //    }
    public function deleteFile($file_name, $directory = 'unknown'): void
    {
        if (!$file_name || $file_name === 'default.png' || $file_name === 'default.webp') {
            return;
        }

        $path = "storage/images/$directory/$file_name";
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function defaultImage($directory = null): string
    {
        $directory = $directory ?: 'users';
        $relativePath = "storage/images/$directory/default.webp";
        $fullPath = public_path($relativePath);

        if (!file_exists($fullPath)) {
            return 'https://i.vgy.me/KLzxNI.png';
        }

        return asset($relativePath);
    }

    public static function getImage($name, $directory)
    {
        return asset("storage/images/$directory/" . $name);
    }

    /**
     * Get image URL helper method.
     * Uses existing defaultImage/getImage logic.
     */
    public function imageUrl(?string $fileName, string $directory): string
    {
        if (empty($fileName)) {
            return $this->defaultImage($directory);
        }

        return $this->getImage($fileName, $directory);
    }

    public function gettingFileType($file, $isFile = false)
    {
        $needle = $isFile ? $file->getClientOriginalExtension() : explode('.', $file)[1];
        return match (true) {
            in_array($needle, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'ico', 'bmp', 'tiff', 'tif', 'heic', 'heif']) => 'image',
            in_array($needle, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'zip', 'rtf']) => 'document',
            in_array($needle, ['mp3', 'mp4', 'wav', 'ogg', 'webm', '3gpp', '3gpp2', 'm4a', 'aac']) => 'audio',
            in_array($needle, ['mp4', 'x-m4v', 'webm', 'ogg', 'avi', 'wmv', 'mov', 'flv', 'mkv', '3gpp', '3gpp2', 'ts', 'mpeg', 'f4v', 'f4p', 'f4a', 'f4b']) => 'video',
            default => 'unknown',
        };
    }
}
