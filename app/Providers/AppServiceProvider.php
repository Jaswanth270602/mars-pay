<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Sitesetting;
use View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $host = $_SERVER['HTTP_HOST'] ??  '13.60.104.103';

            $company = Company::where('company_website', $host)
                            ->where('status_id', 1)
                            ->first();

            if ($company) {
                $sitesettings = Sitesetting::where('company_id', $company->id)->first();
                
                View::share('company_id', $company->id);
                View::share('company_name', $company->company_name);
                View::share('company_email', $company->company_email);
                View::share('company_address', $company->company_address);
                View::share('company_address_two', $company->company_address_two);
                View::share('support_number', $company->support_number);
                View::share('whatsapp_number', $company->whatsapp_number);
                View::share('company_website', $company->company_website);
                View::share('company_logo', $company->company_logo);
                View::share('news', $company->news);
                View::share('sender_id', $company->sender_id);
                View::share('color_start', $company->color_start);
                View::share('color_end', $company->color_end);
                View::share('chat_script', $company->chat_script);
                View::share('cdnLink', $company->cdn_link);
                View::share('registration_status', $sitesettings->registration_status ?? 0);
                View::share('facebook_link', $company->facebook_link);
                View::share('instagram_link', $company->instagram_link);
                View::share('twitter_link', $company->twitter_link);
                View::share('youtube_link', $company->youtube_link);
            }
        } catch (\Exception $e) {
            // If DB is not ready, log error but don't break the site
            \Log::error('AppServiceProvider boot error: ' . $e->getMessage());
        }
    }
}
