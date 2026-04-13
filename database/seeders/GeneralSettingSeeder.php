<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->format('Y');

        $settings = [
            ['key' => 'website_name_ar', 'value' => 'الأعمال والاستثمارات'],
            ['key' => 'website_name_en', 'value' => 'Business & Investments'],
            ['key' => 'about_us_title_ar', 'value' => 'منصة تربط رأس المال بالفرص الجادة'],
            ['key' => 'about_us_title_en', 'value' => 'A marketplace where capital meets serious opportunities'],
            ['key' => 'about_us_description_ar', 'value' => 'نربط المستثمرين بالمعلنين عبر دورة مراجعة واضحة مع حماية البيانات الحساسة حتى مرحلة الجدية.'],
            ['key' => 'about_us_description_en', 'value' => 'We connect investors with advertisers through a clear review workflow while protecting sensitive information until paid intent is confirmed.'],
            ['key' => 'project_brief_ar', 'value' => 'استكشف الفرص، اشترِ مقعد اهتمام، وقدّم طلبك الرسمي عندما تكون جاهزًا للتفاوض.'],
            ['key' => 'project_brief_en', 'value' => 'Explore opportunities, buy a seat, and submit formal interest when you are ready to negotiate.'],
            ['key' => 'website_header_title_ar', 'value' => 'فرص استثمارية مدققة بمسار واضح'],
            ['key' => 'website_header_title_en', 'value' => 'Vetted opportunities with a clear deal flow'],
            ['key' => 'website_header_desc_ar', 'value' => 'من الاكتشاف إلى التفاوض، نوفر تجربة منظمة للمعلن والمستثمر مع إشراف إداري كامل.'],
            ['key' => 'website_header_desc_en', 'value' => 'From discovery to negotiation, the platform offers a structured experience for advertisers and investors with full admin oversight.'],
            ['key' => 'terms_ar', 'value' => '<p>باستخدام المنصة فإنك توافق على سياسة المقعد المدفوع وآلية مراجعة الإعلانات وحماية البيانات.</p>'],
            ['key' => 'terms_en', 'value' => '<p>By using the platform, you agree to the paid seat policy, ad review workflow, and data protection rules.</p>'],
            ['key' => 'privacy_policy_ar', 'value' => '<p>تبقى البيانات الحساسة للمعلن مخفية ولا يتم كشفها إلا وفق صلاحيات واضحة داخل المنصة.</p>'],
            ['key' => 'privacy_policy_en', 'value' => '<p>Sensitive advertiser data remains hidden and is only disclosed under explicit platform permissions.</p>'],
            ['key' => 'contact_number', 'value' => '+96522223333'],
            ['key' => 'contact_mail', 'value' => 'support@business-investments.test'],
            ['key' => 'contact_phone', 'value' => '+96522223333'],
            ['key' => 'contact_email', 'value' => 'deals@business-investments.test'],
            ['key' => 'seat_price', 'value' => '2500.00'],
            ['key' => 'completed_deals_commission', 'value' => '7.50'],
            ['key' => 'packages_page_title_ar', 'value' => 'خطط الوصول للمستثمرين'],
            ['key' => 'packages_page_title_en', 'value' => 'Access plans for investors'],
            ['key' => 'packages_page_description_ar', 'value' => 'اختر الباقة المناسبة لأسلوب متابعتك للفرص الاستثمارية.'],
            ['key' => 'packages_page_description_en', 'value' => 'Choose the package that fits your opportunity tracking style.'],
            ['key' => 'copy_right', 'value' => "All rights reserved © {$year}"],
        ];

        foreach ($settings as $setting) {
            GeneralSetting::query()->create(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
