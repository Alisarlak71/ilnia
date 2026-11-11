<?php

// Persian validation messages for the rules this project uses.
// Rules missing here fall back to English (APP_FALLBACK_LOCALE=en); add them when needed.

return [
    'accepted' => ':attribute باید پذیرفته شود.',
    'array' => ':attribute معتبر نیست.',
    'boolean' => ':attribute معتبر نیست.',
    'confirmed' => 'تکرار :attribute یکسان نیست.',
    'date' => ':attribute تاریخ معتبری نیست.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min تا :max رقم باشد.',
    'distinct' => ':attribute تکراری است.',
    'email' => ':attribute باید یک ایمیل معتبر باشد.',
    'exists' => ':attribute انتخاب‌شده معتبر نیست.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute نباید خالی باشد.',
    'image' => ':attribute باید یک عکس باشد.',
    'in' => ':attribute انتخاب‌شده معتبر نیست.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'max' => [
        'array' => ':attribute نباید بیشتر از :max مورد باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بیشتر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max حرف باشد.',
    ],
    'mimes' => 'نوع فایل :attribute باید یکی از این‌ها باشد: :values.',
    'min' => [
        'array' => ':attribute باید دست‌کم :min مورد داشته باشد.',
        'file' => 'حجم :attribute باید دست‌کم :min کیلوبایت باشد.',
        'numeric' => ':attribute باید دست‌کم :min باشد.',
        'string' => ':attribute باید دست‌کم :min حرف باشد.',
    ],
    'numeric' => ':attribute باید عدد باشد.',
    'regex' => 'قالب :attribute معتبر نیست.',
    'required' => ':attribute را وارد کنید.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'string' => ':attribute باید :size حرف باشد.',
    ],
    'string' => ':attribute باید متن باشد.',
    'unique' => 'این :attribute قبلاً ثبت شده است.',
    'uploaded' => 'بارگذاری :attribute ناموفق بود.',

    'custom' => [
        'province' => ['required' => 'استان را انتخاب کنید.'],
    ],

    'attributes' => [
        'name' => 'نام',
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'mobile' => 'شمارهٔ موبایل',
        'recipient_name' => 'نام گیرنده',
        'province' => 'استان',
        'city' => 'شهر',
        'address_line' => 'نشانی',
        'postal_code' => 'کد پستی',
        'slug' => 'نامک',
        'sku' => 'کد کالا',
        'title' => 'عنوان',
        'price' => 'قیمت',
        'stock' => 'موجودی',
        'weight_grams' => 'وزن',
        'category_id' => 'دسته',
    ],
];
