<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Turns an uploaded (or brand/) photo into two WebP files on the public disk:
 * "{base}-lg.webp" (1600px wide) and "{base}-sm.webp" (800px wide).
 *
 * The returned "base" also carries the large size, e.g. "updates/01jb…-1600x1067",
 * so views can set width/height and avoid layout shift. Re-encoding drops EXIF
 * data, which removes any GPS location hidden inside phone photos.
 */
class ImageStore
{
    public function store(UploadedFile|string $source, string $folder, int $minQuality = 40): string
    {
        $path = $source instanceof UploadedFile ? $source->getRealPath() : $source;
        $manager = new ImageManager(new Driver);
        $config = config('site.images');

        // Very detailed images (posters, price lists) can stay over budget even at the
        // lowest quality, so make them a little smaller and try again.
        $size = $config['large'];
        do {
            $large = $manager->read($path)->scaleDown(width: $size, height: $size);
            $data = $this->encode($large, $config['max_bytes'], $minQuality);
            $size = (int) round($size * 0.85);
        } while (strlen($data) > $config['max_bytes'] && $size >= $config['small']);

        $base = $folder.'/'.Str::lower((string) Str::ulid()).'-'.$large->width().'x'.$large->height();
        Storage::disk('public')->put("{$base}-lg.webp", $data);

        $small = $manager->read($path)->scaleDown(width: $config['small'], height: $config['small']);
        Storage::disk('public')->put("{$base}-sm.webp", $this->encode($small, $config['max_bytes'], $minQuality));

        return $base;
    }

    public function delete(?string $base): void
    {
        if ($base) {
            Storage::disk('public')->delete(["{$base}-lg.webp", "{$base}-sm.webp"]);
        }
    }

    public static function url(?string $base, string $size = 'lg'): ?string
    {
        return $base ? Storage::disk('public')->url("{$base}-{$size}.webp") : null;
    }

    /** [width, height] of the large file, read from the base name. */
    public static function dimensions(?string $base): array
    {
        if ($base && preg_match('/-(\d+)x(\d+)$/', $base, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        return [null, null];
    }

    /** Lower the quality step by step until the file is under the size budget. */
    private function encode($image, int $maxBytes, int $minQuality): string
    {
        $quality = 80;

        do {
            $data = (string) $image->toWebp(quality: $quality);
            $quality -= 8;
        } while (strlen($data) > $maxBytes && $quality >= $minQuality);

        return $data;
    }
}
