<?php

namespace App\Traits\BaseService;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

trait FileNotificationTrait
{
    public function uploadMultiFiles($requestedImageToUpload, $modelImagePath, $key = null): array
    {
        try {
            $multiImages = [];
            if (count($requestedImageToUpload) > 0) {
                $multiImages = array_map(function ($singleImage) use ($key, $modelImagePath) {
                    return $key
                        ? [$key => $this->uploadAllTypes($singleImage, $modelImagePath)]
                        : $this->uploadAllTypes($singleImage, $modelImagePath);
                }, $requestedImageToUpload);
            }

            return $multiImages;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function  moveFilesFromTempToModelDirectory($data, $model, $otherModelInfo = null)
    {
        $modelFields = $model->getFillable();
        $filesFields = array_filter($modelFields, function ($field) {
            return Str::contains($field, ['photo', 'image', 'attachment']);
        });

        $otherModelInfoFields = $otherModelInfo ? array_filter($otherModelInfo->getFillable(), function ($field) {
            return Str::contains($field, ['photo', 'image', 'attachment']);
        }) : [];
        
        foreach (array_merge($filesFields, $otherModelInfoFields) as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $path = public_path('storage/images/uploaded_temp_files/' . $data[$field]);
                if (File::exists($path)) {
                    if(!File::exists(public_path('storage/images/' . $model::IMAGEPATH ))){
                        File::makeDirectory(public_path('storage/images/' . $model::IMAGEPATH), 0777, true, true);
                    }
                    File::move($path, public_path('storage/images/' . $model::IMAGEPATH . '/' . $data[$field]));
                }
            }
            if (isset($data[$field]) && is_array($data[$field])) {
                foreach ($data[$field] as $file) {
                    $path = public_path('storage/images/uploaded_temp_files/' . $file);
                    if (File::exists($path)) {
                        if(!File::exists(public_path('storage/images/' . $model::IMAGEPATH ))){
                            File::makeDirectory(public_path('storage/images/' . $model::IMAGEPATH), 0777, true, true);
                        }
                        File::move($path, public_path('storage/images/' . $model::IMAGEPATH . '/' . $file));
                    }
                }
            }
        }
    }

    public function sendNotification($data)
    {
        try {
            $classData = [
                'type' => $data['type'],
                'data' => isset($data['model_id']) ? $this->prepareNotificationData($data['model_id']) : []
            ];
            Notification::send($data['user'], new $data['class'](data: $classData));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function prepareNotificationData($id)
    {
        return ['class' => $this->model, 'id' => $id];
    }
}
