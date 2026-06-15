<?php

namespace App\Http\Requests\Dashboard\SubscriptionPackages;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $description = $this->input('description', []);

        foreach (['ar', 'en'] as $locale) {
            if (! array_key_exists($locale, $description)) {
                continue;
            }

            if ($this->isEmptyRichText($description[$locale])) {
                $description[$locale] = null;
            }
        }

        $this->merge([
            'description' => $description,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'numeric', 'min:0', 'max:999999999.999'],
            'description' => ['required', 'array'],
            'description.ar' => ['required', 'string', 'max:65000'],
            'description.en' => ['required', 'string', 'max:65000'],
            'status' => ['required', 'boolean'],
        ];
    }

    private function isEmptyRichText(?string $value): bool
    {
        $plainText = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = preg_replace('/\x{00a0}|&nbsp;|\s+/u', '', $plainText);

        return $plainText === '';
    }
}
