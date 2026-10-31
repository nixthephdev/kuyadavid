<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $pages = collect(['home', 'story', 'help', 'updates.index', 'transparency', 'prayers', 'contact'])
            ->map(fn ($name) => ['loc' => route($name), 'lastmod' => null]);

        $posts = Post::published()->get()->map(fn (Post $post) => [
            'loc' => route('updates.show', $post),
            'lastmod' => $post->updated_at->toAtomString(),
        ]);

        return response()
            ->view('seo.sitemap', ['urls' => $pages->concat($posts)])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain');
    }
}
