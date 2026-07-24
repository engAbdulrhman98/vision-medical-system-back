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

    'accepted' => 'يجب قبول الحقل :attribute.',
    'accepted_if' => 'يجب قبول الحقل :attribute عندما يكون :other مساوياً لـ :value.',
    'active_url' => 'الحقل :attribute ليس رابطاً صالحاً.',
    'after' => 'يجب أن يكون الحقل :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون الحقل :attribute تاريخاً مساوياً أو بعد :date.',
    'alpha' => 'يجب أن يحتوي الحقل :attribute على حروف فقط.',
    'alpha_dash' => 'يجب أن يحتوي الحقل :attribute على حروف، أرقام، شرطات، وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي الحقل :attribute على حروف وأرقام فقط.',
    'array' => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي الحقل :attribute على رموز وأرقام وحروف أحادية البايت فقط.',
    'before' => 'يجب أن يكون الحقل :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن يكون الحقل :attribute تاريخاً مساوياً أو قبل :date.',
    'between' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على ما بين :min و :max عنصراً.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول النص :attribute بين :min و :max حرفاً.',
    ],
    'boolean' => 'يجب أن تكون قيمة الحقل :attribute إما true أو false.',
    'confirmed' => 'تأكيد الحقل :attribute غير متطابق.',
    'contains' => 'الحقل :attribute يفتقد إلى قيمة مطلوبة.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'الحقل :attribute ليس تاريخاً صالحاً.',
    'date_equals' => 'يجب أن يكون الحقل :attribute تاريخاً مساوياً لـ :date.',
    'date_format' => 'الحقل :attribute لا يتطابق مع الصيغة :format.',
    'decimal' => 'يجب أن يحتوي الحقل :attribute على :decimal خانة عشرية.',
    'declined' => 'يجب رفض الحقل :attribute.',
    'declined_if' => 'يجب رفض الحقل :attribute عندما يكون :other مساوياً لـ :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits' => 'يجب أن يتكون الحقل :attribute من :digits خانة/أرقام.',
    'digits_between' => 'يجب أن يتكون الحقل :attribute بين :min و :max خانة/أرقام.',
    'dimensions' => 'الحقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'الحقل :attribute يحتوي على قيمة مكررة.',
    'doesnt_end_with' => 'يجب ألا ينتهي الحقل :attribute بأي مما يلي: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ الحقل :attribute بأي مما يلي: :values.',
    'email' => 'يجب أن يكون الحقل :attribute عنوان بريد إلكتروني صالحاً.',
    'ends_with' => 'يجب أن ينتهي الحقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'exists' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'extensions' => 'يجب أن يكون للملف :attribute أحد الامتدادات التالية: :values.',
    'file' => 'يجب أن يكون الحقل :attribute ملفاً.',
    'filled' => 'الحقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أكثر من :value عنصراً/عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول النص :attribute أكبر من :value حرفاً/حروف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :value عنصراً أو أكثر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من أو مساوياً لـ :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أكبر من أو مساوية لـ :value.',
        'string' => 'يجب أن يكون طول النص :attribute أكبر من أو مساوياً لـ :value حرفاً/حروف.',
    ],
    'hex_color' => 'يجب أن يكون الحقل :attribute لوناً سداسي عشرياً صالحاً.',
    'image' => 'يجب أن يكون الحقل :attribute صورة.',
    'in' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'in_array' => 'الحقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون الحقل :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون الحقل :attribute عنوان IP صالحاً.',
    'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 صالحاً.',
    'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 صالحاً.',
    'json' => 'يجب أن يكون الحقل :attribute نصاً بصيغة JSON صالحة.',
    'list' => 'يجب أن يكون الحقل :attribute قائمة.',
    'lt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أقل من :value عنصراً/عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أقل من :value.',
        'string' => 'يجب أن يكون طول النص :attribute أقل من :value حرفاً/حروف.',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :value عنصراً/عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أقل من أو مساوياً لـ :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أقل من أو مساوية لـ :value.',
        'string' => 'يجب أن يكون طول النص :attribute أقل من أو مساوياً لـ :value حرفاً/حروف.',
    ],
    'mac_address' => 'يجب أن يكون الحقل :attribute عنوان MAC صالحاً.',
    'max' => [
        'array' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max عنصراً.',
        'file' => 'يجب ألا يتعدى حجم الملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب ألا تكون قيمة الحقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يتعدى طول النص :attribute :max حرفاً/حروف.',
    ],
    'max_digits' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون الحقل :attribute ملفاً من النوع: :values.',
    'mimetypes' => 'يجب أن يكون الحقل :attribute ملفاً من النوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min من العناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute على الأقل :min.',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :min من الحروف.',
    ],
    'min_digits' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min من الأرقام.',
    'missing' => 'يجب أن يكون الحقل :attribute مفقوداً.',
    'missing_if' => 'يجب أن يكون الحقل :attribute مفقوداً عندما يكون :other مساوياً لـ :value.',
    'missing_unless' => 'يجب أن يكون الحقل :attribute مفقوداً إلا إذا كان :other مساوياً لـ :value.',
    'missing_with' => 'يجب أن يكون الحقل :attribute مفقوداً عندما يكون :values موجوداً.',
    'missing_with_all' => 'يجب أن يكون الحقل :attribute مفقوداً عندما تكون :values موجودة.',
    'multiple_of' => 'يجب أن يكون الحقل :attribute مضاعفاً للقيمة :value.',
    'not_in' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'not_regex' => 'صيغة الحقل :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون الحقل :attribute رقماً.',
    'password' => [
        'letters' => 'يجب أن يحتوي الحقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي الحقل :attribute على حرف كبير وحرف صغير واحد على الأقل.',
        'numbers' => 'يجب أن يحتوي الحقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي الحقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'الحقل المعطى :attribute ظهر في تسريب بيانات. يرجى اختيار :attribute مختلف.',
    ],
    'present' => 'يجب أن يكون الحقل :attribute موجوداً.',
    'present_if' => 'يجب أن يكون الحقل :attribute موجوداً عندما يكون :other مساوياً لـ :value.',
    'present_unless' => 'يجب أن يكون الحقل :attribute موجوداً إلا إذا كان :other مساوياً لـ :value.',
    'present_with' => 'يجب أن يكون الحقل :attribute موجوداً عندما يكون :values موجوداً.',
    'present_with_all' => 'يجب أن يكون الحقل :attribute موجوداً عندما تكون :values موجودة.',
    'prohibited' => 'الحقل :attribute محظور.',
    'prohibited_if' => 'الحقل :attribute محظور عندما يكون :other مساوياً لـ :value.',
    'prohibited_unless' => 'الحقل :attribute محظور إلا إذا كان :other في :values.',
    'prohibits' => 'الحقل :attribute يمنع :other من التواجد.',
    'regex' => 'صيغة الحقل :attribute غير صالحة.',
    'required' => 'الحقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي الحقل :attribute على مفاتيح لـ: :values.',
    'required_if' => 'الحقل :attribute مطلوب عندما يكون :other مساوياً لـ :value.',
    'required_if_accepted' => 'الحقل :attribute مطلوب عندما يتم قبول :other.',
    'required_if_declined' => 'الحقل :attribute مطلوب عندما يتم رفض :other.',
    'required_unless' => 'الحقل :attribute مطلوب إلا إذا كان :other مساوياً لـ :values.',
    'required_with' => 'الحقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all' => 'الحقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'الحقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'الحقل :attribute مطلوب عندما لا تكون :values موجودة.',
    'same' => 'يجب أن يتطابق الحقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :size من العناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يكون طول النص :attribute مساوياً لـ :size من الحروف.',
    ],
    'starts_with' => 'يجب أن يبدأ الحقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون الحقل :attribute نصاً.',
    'timezone' => 'يجب أن يكون الحقل :attribute منطقة زمنية صالحة.',
    'unique' => 'قيمة الحقل :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل تحميل الحقل :attribute.',
    'uppercase' => 'يجب أن يكون الحقل :attribute كبيراً (Uppercase).',
    'url' => 'يجب أن يكون الحقل :attribute رابطاً صالحاً.',
    'ulid' => 'يجب أن يكون الحقل :attribute معرف ULID صالحاً.',
    'uuid' => 'يجب أن يكون الحقل :attribute معرف UUID صالحاً.',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
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

    'attributes' => [
        'name' => 'الاسم',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'address' => 'العنوان',
        'phone' => 'الهاتف',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'sex' => 'الجنس',
        'gender' => 'النوع',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'الساعة',
        'minute' => 'الدقيقة',
        'second' => 'الثانية',
        'title' => 'العنوان',
        'content' => 'المحتوى',
        'description' => 'الوصف',
        'excerpt' => 'المقتطف',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'متاح',
        'size' => 'الحجم',
    ],
];
