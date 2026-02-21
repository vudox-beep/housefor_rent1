<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingsConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        try {
            if (! $this->canBootConfig()) {
                return;
            }
            
            $appUrl = Setting::getValue('app_url');
            if ($appUrl) {
                config(['app.url' => $appUrl]);
            }

            $mailSettings = [
                'mail.mailers.smtp.scheme' => Setting::getValue('mail_scheme'),
                'mail.mailers.smtp.host' => Setting::getValue('mail_host'),
                'mail.mailers.smtp.port' => Setting::getValue('mail_port'),
                'mail.mailers.smtp.encryption' => Setting::getValue('mail_encryption'),
                'mail.mailers.smtp.username' => Setting::getValue('mail_username'),
                'mail.mailers.smtp.password' => Setting::getValue('mail_password'),
                'mail.from.address' => Setting::getValue('mail_from_address'),
                'mail.from.name' => Setting::getValue('mail_from_name'),
            ];

            foreach ($mailSettings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }

            $googleSettings = [
                'services.google.client_id' => Setting::getValue('google_client_id'),
                'services.google.client_secret' => Setting::getValue('google_client_secret'),
                'services.google.redirect' => Setting::getValue('google_redirect'),
            ];

            foreach ($googleSettings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }

            $lencoSettings = [
                'services.lenco.base_url' => Setting::getValue('lenco_base_url'),
                'services.lenco.key' => Setting::getValue('lenco_key'),
                'services.lenco.secret' => Setting::getValue('lenco_secret'),
                'services.lenco.webhook_secret' => Setting::getValue('lenco_webhook_secret'),
            ];

            foreach ($lencoSettings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
        } catch (\Throwable $e) {
            // Log error or silently fail to allow default config to work
            // \Illuminate\Support\Facades\Log::error('SettingsConfigServiceProvider failed: ' . $e->getMessage());
        }
    }

    protected function canBootConfig(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
