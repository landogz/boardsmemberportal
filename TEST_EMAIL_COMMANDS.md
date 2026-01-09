# Test Email Commands

## Quick Test Commands

### Option 1: Using Tinker (Recommended)

```bash
php artisan tinker
```

Then in tinker, run:
```php
Mail::raw('This is a test email from Laravel Board Member Portal', function ($message) {
    $message->to('dianamarcia123@gmail.com')
            ->subject('Test Email - Board Member Portal');
});
```

### Option 2: One-liner Tinker Command

```bash
php artisan tinker --execute="Mail::raw('Test email from Laravel', function(\$m) { \$m->to('dianamarcia123@gmail.com')->subject('Test Email'); });"
```

### Option 3: Create a Test Route (Temporary)

Add to `routes/web.php` (remove after testing):
```php
Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email', function ($message) {
            $message->to('dianamarcia123@gmail.com')
                    ->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

Then visit: `https://landogz.alwaysdata.net/test-email`

### Option 4: Test with Mailable Class

If you have a mailable class, test it:
```bash
php artisan tinker --execute="Mail::to('dianamarcia123@gmail.com')->send(new \App\Mail\TestMail());"
```

## Check Email Configuration

```bash
# Check mail config
php artisan tinker --execute="print_r(config('mail'));"

# Check MAIL_MAILER
php artisan tinker --execute="echo config('mail.default');"

# Check MAIL_HOST
php artisan tinker --execute="echo config('mail.mailers.smtp.host');"
```

## Test on Your Server

SSH into your server and run:

```bash
cd /home/landogz/boardsmemberportal

# Test email
php artisan tinker --execute="Mail::raw('Test email from server', function(\$m) { \$m->to('dianamarcia123@gmail.com')->subject('Server Test Email'); }); echo 'Email sent!';"
```

## Expected Output

If successful:
- No error message
- Email should arrive in your Gmail inbox within a few seconds

If failed:
- You'll see an error message with details
- Check the error message for authentication issues

## Troubleshooting

If you get authentication errors:
1. Make sure you're using Gmail App Password (not regular password)
2. Check `.env` file has correct settings
3. Clear config cache: `php artisan config:clear && php artisan config:cache`

