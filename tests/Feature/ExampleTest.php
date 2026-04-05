<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // إعادة بناء قاعدة البيانات قبل كل اختبار
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/admin');
        $response->assertSee('login'); // ده بيدور علي الكلمه اللي مكتوبه بيشوها راجعه ف  الصفحه والا لا

        $response->assertStatus(200);
    }
}
