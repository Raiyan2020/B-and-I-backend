<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = now()->format('Y');
        $general_settings = array(
            array('key' => 'website_name_ar', 'value' => 'الأعمال والاستثمارات', 'created_at' => now()),
            array('key' => 'website_name_en', 'value' => 'Business and Investments', 'created_at' => now()),
            array('key' => 'terms_ar', 'value' => '<p>الشروط والاحكام</p>', 'created_at' => now()),
            array('key' => 'terms_en', 'value' => '<p>Terms and Conditions</p>', 'created_at' => now()),
            array('key' => 'privacy_policy_ar', 'value' => '<p> <strong>سياسة</strong> الخصوصية</p>', 'created_at' => now()),
            array('key' => 'privacy_policy_en', 'value' => '<p>Privacy Policy</p>', 'created_at' => now()),
            array('key' => 'about_us_title_ar', 'value' => 'من نحن', 'created_at' => now()),
            array('key' => 'about_us_title_en', 'value' => 'Who We Are', 'created_at' => now()),
            array('key' => 'about_us_description_ar', 'value' => 'الأعمال والاستثمارات منصة كويتية تربط المستثمرين بأصحاب الفرص بشكل آمن وموثوق، مع أعلى مستوى من الخصوصية وإخفاء الهوية.', 'created_at' => now()),
            array('key' => 'about_us_description_en', 'value' => 'The Business and Investments website connects investors with visionaries in a secure and transparent way, with the highest level of privacy and identity concealment.', 'created_at' => now()),
            array('key' => 'project_brief_ar', 'value' => 'ربط أصحاب الرؤى برأس المال. منصة آمنة ومجهولة لصفقات الأعمال الجادة.', 'created_at' => now()),
            array('key' => 'project_brief_en', 'value' => 'Connecting visionaries with capital. A secure and anonymous platform for legitimate business deals.', 'created_at' => now()),
            array('key' => 'contact_number', 'value' => '966556565297', 'created_at' => now()),
            array('key' => 'contact_mail', 'value' => 'saudiaticket@gmail.com', 'created_at' => now()),
            array('key' => 'logo1', 'value' => 'logo.svg', 'created_at' => '2024-02-19 21:54:41'),
            array('key' => 'favicon2', 'value' => 'favicon.svg', 'created_at' => '2024-02-19 21:54:41'),
            array('key' => 'commercial_register', 'value' => '4031264892', 'created_at' => '2024-02-19 22:10:43'),
            array('key' => 'tax_number', 'value' => '311025770300003', 'created_at' => '2024-02-19 22:10:43'),
            array('key' => 'copy_right', 'value' => "جميع الحقوق محفوظة © $year", 'created_at' => '2024-02-19 22:14:46'),
            array('key' => 'twitter_links', 'value' => 'https://x.com/ticket_71?s=21&t=8TXEVn0zqOXiAm6TasfkUg', 'created_at' => '2024-02-19 22:50:59'),
            array('key' => 'whatsapp_link', 'value' => 'https://wa.me/message/V63V6XGEJHFCD1', 'created_at' => '2024-02-19 22:50:59'),
            array('key' => 'snap_link', 'value' => 'https://t.snapchat.com/o3OcXv11', 'created_at' => '2024-02-19 22:50:59'),
            array('key' => 'tiktok_link', 'value' => 'https://www.tiktok.com/ar/', 'created_at' => '2024-02-19 22:50:59'),
            array('key' => 'login_page_image3', 'value' => 'login_page_image3.webp', 'created_at' => '2024-02-24 23:51:52'),
            array('key' => 'website_header_title_ar', 'value' => 'حيث تلتقي الرؤية برأس المال', 'created_at' => now()),
            array('key' => 'website_header_title_en', 'value' => 'Where Vision Meets Capital', 'created_at' => now()),
            array('key' => 'website_header_desc_ar', 'value' => 'النظام البيئي الرقمي الحصري لربط أصحاب الأعمال والمستثمرين بخصوصية وأمان.', 'created_at' => now()),
            array('key' => 'website_header_desc_en', 'value' => 'The exclusive digital platform to connect entrepreneurs and investors with the highest level of privacy and transparency.', 'created_at' => now()),
            array('key' => 'packages_page_title_ar', 'value' => 'اختر مستوى الوصول', 'created_at' => now()),
            array('key' => 'packages_page_title_en', 'value' => 'Choose your access level', 'created_at' => now()),
            array('key' => 'packages_page_description_ar', 'value' => 'باقات اشتراك مرنة للوصول إلى مزايا منصة الأعمال والاستثمارات.', 'created_at' => now()),
            array('key' => 'packages_page_description_en', 'value' => 'Flexible subscription plans to access Business &amp; Investments platform features.', 'created_at' => now()),
        );
        GeneralSetting::insert($general_settings);
    }
}
