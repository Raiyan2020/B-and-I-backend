<?php

namespace App\Http\Requests\Dashboard\Categories;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $categoryId = $this->route('category');
        $categoryId = is_object($categoryId) ? $categoryId->id : $categoryId;

        $maxOrder = Category::max('order') ?? 0;

        return [
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'order' => ['required', 'numeric', 'min:1', 'max:' . max(1, $maxOrder)],
            'status' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }
}
