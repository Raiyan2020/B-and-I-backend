<?php

namespace App\Services\Dashboard;

use App\Models\Category;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Model;

class CategoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Category::class);
    }

    /**
     * Preprocess data before storing.
     */
    public function storeData(array $data): array
    {
        // Add order automatically (max order + 1)
        $maxOrder = Category::max('order') ?? 0;
        $data['order'] = $maxOrder + 1;

        return $data;
    }

    /**
     * Preprocess data before updating.
     * Handles order shifting logic.
     */
    public function updateData(array $data, int $id): array
    {
        $category = $this->find($id);
        $oldOrder = $category->order;
        $newOrder = $data['order'] ?? $oldOrder;

        // Only shift orders if order changed
        if ($oldOrder != $newOrder && isset($data['order'])) {
            // Shift orders: increment orders >= new order
            Category::where('order', '>=', $newOrder)
                ->where('id', '!=', $id)
                ->increment('order');
        }

        return $data;
    }

    /**
     * Actions to perform after creating a category.
     */
    public function afterCreate(Model $model): void
    {
        // Any post-create logic can go here
    }

    /**
     * Actions to perform after updating a category.
     */
    public function afterUpdate(Model $model): void
    {
        // Any post-update logic can go here
    }

    /**
     * Get max order for validation in forms.
     */
    public function getMaxOrder(): int
    {
        return Category::max('order') ?? 0;
    }
}
