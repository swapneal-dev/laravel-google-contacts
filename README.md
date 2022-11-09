
# Fetch contacts from Google Contacts Using Google People API

# Installation

Install using composer<br>
```bash
composer require swapneal-dev/laravel-google-contacts
```
You must publish the configuration with this command:

```
php artisan vendor:publish --provider="SwapnealDev\GoogleContact\GoogleContactServiceProvider"
```

1. Setup oauth config in `google-contact.php`
2. Run migration
3. add your access key to `google_access_token` column in users table.
4. add calendar id to `google_calender_id` column in users table.
5. add trait to user model `SwapnealDev\GoogleContact\traits\HasGoogleToken`

# I have created an example project for google contacts to know implementation of this package.
https://github.com/swapneal-dev/google-calender-and-contacts

Now you are good to go.
