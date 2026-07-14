<?php

namespace App\Enums;

class GlobalConstant
{
    // Political Parties of Bangladesh
    



    public const MANUFACTURERS = [
        ['code' => 'own_factory','en_name' => 'Own Factory','bn_name' => 'নিজস্ব কারখানা','status' => 1],
        ['code' => 'outside_factory', 'en_name' => 'Outside Factory', 'bn_name' => 'বাইরের কারখানা', 'status' => 1],
        ['code' => 'local_carpenter', 'en_name' => 'Local Carpenter', 'bn_name' => 'স্থানীয় কাঠমিস্ত্রি', 'status' => 1],
    ];

    public const WOOD_TYPE = [
        ['code' => 'akashi', 'en_name' => 'Akashi', 'bn_name' => 'অকাশি', 'status' => 1],
        ['code' => 'shegun', 'en_name' => 'Shegun', 'bn_name' => 'সেগুন', 'status' => 1],
        ['code' => 'mango', 'en_name' => 'Mango', 'bn_name' => 'আম', 'status' => 1],
        ['code' => 'jackfruit', 'en_name' => 'Jackfruit', 'bn_name' => 'কাঁঠাল', 'status' => 1],
        ['code' => 'shishu', 'en_name' => 'Shishu', 'bn_name' => 'শিশু', 'status' => 1],
        ['code' => 'other', 'en_name' => 'Other', 'bn_name' => 'অন্যান্য', 'status' => 1],
    ];

    public const COLOR = [
        ['code' => 'dark_brown', 'en_name' => 'Dark Brown', 'bn_name' => 'গাঢ় বাদামী', 'status' => 1],
        ['code' => 'light_brown', 'en_name' => 'Light Brown', 'bn_name' => 'হালকা বাদামী', 'status' => 1],
        ['code' => 'black', 'en_name' => 'Black', 'bn_name' => 'কালো', 'status' => 1],
        ['code' => 'white', 'en_name' => 'White', 'bn_name' => 'সাদা', 'status' => 1],
        ['code' => 'other', 'en_name' => 'Other', 'bn_name' => 'অন্যান্য', 'status' => 1],
    ];

    public static function activeManufacturers()
    {
        return collect(self::MANUFACTURERS)
            ->where('status', 1)
            ->values();
    }

    public static function manufacturerName(?string $code): ?string
    {
        $manufacturer = collect(self::MANUFACTURERS)->firstWhere('code', $code);

        if (! $manufacturer) {
            return null;
        }

        return app()->getLocale() === 'bn' ? $manufacturer['bn_name'] : $manufacturer['en_name'];
    }

    public static function activeWoodTypes()
    {
        return collect(self::WOOD_TYPE)
            ->where('status', 1)
            ->values();
    }

    public static function woodTypeName(?string $code): ?string
    {
        $woodType = collect(self::WOOD_TYPE)->firstWhere('code', $code);

        if (! $woodType) {
            return null;
        }

        return app()->getLocale() === 'bn' ? $woodType['bn_name'] : $woodType['en_name'];
    }

    public static function activeColors()
    {
        return collect(self::COLOR)
            ->where('status', 1)
            ->values();
    }

    public static function colorName(?string $code): ?string
    {
        $color = collect(self::COLOR)->firstWhere('code', $code);

        if (! $color) {
            return null;
        }

        return app()->getLocale() === 'bn' ? $color['bn_name'] : $color['en_name'];
    }

    public const PAYMENT_METHOD = [
        ['code' => 'cash', 'en_name' => 'Cash', 'bn_name' => 'নগদ', 'status' => 1],
        ['code' => 'bkash', 'en_name' => 'bKash', 'bn_name' => 'বিকাশ', 'status' => 1],
        ['code' => 'nagad', 'en_name' => 'Nagad', 'bn_name' => 'নগদ (মোবাইল ব্যাংকিং)', 'status' => 1],
        ['code' => 'rocket', 'en_name' => 'Rocket', 'bn_name' => 'রকেট', 'status' => 1],
        ['code' => 'bank_transfer', 'en_name' => 'Bank Transfer', 'bn_name' => 'ব্যাংক ট্রান্সফার', 'status' => 1],
        ['code' => 'other', 'en_name' => 'Other', 'bn_name' => 'অন্যান্য', 'status' => 1],
    ];

    public static function activePaymentMethods()
    {
        return collect(self::PAYMENT_METHOD)
            ->where('status', 1)
            ->values();
    }

    public static function paymentMethodName(?string $code): ?string
    {
        $paymentMethod = collect(self::PAYMENT_METHOD)->firstWhere('code', $code);

        if (! $paymentMethod) {
            return null;
        }

        return app()->getLocale() === 'bn' ? $paymentMethod['bn_name'] : $paymentMethod['en_name'];
    }

    public static function getSymbolByCode(string $code): ?array
    {
        return collect(self::POLITICAL_SYMBOL)
            ->firstWhere('code', $code);
    }

    public static function activeSymbols()
    {
        return collect(self::POLITICAL_SYMBOL)
            ->where('status', 1)
            ->values();
    }

   
}
