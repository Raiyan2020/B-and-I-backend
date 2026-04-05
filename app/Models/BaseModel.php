<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use UploadTrait;

    /**
     * Directory name for file uploads.
     * Override in child models if different from plural model name.
     */
    protected ?string $uploadDir = null;

    /**
     * Get the upload directory for this model.
     */
    public function uploadDirectory(): string
    {
        if ($this->uploadDir !== null) {
            return $this->uploadDir;
        }

        // Use FOLDER constant if exists (for backward compatibility)
        if (defined('static::FOLDER')) {
            return static::FOLDER;
        }

        // Default to plural model name
        return Str::plural(Str::snake(class_basename(static::class)));
    }

    /**
     * Get formatted created_at attribute.
     */
    public function getCreatedAtFormattedAttribute(): ?string
    {
        return $this->created_at?->format('Y-m-d h:i A');
    }

    /**
     * Handle image field mutators dynamically.
     */
    public function setAttribute($key, $value)
    {
        // Check if this is an image field (image or ends with _image) and it's in fillable
        if (($key === 'image' || str_ends_with($key, '_image')) && in_array($key, $this->getFillable())) {
            if ($value instanceof UploadedFile) {
                // Upload file using UploadTrait
                $directory = $this->uploadDirectory();
                $fileName = $this->uploadAllTypes($value, $directory);
                return parent::setAttribute($key, $fileName);
            } elseif (is_string($value) || $value === null) {
                // Store as-is if string or null
                return parent::setAttribute($key, $value);
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Handle image field accessors dynamically.
     */
    public function getAttribute($key)
    {
        // Get raw value from attributes array to avoid infinite recursion
        $rawValue = $this->attributes[$key] ?? null;

        // Check if this is an image field (image or ends with _image) and it's in fillable
        if (($key === 'image' || str_ends_with($key, '_image')) && in_array($key, $this->getFillable())) {
            // For image fields, transform the raw value
            if (empty($rawValue)) {
                // Return default image if empty
                return $this->defaultImage($this->uploadDirectory());
            }
            
            // Return full image URL
            return $this->getImage($rawValue, $this->uploadDirectory());
        }

        // For non-image fields, use parent implementation (handles accessors, relations, etc.)
        return parent::getAttribute($key);
    }

    /**
     * Get the raw image filename from database (bypass accessor).
     * Useful when you need the actual filename stored in DB.
     */
    public function getRawImageAttribute(string $field = 'image'): ?string
    {
        return $this->attributes[$field] ?? null;
    }
}
