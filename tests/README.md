# Admin Dashboard Tests - ملخص الاختبارات

## Summary / الملخص

تم إنشاء مجموعة شاملة من الاختبارات (Feature Tests) لتغطية جميع أجزاء لوحة التحكم الإدارية.

A comprehensive set of Feature Tests has been created to cover all parts of the admin dashboard.

## Test Files Created / الملفات المُنشأة

### 1. AuthControllerTest.php (19 tests)
- Login/Logout functionality
- Profile management
- Password updates
- Validation tests

### 2. HomeControllerTest.php (8 tests)
- Dashboard statistics
- Data visualization
- Growth calculations

### 3. AdminControllerTest.php (14 tests)
- CRUD operations for admins
- Block/unblock functionality
- Permission checks

### 4. UserControllerTest.php (16 tests)
- CRUD operations for users
- Wallet charging
- Notifications
- Status toggles

### 5. CategoryControllerTest.php (12 tests)
- CRUD operations for categories
- Status toggles
- Parent-child relationships

### 6. RolesControllerTest.php (11 tests)
- Role management
- Permission assignment
- Access control

### 7. GeneralSettingControllerTest.php (11 tests)
- General settings
- Social media settings
- Terms and Privacy policies

### 8. NotificationsControllerTest.php (8 tests)
- Notification management
- FCM token updates

**Total: 99 tests**

## Factories Created / الـ Factories المُنشأة

1. **AdminFactory.php** - لإنشاء بيانات تجريبية للـ Admins
2. **CategoryFactory.php** - لإنشاء بيانات تجريبية للـ Categories
3. **NotificationFactory.php** - لإنشاء بيانات تجريبية للـ Notifications
4. **UserFactory.php** - تم تحديثه لإضافة الحقول المطلوبة

## How to Run Tests / كيفية تشغيل الاختبارات

### Run all tests:
```bash
php artisan test
```

### Run specific test file:
```bash
php artisan test tests/Feature/AuthControllerTest.php
```

### Run specific test:
```bash
php artisan test --filter test_admin_can_login_with_valid_credentials
```

### Run with coverage:
```bash
php artisan test --coverage
```

## Test Coverage / التغطية

الاختبارات تغطي:
- ✅ Authentication & Authorization
- ✅ CRUD operations
- ✅ Business logic (wallet, notifications, etc.)
- ✅ Validation rules
- ✅ Access control
- ✅ Error handling
- ✅ Edge cases

## Notes / ملاحظات

1. جميع الاختبارات تستخدم `RefreshDatabase` لإعادة تعيين قاعدة البيانات
2. يتم استخدام `Storage::fake()` لتجنب رفع الملفات الفعلية
3. جميع الروابط تستخدم البادئة `/en/admin` للغة الإنجليزية
4. يتم إنشاء الصلاحيات والرولات تلقائياً في `setUp()` method

## Documentation / التوثيق

راجع `TESTING_DOCUMENTATION.md` للحصول على تفاصيل أكثر.
