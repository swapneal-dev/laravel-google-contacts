<?php

namespace SwapnealDev\GoogleContact;

use Illuminate\Support\ServiceProvider;
use SwapnealDev\GoogleContact\Exceptions\InvalidConfiguration;

class GoogleContactServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/google-contact.php' => config_path('google-contact.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/google-contact.php', 'google-contact');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->app->bind(GoogleContact::class, function () {
            $config = config('google-contact');

            $this->guardAgainstInvalidConfiguration($config);

            return GoogleContactFactory::create();
        });

        $this->app->alias(GoogleContact::class, 'laravel-google-contact');
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (!auth()->check()) {
            throw InvalidConfiguration::authenticationRequired();
        }

        $authProfile = $config['default_auth_profile'];

        if ($authProfile === 'oauth') {
            $this->validateOAuthConfigSettings($config);

            return;
        }

        throw InvalidConfiguration::invalidAuthenticationProfile($authProfile);
    }


    /**
     * @throws InvalidConfiguration
     */
    protected function validateOAuthConfigSettings(array $config = null)
    {
        $credentials = $config['auth_profiles']['oauth']['credentials_json'];

        $this->validateConfigSetting($credentials);

        $this->validateConfigSetting(auth()->user()->getGoogleAccessToken());
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function validateConfigSetting(string $setting)
    {
        if (! is_array($setting) && ! is_string($setting)) {
            throw InvalidConfiguration::credentialsTypeWrong($setting);
        }

        if (is_string($setting) && ! file_exists($setting)) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($setting);
        }
    }
}
