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
