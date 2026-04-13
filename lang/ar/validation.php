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

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => ' :attribute يجب أن يكون تاريخا لاحقا عن :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute نعم أو لا.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'حقل كلمة المرور الحالية مطلوب.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ' :attribute لا يتطابق مع التنسيق :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'يجب أن يتكون :attribute من :digits رقماً.',
    'digits_between' => ' :attribute يجب ان يكون ما بين :min و :max رقما.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'يجب أن يكون :attribute بريداً إلكترونياً صالحاً.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'قيمة :attribute المختارة غير صالحة.',
    'exists' => 'قيمة :attribute غير صالحة أو غير موجودة.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'قيمة :attribute المختارة غير صالحة.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute يجب أن يكون رقماً صحيحاً.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
        'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.',
        'string' => 'يجب ألا يزيد :attribute عن :max حرفاً.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عنصر.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفاً من الأنواع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'file' => 'يجب ألا يقل حجم :attribute عن :min كيلوبايت.',
        'string' => 'يجب ألا يقل :attribute عن :min حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على :min عنصراً على الأقل.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',//->ignore($this->category->id)
    'prohibited' => 'The :attribute field is prohibited.',//->ignore($this->category->id)
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'تنسيق :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ':attribute مستخدم من قبل.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

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
        'last_name' => 'اسم العائلة',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'country_code' => 'رمز الدولة',
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
