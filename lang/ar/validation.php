<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other يساوي :value.',
    'active_url' => 'حقل :attribute ليس رابطًا صالحًا.',
    'after' => 'يجب أن يكون :attribute تاريخًا لاحقًا عن :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخًا لاحقًا أو مساويًا للتاريخ :date.',
    'alpha' => 'يجب أن يحتوي :attribute على حروفٍ فقط.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على حروف وأرقام وواصلات وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخًا سابقًا أو مساويًا للتاريخ :date.',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون :attribute بين :min و :max أحرف.',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute نعم أو لا.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'حقل كلمة المرور الحالية مطلوب.',
    'date' => 'حقل :attribute ليس تاريخًا صالحًا.',
    'date_equals' => 'يجب أن يكون :attribute تاريخًا مطابقًا للتاريخ :date.',
    'date_format' => ' :attribute لا يتطابق مع التنسيق :format.',
    'declined' => 'يجب رفض :attribute.',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other يساوي :value.',
    'different' => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits' => 'يجب أن يتكون :attribute من :digits رقماً.',
    'digits_between' => ':attribute يجب أن يكون ما بين :min و :max رقماً.',
    'dimensions' => 'أبعاد الصورة في :attribute غير صالحة.',
    'distinct' => 'لـ :attribute قيمة مكررة.',
    'email' => 'يجب أن يكون :attribute بريداً إلكترونياً صالحاً.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum' => 'قيمة :attribute المختارة غير صالحة.',
    'exists' => 'قيمة :attribute غير صالحة أو غير موجودة.',
    'file' => 'يجب أن يكون :attribute ملفًا.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون طول :attribute أكثر من :value أحرف.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون طول :attribute أكثر من أو يساوي :value أحرف.',
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'قيمة :attribute المختارة غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => ':attribute يجب أن يكون رقماً صحيحاً.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صالحًا.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالحًا.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالحًا.',
    'json' => 'يجب أن يكون :attribute نص JSON صالحًا.',
    'lt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.',
        'string' => 'يجب أن يكون طول :attribute أقل من :value أحرف.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من أو تساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون طول :attribute أقل من أو يساوي :value أحرف.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صالحًا.',
    'max' => [
        'numeric' => 'يجب ألا تزيد قيمة :attribute عن :max.',
        'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.',
        'string' => 'يجب ألا يزيد طول :attribute عن :max حرفًا.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفاً من الأنواع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب ألا تقل قيمة :attribute عن :min.',
        'file' => 'يجب ألا يقل حجم :attribute عن :min كيلوبايت.',
        'string' => 'يجب ألا يقل طول :attribute عن :min حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على :min عناصر على الأقل.',
    ],
    'multiple_of' => 'يجب أن يكون :attribute من مضاعفات :value.',
    'not_in' => 'قيمة :attribute المختارة غير صالحة.',
    'not_regex' => 'تنسيق :attribute غير صالح.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute موجودًا.',//->ignore($this->category->id)
    'prohibited' => 'حقل :attribute محظور.',//->ignore($this->category->id)
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other يساوي :value.',
    'prohibited_unless' => 'حقل :attribute محظور إلا إذا كان :other موجودًا في :values.',
    'prohibits' => 'حقل :attribute يمنع وجود :other.',
    'regex' => 'تنسيق :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'حقل :attribute يجب أن يحتوي على القيم التالية: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other موجودًا في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :values موجودًا.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون جميع القيم :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجودًا.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من القيم :values موجودة.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'string' => 'يجب أن يكون طول :attribute :size أحرف.',
        'array' => 'يجب أن يحتوي :attribute على :size عناصر.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صالحًا.',
    'unique' => ':attribute مستخدم من قبل.',
    'uploaded' => 'فشل رفع :attribute.',
    'url' => 'يجب أن يكون :attribute عنوان URL صالحًا.',
    'uuid' => 'يجب أن يكون :attribute UUID صالحًا.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'agreed_to_terms' => [
            'accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'phone_international' => 'يجب إدخال رقم جوال دولي صالح يبدأ بـ + متبوعاً برقم الدولة والرقم (بدون مسافات).',

    'unique_role_title_locale' => 'قيمة :locale مستخدمة بالفعل لدور آخر.',

    'attributes' => [
        'role' => 'الدور',
        'image' => 'الصورة الشخصية',
        'name' => 'الاسم',
        'phone' => 'رقم الجوال',
        'phone_local' => 'رقم الجوال (بدون المفتاح)',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'today' => 'اليوم',
        'account_number' => 'رقم الحساب',
        'IBAN' => 'رقم الحساب الايبان',
        'investor_type' => 'نوع المستثمر',
        'investor_experience' => 'خبرة الاستثمار',
        'experience_level' => 'مستوى الخبرة',
        'capital' => 'رأس المال',
        'available_capital' => 'رأس المال المتاح',
        'preferred_sector_id' => 'المجال المفضل',
        'category_id' => 'قطاع التركيز',
        'previous_investments_count' => 'عدد الاستثمارات السابقة',
        'first_name' => 'الاسم الأول',
        'investor_id' => 'معرف المستثمر',
        'last_name' => 'اسم العائلة',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'country_code' => 'رمز الدولة',
        'company_license' => 'ترخيص الشركة',
        'agreed_to_terms' => 'الموافقة على الشروط',
        'title_ar' => 'اسم الدور (العربية)',
        'title_en' => 'اسم الدور (الإنجليزية)',
        'title.ar' => 'اسم الدور (العربية)',
        'title.en' => 'اسم الدور (الإنجليزية)',
        'otp' => 'كود التحقق',
        'otp_code' => 'كود التحقق',
        'verification_code' => 'رمز التحقق',
        'code' => 'الرمز',
        'new_email' => 'البريد الإلكتروني الجديد',
        'current_email' => 'البريد الإلكتروني الحالي',
        'email_change_token' => 'رمز تغيير البريد الإلكتروني',
        'email_verification_code' => 'رمز التحقق من البريد الإلكتروني',
        'change_email_code' => 'رمز تغيير البريد الإلكتروني',
        'current_password' => 'كلمة المرور الحالية',
        'password' => 'كلمة المرور',
        'status' => 'الحالة',
    ],

];
