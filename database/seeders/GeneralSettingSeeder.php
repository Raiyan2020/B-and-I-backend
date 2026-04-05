<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $general_settings = array(
            array('key' => 'website_name_ar', 'value' => 'لوحة التحكم', 'created_at' => NULL, 'updated_at' => '2024-02-19 21:54:41'),
            array('key' => 'website_name_en', 'value' => 'Dashboard', 'created_at' => NULL, 'updated_at' => '2024-02-19 21:54:41'),
            array('key' => 'contact_number', 'value' => '966556565297', 'created_at' => NULL, 'updated_at' => '2024-02-19 21:54:41'),
            array('key' => 'contact_mail', 'value' => 'saudiaticket@gmail.com', 'created_at' => '2024-02-19 21:54:41', 'updated_at' => '2024-02-19 21:54:41'),
            array('key' => 'logo1', 'value' => 'logo.svg', 'created_at' => '2024-02-19 21:54:41', 'updated_at' => '2024-02-19 21:54:41'),
            array('key' => 'favicon2', 'value' => 'favicon.svg', 'created_at' => '2024-02-19 21:54:41', 'updated_at' => '2024-02-19 21:58:33'),
            array('key' => 'commercial_register', 'value' => '4031264892', 'created_at' => '2024-02-19 22:10:43', 'updated_at' => '2024-02-19 22:10:43'),
            array('key' => 'tax_number', 'value' => '311025770300003', 'created_at' => '2024-02-19 22:10:43', 'updated_at' => '2024-02-19 22:10:43'),
            array('key' => 'copy_right', 'value' => 'جميع الحقوق محفوظة © 2024', 'created_at' => '2024-02-19 22:14:46', 'updated_at' => '2024-02-19 22:14:46'),
            array('key' => 'twitter_links', 'value' => 'https://x.com/ticket_71?s=21&t=8TXEVn0zqOXiAm6TasfkUg', 'created_at' => '2024-02-19 22:50:59', 'updated_at' => '2024-02-19 22:50:59'),
            array('key' => 'whatsapp_link', 'value' => 'https://wa.me/message/V63V6XGEJHFCD1', 'created_at' => '2024-02-19 22:50:59', 'updated_at' => '2024-02-19 22:50:59'),
            array('key' => 'snap_link', 'value' => 'https://t.snapchat.com/o3OcXv11', 'created_at' => '2024-02-19 22:50:59', 'updated_at' => '2024-02-19 22:50:59'),
            array('key' => 'tiktok_link', 'value' => 'https://www.tiktok.com/ar/', 'created_at' => '2024-02-19 22:50:59', 'updated_at' => '2024-02-19 22:50:59'),
            array('key' => 'login_page_image3', 'value' => 'login_page_image3.webp', 'created_at' => '2024-02-24 23:51:52', 'updated_at' => '2024-02-24 23:51:52')
        );
        GeneralSetting::insert($general_settings);
    }
}
