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
        if (! $this->canBootConfig()) {
            return;
        }
        $appUrl = Setting::getValue('app_url', env('APP_URL'));
        if ($appUrl) {
            config(['app.url' => $appUrl]);
        }

        $mailScheme = Setting::getValue('mail_scheme', env('MAIL_SCHEME'));
        $mailHost = Setting::getValue('mail_host', env('MAIL_HOST', '127.0.0.1'));
        $mailPort = (int) (Setting::getValue('mail_port', (string) env('MAIL_PORT', 2525)));
        $mailEncryption = Setting::getValue('mail_encryption', env('MAIL_ENCRYPTION'));
        $mailUsername = Setting::getValue('mail_username', env('MAIL_USERNAME'));
        $mailPassword = Setting::getValue('mail_password', env('MAIL_PASSWORD'));
        $mailFromAddress = Setting::getValue('mail_from_address', env('MAIL_FROM_ADDRESS'));
        $mailFromName = Setting::getValue('mail_from_name', env('MAIL_FROM_NAME', config('app.name')));

        config([
            'mail.mailers.smtp.scheme' => $mailScheme,
            'mail.mailers.smtp.host' => $mailHost,
            'mail.mailers.smtp.port' => $mailPort,
            'mail.mailers.smtp.encryption' => $mailEncryption,
            'mail.mailers.smtp.username' => $mailUsername,
            'mail.mailers.smtp.password' => $mailPassword,
            'mail.from.address' => $mailFromAddress,
            'mail.from.name' => $mailFromName,
        ]);

        $googleClientId = Setting::getValue('google_client_id', env('GOOGLE_CLIENT_ID'));
        $googleClientSecret = Setting::getValue('google_client_secret', env('GOOGLE_CLIENT_SECRET'));
        $googleRedirect = Setting::getValue('google_redirect', env('GOOGLE_REDIRECT'));

        config([
            'services.google.client_id' => $googleClientId,
            'services.google.client_secret' => $googleClientSecret,
            'services.google.redirect' => $googleRedirect,
        ]);

        $lencoBaseUrl = Setting::getValue('lenco_base_url', env('LENCO_BASE_URL'));
        $lencoKey = Setting::getValue('lenco_key', env('LENCO_KEY'));
        $lencoSecret = Setting::getValue('lenco_secret', env('LENCO_SECRET'));
        $lencoWebhookSecret = Setting::getValue('lenco_webhook_secret', env('LENCO_WEBHOOK_SECRET'));

        config([
            'services.lenco.base_url' => $lencoBaseUrl,
            'services.lenco.key' => $lencoKey,
            'services.lenco.secret' => $lencoSecret,
            'services.lenco.webhook_secret' => $lencoWebhookSecret,
        ]);
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
