# Deploying to Hostinger (shared hosting)

This guide puts the site live on a Hostinger shared plan. No Node.js is needed on the server:
the built CSS/JS in `public/build/` is committed to the repository.

The same steps work on most cPanel hosts. Only the menu names differ.

**You need:** a Hostinger plan with SSH access (Premium or Business), a domain, and about 30 minutes.

---

## 0. Before you upload (on your computer)

```bash
npm ci
npm run build            # updates public/build/, which you then commit
php artisan test         # PHP tests
npx playwright test      # browser tests (phone + desktop)
git add -A && git commit -m "Build for deploy"
```

Then put the family's real content in `brand/` (see `brand/README.md`) and check
`TO-CONFIRM.md` with the family.

> `brand/proof/` is **not** committed to git, because it holds private documents.
> Upload those files by hand (step 6), or add them later in **/admin → Story, photos and headline**.

## 1. Set the PHP version

hPanel → **Websites → Manage → Advanced → PHP Configuration**:

- Choose **PHP 8.4**.
- Under *PHP extensions*, make sure these are ticked: `gd`, `fileinfo`, `pdo_mysql`, `mbstring`, `intl`, `zip`.
  They are usually on by default.

## 2. Create the database

hPanel → **Databases → MySQL Databases**: create a database and a user.
Write down the database name, user and password (they look like `u123456789_david`).

## 3. Upload the code

The app lives **outside** `public_html`. Only the `public/` folder is visible to the web.

Connect with SSH (hPanel → **Advanced → SSH Access** shows the command and port):

```bash
ssh -p 65002 u123456789@your-server-ip
cd ~/domains/your-domain.com

# Option A: from git
git clone https://github.com/<you>/<repo>.git david

# Option B: no git. Upload a zip of the project with hPanel File Manager into
# ~/domains/your-domain.com/, then:
#   unzip david.zip -d david
```

Install the PHP packages on the server. Composer 2 is already installed on Hostinger:

```bash
cd ~/domains/your-domain.com/david
composer install --no-dev --optimize-autoloader
```

> If `composer` is not available, run `composer install --no-dev --optimize-autoloader` on your
> computer and upload the `vendor/` folder too (zip it first, it is big).

## 4. Point the domain at `/public` (document root)

Replace `public_html` with a link to the app's `public/` folder:

```bash
cd ~/domains/your-domain.com
mv public_html public_html_old          # keep the old one until the site works
ln -s david/public public_html
```

Now `https://your-domain.com` serves `david/public/index.php`, and `.env`, `vendor/`,
`storage/` and `brand/` cannot be reached from the web.

<details>
<summary>No SSH? Use this fallback instead</summary>

Upload the whole project **into** `public_html/`, then create `public_html/.htaccess` with:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(\.env|\.git|composer\.(json|lock)|artisan|brand|storage|vendor) - [F,L,NC]
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

This sends every request into `public/` and blocks the private files. The symlink above is
still the better option.
</details>

## 5. Create `.env`

```bash
cd ~/domains/your-domain.com/david
cp .env.example .env
nano .env
```

Set at least:

```dotenv
APP_NAME="Help David"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_david
DB_USERNAME=u123456789_david
DB_PASSWORD=the-database-password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

# Email: create a mailbox in hPanel → Emails (for example noreply@your-domain.com)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=the-mailbox-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Where contact-form messages go (the family's own email)
FAMILY_EMAIL=family-email@example.com

# First admin login (you can remove the password from .env after seeding)
ADMIN_NAME="David's family"
ADMIN_EMAIL=family-email@example.com
ADMIN_PASSWORD=a-long-password-you-share-privately
```

Then:

```bash
php artisan key:generate --force
```

> Never set `APP_DEBUG=true` on the live site. It shows private details when an error happens.

## 6. Database, content and storage

```bash
php artisan migrate --force
php artisan db:seed --force        # admin account + site copy + everything in brand/
php artisan storage:link           # makes uploaded photos visible at /storage/...
```

- If `storage:link` fails because `symlink()` is disabled, make the link by hand:
  `ln -s ../storage/app/public public/storage`
- If you uploaded medical documents by hand, put them in `brand/proof/` **before** `db:seed`.
- Run `db:seed` once only. Later changes happen in `/admin`.
- **Never** run `DemoContentSeeder` on the live site. It adds sample text, and it refuses to run in production anyway.

Make the app fast:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Make sure the web server can write to these folders (Hostinger's defaults are usually fine):

```bash
chmod -R 775 storage bootstrap/cache
```

## 7. SSL (https)

hPanel → **Security → SSL**: install the free SSL certificate for the domain (and `www`).
Then turn on **Force HTTPS**. Wait a few minutes, then open `https://your-domain.com`.

Check:

- [ ] Home page loads, the lock icon shows, and there are no warnings.
- [ ] `https://your-domain.com/admin` asks for a login, and the family can sign in.
- [ ] Send a test message from **Contact**. It arrives at `FAMILY_EMAIL` and shows in **/admin → Contact messages**.
- [ ] Leave a test prayer, approve it in the admin, then delete it.
- [ ] Paste the link into Facebook's [Sharing Debugger](https://developers.facebook.com/tools/debug/)
      and press **Scrape Again**. The hero photo, title and description should appear.
- [ ] Run [PageSpeed Insights](https://pagespeed.web.dev/) on the home page (mobile). The target is 90 or more.
- [ ] Submit `https://your-domain.com/sitemap.xml` in Google Search Console (optional).

Afterwards you can delete `public_html_old`.

## 8. Backups

Things to keep safe: **the database** and **uploaded photos** (`storage/app/public/`).

1. **Automatic:** hPanel → **Files → Backups**. Hostinger keeps weekly (Premium) or daily (Business)
   backups. Check once that a backup exists.
2. **Monthly manual copy** (keep it on your own computer or Google Drive):
   - hPanel → **Databases → phpMyAdmin → Export → Quick → SQL**.
   - File Manager → `domains/your-domain.com/david/storage/app/public` → right-click → **Compress** → download.
3. **Optional scheduled database dump:** hPanel → **Advanced → Cron Jobs**, weekly:

   ```bash
   mkdir -p ~/backups && mysqldump -u u123456789_david -p'the-database-password' u123456789_david | gzip > ~/backups/david-$(date +\%F).sql.gz && find ~/backups -name 'david-*.sql.gz' -mtime +60 -delete
   ```

To restore: import the `.sql` file in phpMyAdmin, and upload the photos back into `storage/app/public/`.

## 9. Updating the site later

On your computer:

```bash
npm run build && php artisan test
git add -A && git commit -m "…" && git push
```

On the server:

```bash
cd ~/domains/your-domain.com/david
php artisan down --refresh=15
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## Troubleshooting

| Problem | Fix |
|---|---|
| "500 Server Error" | Check `storage/logs/laravel.log`. Usually a wrong DB password in `.env`, or `storage/` is not writable. After editing `.env`, run `php artisan config:cache` again. |
| Photos don't show | `public/storage` link is missing: run step 6 again. |
| Page has no styling | `public/build/` was not uploaded. Run `npm run build` locally and upload/commit it. |
| Contact emails don't arrive | Check the `MAIL_*` values and look in spam. Messages are still saved in **/admin → Contact messages**. |
| "419 Page expired" on login | The browser was open too long. Reload the page and try again. |
| Photo upload fails | The file must be JPG/PNG/WEBP under 5 MB. In hPanel → PHP Configuration, `upload_max_filesize` should be at least 8M. |
