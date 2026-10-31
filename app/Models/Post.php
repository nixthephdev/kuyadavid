<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** A journal entry, shown on the site as an "Update". */
class Post extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'photos', 'published_at'];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if (blank($post->slug)) {
                $post->slug = static::uniqueSlug($post->title);
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'update';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())->latest('published_at');
    }

    public function excerpt(int $limit = 160): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', $this->body), $limit);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
