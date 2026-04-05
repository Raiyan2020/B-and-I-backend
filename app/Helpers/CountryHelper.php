<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Get list of countries with codes and flags (excluding Israel).
     *
     * @return array
     */
    public static function getCountries(): array
    {
        return [
            ['code' => '+966', 'name_en' => 'Saudi Arabia', 'name_ar' => 'السعودية', 'iso' => 'sa', 'phone_start' => '5'],
            ['code' => '+971', 'name_en' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'iso' => 'ae', 'phone_start' => '5'],
            ['code' => '+965', 'name_en' => 'Kuwait', 'name_ar' => 'الكويت', 'iso' => 'kw', 'phone_start' => '5'],
            ['code' => '+974', 'name_en' => 'Qatar', 'name_ar' => 'قطر', 'iso' => 'qa', 'phone_start' => '3'],
            ['code' => '+973', 'name_en' => 'Bahrain', 'name_ar' => 'البحرين', 'iso' => 'bh', 'phone_start' => '3'],
            ['code' => '+968', 'name_en' => 'Oman', 'name_ar' => 'عمان', 'iso' => 'om', 'phone_start' => '9'],
            ['code' => '+961', 'name_en' => 'Lebanon', 'name_ar' => 'لبنان', 'iso' => 'lb', 'phone_start' => '3'],
            ['code' => '+962', 'name_en' => 'Jordan', 'name_ar' => 'الأردن', 'iso' => 'jo', 'phone_start' => '7'],
            ['code' => '+963', 'name_en' => 'Syria', 'name_ar' => 'سوريا', 'iso' => 'sy', 'phone_start' => '9'],
            ['code' => '+964', 'name_en' => 'Iraq', 'name_ar' => 'العراق', 'iso' => 'iq', 'phone_start' => '7'],
            ['code' => '+20', 'name_en' => 'Egypt', 'name_ar' => 'مصر', 'iso' => 'eg', 'phone_start' => '1'],
            ['code' => '+212', 'name_en' => 'Morocco', 'name_ar' => 'المغرب', 'iso' => 'ma', 'phone_start' => '6'],
            ['code' => '+213', 'name_en' => 'Algeria', 'name_ar' => 'الجزائر', 'iso' => 'dz', 'phone_start' => '5'],
            ['code' => '+216', 'name_en' => 'Tunisia', 'name_ar' => 'تونس', 'iso' => 'tn', 'phone_start' => '9'],
            ['code' => '+218', 'name_en' => 'Libya', 'name_ar' => 'ليبيا', 'iso' => 'ly', 'phone_start' => '9'],
            ['code' => '+249', 'name_en' => 'Sudan', 'name_ar' => 'السودان', 'iso' => 'sd', 'phone_start' => '9'],
            ['code' => '+967', 'name_en' => 'Yemen', 'name_ar' => 'اليمن', 'iso' => 'ye', 'phone_start' => '7'],
            ['code' => '+1', 'name_en' => 'United States', 'name_ar' => 'الولايات المتحدة', 'iso' => 'us', 'phone_start' => '2'],
            ['code' => '+44', 'name_en' => 'United Kingdom', 'name_ar' => 'المملكة المتحدة', 'iso' => 'gb', 'phone_start' => '7'],
            ['code' => '+33', 'name_en' => 'France', 'name_ar' => 'فرنسا', 'iso' => 'fr', 'phone_start' => '6'],
            ['code' => '+49', 'name_en' => 'Germany', 'name_ar' => 'ألمانيا', 'iso' => 'de', 'phone_start' => '1'],
            ['code' => '+39', 'name_en' => 'Italy', 'name_ar' => 'إيطاليا', 'iso' => 'it', 'phone_start' => '3'],
            ['code' => '+34', 'name_en' => 'Spain', 'name_ar' => 'إسبانيا', 'iso' => 'es', 'phone_start' => '6'],
            ['code' => '+7', 'name_en' => 'Russia', 'name_ar' => 'روسيا', 'iso' => 'ru', 'phone_start' => '9'],
            ['code' => '+86', 'name_en' => 'China', 'name_ar' => 'الصين', 'iso' => 'cn', 'phone_start' => '1'],
            ['code' => '+81', 'name_en' => 'Japan', 'name_ar' => 'اليابان', 'iso' => 'jp', 'phone_start' => '9'],
            ['code' => '+82', 'name_en' => 'South Korea', 'name_ar' => 'كوريا الجنوبية', 'iso' => 'kr', 'phone_start' => '1'],
            ['code' => '+91', 'name_en' => 'India', 'name_ar' => 'الهند', 'iso' => 'in', 'phone_start' => '9'],
            ['code' => '+92', 'name_en' => 'Pakistan', 'name_ar' => 'باكستان', 'iso' => 'pk', 'phone_start' => '3'],
            ['code' => '+90', 'name_en' => 'Turkey', 'name_ar' => 'تركيا', 'iso' => 'tr', 'phone_start' => '5'],
            ['code' => '+27', 'name_en' => 'South Africa', 'name_ar' => 'جنوب أفريقيا', 'iso' => 'za', 'phone_start' => '8'],
            ['code' => '+61', 'name_en' => 'Australia', 'name_ar' => 'أستراليا', 'iso' => 'au', 'phone_start' => '4'],
            ['code' => '+1', 'name_en' => 'Canada', 'name_ar' => 'كندا', 'iso' => 'ca', 'phone_start' => '2'],
            ['code' => '+52', 'name_en' => 'Mexico', 'name_ar' => 'المكسيك', 'iso' => 'mx', 'phone_start' => '1'],
            ['code' => '+55', 'name_en' => 'Brazil', 'name_ar' => 'البرازيل', 'iso' => 'br', 'phone_start' => '9'],
            ['code' => '+54', 'name_en' => 'Argentina', 'name_ar' => 'الأرجنتين', 'iso' => 'ar', 'phone_start' => '9'],
            ['code' => '+31', 'name_en' => 'Netherlands', 'name_ar' => 'هولندا', 'iso' => 'nl', 'phone_start' => '6'],
            ['code' => '+32', 'name_en' => 'Belgium', 'name_ar' => 'بلجيكا', 'iso' => 'be', 'phone_start' => '4'],
            ['code' => '+41', 'name_en' => 'Switzerland', 'name_ar' => 'سويسرا', 'iso' => 'ch', 'phone_start' => '7'],
            ['code' => '+46', 'name_en' => 'Sweden', 'name_ar' => 'السويد', 'iso' => 'se', 'phone_start' => '7'],
            ['code' => '+47', 'name_en' => 'Norway', 'name_ar' => 'النرويج', 'iso' => 'no', 'phone_start' => '4'],
            ['code' => '+45', 'name_en' => 'Denmark', 'name_ar' => 'الدنمارك', 'iso' => 'dk', 'phone_start' => '2'],
            ['code' => '+358', 'name_en' => 'Finland', 'name_ar' => 'فنلندا', 'iso' => 'fi', 'phone_start' => '4'],
            ['code' => '+351', 'name_en' => 'Portugal', 'name_ar' => 'البرتغال', 'iso' => 'pt', 'phone_start' => '9'],
            ['code' => '+30', 'name_en' => 'Greece', 'name_ar' => 'اليونان', 'iso' => 'gr', 'phone_start' => '6'],
            ['code' => '+48', 'name_en' => 'Poland', 'name_ar' => 'بولندا', 'iso' => 'pl', 'phone_start' => '5'],
            ['code' => '+36', 'name_en' => 'Hungary', 'name_ar' => 'المجر', 'iso' => 'hu', 'phone_start' => '2'],
            ['code' => '+40', 'name_en' => 'Romania', 'name_ar' => 'رومانيا', 'iso' => 'ro', 'phone_start' => '7'],
            ['code' => '+380', 'name_en' => 'Ukraine', 'name_ar' => 'أوكرانيا', 'iso' => 'ua', 'phone_start' => '5'],
            ['code' => '+60', 'name_en' => 'Malaysia', 'name_ar' => 'ماليزيا', 'iso' => 'my', 'phone_start' => '1'],
            ['code' => '+65', 'name_en' => 'Singapore', 'name_ar' => 'سنغافورة', 'iso' => 'sg', 'phone_start' => '9'],
            ['code' => '+66', 'name_en' => 'Thailand', 'name_ar' => 'تايلاند', 'iso' => 'th', 'phone_start' => '8'],
            ['code' => '+62', 'name_en' => 'Indonesia', 'name_ar' => 'إندونيسيا', 'iso' => 'id', 'phone_start' => '8'],
            ['code' => '+84', 'name_en' => 'Vietnam', 'name_ar' => 'فيتنام', 'iso' => 'vn', 'phone_start' => '9'],
            ['code' => '+63', 'name_en' => 'Philippines', 'name_ar' => 'الفلبين', 'iso' => 'ph', 'phone_start' => '9'],
            ['code' => '+64', 'name_en' => 'New Zealand', 'name_ar' => 'نيوزيلندا', 'iso' => 'nz', 'phone_start' => '2'],
        ];
    }

    /**
     * Get country name based on current locale.
     *
     * @param array $country
     * @return string
     */
    public static function getCountryName(array $country): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $country['name_ar'] : $country['name_en'];
    }

    /**
     * Get country by code.
     *
     * @param string $code
     * @return array|null
     */
    public static function getCountryByCode(string $code): ?array
    {
        $countries = self::getCountries();
        foreach ($countries as $country) {
            if ($country['code'] === $code) {
                return $country;
            }
        }
        return null;
    }

    /**
     * Get phone start digit by country code.
     *
     * @param string $code
     * @return string|null
     */
    public static function getPhoneStartByCode(string $code): ?string
    {
        $country = self::getCountryByCode($code);
        return $country['phone_start'] ?? null;
    }
}
