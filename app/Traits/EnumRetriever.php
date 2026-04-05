<?php

namespace App\Traits;


trait EnumRetriever
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function valuesWithout(array $except): array
    {
        return array_values(array_diff(self::values(), $except));
    }

    public static function fromCase($case)
    {
        $name_is_exist = array_search(strtolower($case), array_map('strtolower', self::names()));
        $case = $name_is_exist != false ? self::names()[$name_is_exist] : self::names()[0];
        return constant("self::$case")->value;
    }

    public static function getName($value): string
    {
        $constants = self::cases();
        foreach ($constants as $constant) {
            if ($constant->value === $value) {
                return $constant->name;
            }
        }
        return self::cases()[0]->name;
    }

    public static function getNames($key = null): array
    {
        $constants = self::cases();
        $names = [];
        foreach ($constants as $constant) {
            $names[strtolower($constant->name)] = [
                'value' => $constant->value,
                'name'  => $constant->name,
                'label' => $key ? __('enums.' . $key . '.' . $constant->name) : __('enums.' . $constant->name),
            ];
        }
        return $names;
    }

    public static function getTranslatedName($value, $key = null): string
    {
        return $key ? __('enums.' . $key . '.' . self::getName($value)) :
            __('enums.' . self::getName($value));
    }

    public static function getFullObj($value, $key = null)
    {
        $name = self::getName($value);
        return [
            'value' => $value,
            'name'  => $name,
            'label' => $key ? __('enums.' . $key . '.' . $name) : __('enums.' . $name),
        ];
    }
}
