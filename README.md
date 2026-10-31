# Help David

A fundraising and story website for David, a Filipino husband fighting a serious rare illness,
and his wife who cares for him. It was built for free by a volunteer, with the family's permission.

Donations never pass through the site: visitors are shown the family's own GCash, bank and PayPal details.

- **Stack:** Laravel 12, Livewire 3, Alpine.js, Tailwind CSS 4 (Vite). SQLite locally, MySQL in production.
- **Hosting:** Hostinger shared hosting with PHP 8.4 and no Node on the server. See [DEPLOY.md](DEPLOY.md).
- **Before launch:** the family checks [TO-CONFIRM.md](TO-CONFIRM.md).

## Run it locally

```bash
composer install
npm ci
cp .env.example .env && php artisan key:generate
# set ADMIN_EMAIL / ADMIN_PASSWORD in .env
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoContentSeeder   # optional sample updates/prayers, local only
php artisan storage:link
npm run build        # or: npm run dev
php artisan serve    # http://127.0.0.1:8000 — admin at /admin
```

## Content

| Where | What |
|---|---|
| `brand/` | The family's real material (story, donation details, photos, medical proof). See [brand/README.md](brand/README.md). |
| `database/seeders/content/site.php` | The starting website copy. Unknown facts are marked `[TO CONFIRM]`. |
| `lang/en/site.php`, `lang/fil/site.php` | Button and heading text in English and Filipino. |
| `/admin` | Everything the family edits after launch. |

Photos (uploads and `brand/photos`) are converted to WebP at 1600px and 800px wide, kept under
200 KB, and stripped of EXIF/GPS data. Pages use `srcset` and lazy loading.

## Tests

```bash
php artisan test        # feature + unit tests (prayer wall, progress math, admin access, contact form, uploads, SEO)
npx playwright test     # browser tests at 390×844 and 1280×800: no console errors, no sideways scroll, key flows
```

Playwright starts its own server on port 8123, using `database/e2e.sqlite`, the real `brand/` content
and some sample updates and prayers. Screenshots are saved to `e2e/screenshots/`. Each run copies the
brand images into `storage/app/public` again. To clean up locally, empty that folder (keep its
`.gitignore`) and run `php artisan migrate:fresh --seed`.

## Structure

- `app/Http/Controllers/PageController.php`: public pages (Blade, no Livewire unless a page has a form)
- `app/Livewire/PrayerWall.php`, `ContactForm.php`: public forms (honeypot + rate limit)
- `app/Livewire/Admin/*`: the admin screens
- `app/Support/ImageStore.php`: image resizing and WebP conversion
- `app/Support/Fundraising.php`: progress bar math
- `resources/js/site.js` (Alpine only) and `resources/js/app.js` (Livewire + Alpine): the light bundle on most pages, the full one only where a form needs it
