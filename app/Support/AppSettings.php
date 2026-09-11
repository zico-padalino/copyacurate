<?php

namespace App\Support;

use App\Models\Setting;

class AppSettings
{
    public const APP_NAME = 'app_name';

    public const COMPANY_NAME = 'company_name';

    public const TAGLINE = 'tagline';

    public static function appName(): string
    {
        return Setting::getValue(self::APP_NAME, 'Kabar Banten') ?: 'Kabar Banten';
    }

    public static function companyName(): string
    {
        return Setting::getValue(self::COMPANY_NAME, self::appName()) ?: self::appName();
    }

    public static function tagline(): string
    {
        return Setting::getValue(self::TAGLINE, 'Sistem Keuangan') ?: 'Sistem Keuangan';
    }

    /**
     * @param  array{app_name?: string, company_name?: string, tagline?: string}  $data
     */
    public static function update(array $data): void
    {
        if (array_key_exists('app_name', $data)) {
            Setting::setValue(self::APP_NAME, $data['app_name']);
        }

        if (array_key_exists('company_name', $data)) {
            Setting::setValue(self::COMPANY_NAME, $data['company_name']);
        }

        if (array_key_exists('tagline', $data)) {
            Setting::setValue(self::TAGLINE, $data['tagline']);
        }
    }
}
