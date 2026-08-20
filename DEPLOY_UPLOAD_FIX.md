# Upload / Deployment notes for servis kabul photo fix

This file includes the operational steps required to ensure file uploads work correctly and how to deploy the changes from the `fix/upload-500-logging` branch.

## What I changed
- Improved validation for uploaded images (allowed mime types and max size 5MB per image).
- Added robust image processing using Intervention Image and saving images as WebP in `storage/app/public/servisler/{id}`.
- Added detailed logging for failures and success.
- Ensured directories are created on the filesystem when needed.

## Quick server checks & fix commands
Run these on the server where the application is hosted (adjust php version path as needed):

1) Ensure storage link exists and permissions are correct

```bash
cd /path/to/project
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

2) PHP limits (increase if you expect large uploads)

Edit php.ini (e.g. /etc/php/8.1/fpm/php.ini) and set:

```
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

Then restart php-fpm:

```bash
sudo systemctl restart php8.1-fpm
```

3) Nginx config: allow larger request body

Add or update in your nginx server block or http block:

```
client_max_body_size 50M;
```

Test and reload nginx:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

4) If you use S3 or external disks

- Verify `.env` variables (AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET, AWS_DEFAULT_REGION) are correctly set and not expired.
- Ensure `FILESYSTEM_DISK` points to `s3` if using S3.

## How to test the change locally / staging
1) Switch to the fix branch:

```bash
git fetch origin
git checkout fix/upload-500-logging
```

2) Pull composer / npm if needed and build assets

```bash
composer install
npm install
npm run build
```

3) Create storage link and run local server

```bash
php artisan storage:link
php artisan serve
```

4) Open the Araç Kabul page, select small images (<=5MB), and submit. Check `storage/app/public/servisler/{id}` and `storage/logs/laravel.log` for entries.

## Deploy steps (manual)
1) Merge PR into `main` (or your release branch).
2) On the server, pull latest changes:

```bash
cd /path/to/project
git fetch origin
git checkout main
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
npm ci && npm run build
sudo systemctl restart php8.1-fpm
sudo systemctl reload nginx
```

3) Verify upload flow works in production with a small image.

## Rollback plan
If an issue occurs after deploy:

```bash
cd /path/to/project
git fetch origin
git checkout <previous-working-commit-or-tag>
git reset --hard <previous-working-commit-or-tag>
composer install
php artisan migrate --force
php artisan cache:clear
sudo systemctl restart php8.1-fpm
sudo systemctl reload nginx
```

## Notes
- Do not set `APP_DEBUG=true` in production. Use logs to diagnose issues.
- If image conversion fails for a specific mime type, ensure `intervention/image` supports it or install necessary system libraries (e.g., libwebp, imagick) on the server.

