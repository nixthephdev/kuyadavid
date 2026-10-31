<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\ShopItem;
use App\Support\ImageStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Imports what the family provided in brand/:
 *   brand/donate.txt           "Label: value" lines (see brand/donate.example.txt)
 *   brand/gcash-qr.*, bank-qr.*, bank2-qr.*   QR codes for each way to give
 *   brand/photos/*             hero.* becomes the main photo, the rest go in the story
 *   brand/photos/captions.txt  "file name | description | caption" lines (optional)
 *   brand/proof/*              medical documents, with personal details covered
 *   brand/shop/*               photos/price lists for "Buy from the family" (text in content/site.php)
 *   brand/videos/*.mp4         short reels for the story page, each with a same-name .jpg poster
 *   brand/videos/captions.txt  "file name | caption" lines (optional)
 *
 * Each part is imported once, so running the seeder again does not duplicate anything.
 */
class BrandSeeder extends Seeder
{
    private const DONATE_LABELS = [
        'gcash name' => 'gcash_name',
        'gcash number' => 'gcash_number',
        'bank name' => 'bank_name',
        'bank account name' => 'bank_account_name',
        'bank account number' => 'bank_account_number',
        'bank other details' => 'bank_extra',
        'bank 2 name' => 'bank2_name',
        'bank 2 account name' => 'bank2_account_name',
        'bank 2 account number' => 'bank2_account_number',
        'gofundme link' => 'gofundme_url',
        'gofundme organizer' => 'gofundme_organizer',
        'paypal email' => 'paypal_email',
        'paypal link' => 'paypal_link',
        'facebook page' => 'facebook_url',
    ];

    private const QR_CODES = ['gcash-qr' => 'gcash_qr', 'bank-qr' => 'bank_qr', 'bank2-qr' => 'bank2_qr'];

    public function run(ImageStore $images): void
    {
        // Resizing many large photos in one run needs more than the usual 128 MB on shared hosting.
        @ini_set('memory_limit', '512M');

        $this->importDonationDetails();
        $this->importQrCodes($images);
        $this->importPhotos($images);
        $this->importProof($images);
        $this->importShop($images);
        $this->importVideos($images);
    }

    private function importDonationDetails(): void
    {
        $file = base_path('brand/donate.txt');
        if (! is_file($file)) {
            $this->command?->warn('brand/donate.txt not found: donation details left empty.');

            return;
        }

        $values = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || ! str_contains($line, ':')) {
                continue;
            }
            [$label, $value] = array_map('trim', explode(':', $line, 2));
            if (($key = self::DONATE_LABELS[strtolower($label)] ?? null) && $value !== '') {
                $values[$key] = $value;
            }
        }

        // Same rules as the admin screen, so links are always safe https URLs.
        $validator = Validator::make($values, [
            'paypal_email' => ['nullable', 'email'],
            'paypal_link' => ['nullable', 'url:https'],
            'gofundme_url' => ['nullable', 'url:https'],
            'facebook_url' => ['nullable', 'url:https'],
        ]);

        foreach (array_keys($validator->errors()->messages()) as $bad) {
            $this->command?->warn("brand/donate.txt: skipped invalid {$bad}.");
            unset($values[$bad]);
        }

        Setting::put($values);
        $this->command?->info('Imported '.count($values).' donation details from brand/donate.txt.');
    }

    private function importQrCodes(ImageStore $images): void
    {
        foreach ($this->images('brand') as $file) {
            $key = self::QR_CODES[strtolower(pathinfo($file, PATHINFO_FILENAME))] ?? null;
            if ($key && ! Setting::get($key)) {
                // QR codes must stay sharp enough to scan.
                Setting::put($key, $images->store($file, 'donations', minQuality: 70));
                $this->command?->info('Imported '.basename($file).'.');
            }
        }
    }

    private function importPhotos(ImageStore $images): void
    {
        $files = $this->images('brand/photos');
        if (! $files) {
            $this->command?->warn('No photos in brand/photos/.');

            return;
        }
        if (Setting::get('hero_photo') || Setting::getJson('story_photos')) {
            $this->command?->info('Photos already imported; skipping brand/photos/.');

            return;
        }

        $captions = $this->captions('brand/photos/captions.txt', ['alt', 'caption']);
        $story = [];

        foreach ($files as $file) {
            $name = basename($file);
            if (str_starts_with(strtolower($name), 'hero') && ! Setting::get('hero_photo')) {
                Setting::put('hero_photo', $images->store($file, 'site'));
                if (isset($captions[$name]['alt'])) {
                    Setting::put('hero_alt', $captions[$name]['alt']);
                }

                continue;
            }

            $story[] = [
                'src' => $images->store($file, 'story'),
                'alt' => $captions[$name]['alt'] ?? 'A photo from David\'s family',
                'caption' => $captions[$name]['caption'] ?? '',
            ];
        }

        // No hero.* file: use the first photo as the main photo too.
        if (! Setting::get('hero_photo') && $story) {
            Setting::put('hero_photo', $images->store($files[0], 'site'));
        }

        Setting::put('story_photos', $story);
        $this->command?->info('Imported '.count($files).' photos from brand/photos/.');
    }

    private function importProof(ImageStore $images): void
    {
        $files = $this->images('brand/proof');
        if (! $files || Setting::getJson('medical_proof')) {
            return;
        }

        // Documents need readable text, so keep quality higher.
        Setting::put('medical_proof', array_map(fn ($f) => $images->store($f, 'proof', minQuality: 60), $files));
        $this->command?->info('Imported '.count($files).' medical documents from brand/proof/.');
    }

    private function importShop(ImageStore $images): void
    {
        $items = (require __DIR__.'/content/site.php')['shop'] ?? [];
        if (! $items || ShopItem::count() > 0) {
            return;
        }

        foreach (array_values($items) as $i => $item) {
            $photo = isset($item['photo']) && is_file($path = base_path('brand/shop/'.$item['photo']))
                ? $images->store($path, 'shop')
                : null;

            ShopItem::create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'photo' => $photo,
                'sort_order' => $i,
            ]);
        }

        $this->command?->info('Added '.count($items).' "Buy from the family" items.');
    }

    private function importVideos(ImageStore $images): void
    {
        $files = glob(base_path('brand/videos').'/*.mp4') ?: [];
        if (! $files || Setting::getJson('story_videos')) {
            return;
        }

        sort($files, SORT_NATURAL);
        $captions = $this->captions('brand/videos/captions.txt', ['caption']);
        $videos = [];

        foreach ($files as $file) {
            $name = basename($file);
            $target = 'videos/'.Str::lower((string) Str::ulid()).'.mp4';
            Storage::disk('public')->put($target, file_get_contents($file));

            $poster = preg_replace('/\.mp4$/', '.jpg', $file);
            $videos[] = [
                'src' => $target,
                'poster' => is_file($poster) ? $images->store($poster, 'videos') : null,
                'caption' => $captions[$name]['caption'] ?? '',
            ];
        }

        Setting::put('story_videos', $videos);
        $this->command?->info('Imported '.count($videos).' videos from brand/videos/.');
    }

    /** JPG/PNG/WEBP files in a brand/ folder, in natural order. */
    private function images(string $dir): array
    {
        $files = array_filter(glob(base_path($dir).'/*') ?: [], fn ($f) => is_file($f)
            && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true));
        sort($files, SORT_NATURAL);

        return array_values($files);
    }

    /**
     * Reads "file name | value | value" lines.
     *
     * @return array<string, array<string, string>>
     */
    private function captions(string $path, array $fields): array
    {
        $file = base_path($path);
        if (! is_file($file)) {
            return [];
        }

        $captions = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) >= 2 && ! str_starts_with($parts[0], '#')) {
                $values = array_combine($fields, array_pad(array_slice($parts, 1, count($fields)), count($fields), ''));
                $captions[$parts[0]] = array_filter($values);
            }
        }

        return $captions;
    }
}
