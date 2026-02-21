<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class ApiConfig
{
    public static function mail(): array
    {
        return [
            'scheme' => Config::get('mail.mailers.smtp.scheme'),
            'host' => Config::get('mail.mailers.smtp.host'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'password' => Config::get('mail.mailers.smtp.password'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
        ];
    }

    public static function google(): array
    {
        return [
            'client_id' => Config::get('services.google.client_id'),
            'client_secret' => Config::get('services.google.client_secret'),
            'redirect' => Config::get('services.google.redirect'),
        ];
    }

    public static function maps(): array
    {
        return [
            'key' => Config::get('services.google_maps.key'),
        ];
    }

    public static function lenco(): array
    {
        return [
            'base_url' => Config::get('services.lenco.base_url'),
            'key' => Config::get('services.lenco.key'),
            'secret' => Config::get('services.lenco.secret'),
            'webhook_secret' => Config::get('services.lenco.webhook_secret'),
        ];
    }

    public static function missingKeys(): array
    {
        $missing = [];
        foreach (self::mail() as $k => $v) {
            if ($v === null || $v === '') {
                $missing[] = "mail.$k";
            }
        }
        foreach (self::google() as $k => $v) {
            if ($v === null || $v === '') {
                $missing[] = "google.$k";
            }
        }
        foreach (self::maps() as $k => $v) {
            if ($v === null || $v === '') {
                $missing[] = "maps.$k";
            }
        }
        foreach (self::lenco() as $k => $v) {
            if ($v === null || $v === '') {
                $missing[] = "lenco.$k";
            }
        }
        return $missing;
    }
}
