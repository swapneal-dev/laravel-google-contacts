
# fetch contacts from Google Contacts Using Google People API

# Installation

You can install the package via composer:

composer require spatie/laravel-google-calendar
You must publish the configuration with this command:

```
php artisan vendor:publish --provider="SwapnealDev\GoogleContact\GoogleContactServiceProvider"
```

1. Setup oauth config in `google-contact.php`
2. Run migration
3. add your access key to `google_access_token` column in users table.
4. add calendar id to `google_calender_id` column in users table.
5. add trait to user model `SwapnealDev\GoogleContact\traits\HasGoogleToken`

Now you are good to go.
