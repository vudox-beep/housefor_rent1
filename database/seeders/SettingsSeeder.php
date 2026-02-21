<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue('app_url', env('APP_URL'));

        Setting::setValue('mail_scheme', null);
        Setting::setValue('mail_host', env('MAIL_HOST'));
        Setting::setValue('mail_port', (string) env('MAIL_PORT'));
        Setting::setValue('mail_encryption', env('MAIL_ENCRYPTION'));
        Setting::setValue('mail_username', env('MAIL_USERNAME'));
        Setting::setValue('mail_password', env('MAIL_PASSWORD'));
        Setting::setValue('mail_from_address', env('MAIL_FROM_ADDRESS'));
        Setting::setValue('mail_from_name', env('MAIL_FROM_NAME'));

        Setting::setValue('google_client_id', env('GOOGLE_CLIENT_ID'));
        Setting::setValue('google_client_secret', env('GOOGLE_CLIENT_SECRET'));
        Setting::setValue('google_redirect', env('GOOGLE_REDIRECT'));

        Setting::setValue('lenco_base_url', env('LENCO_BASE_URL'));
        Setting::setValue('lenco_key', env('LENCO_KEY'));
        Setting::setValue('lenco_secret', env('LENCO_SECRET'));
        Setting::setValue('lenco_webhook_secret', env('LENCO_WEBHOOK_SECRET'));
    }
}
