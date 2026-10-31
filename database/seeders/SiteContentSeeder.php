<?php

namespace Database\Seeders;

use App\Models\Need;
use App\Models\Setting;
use App\Models\TimelineEvent;
use Illuminate\Database\Seeder;

/** Fills in the website copy from database/seeders/content/site.php (and brand/story.txt). */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = require __DIR__.'/content/site.php';

        $storyFile = base_path('brand/story.txt');
        $story = $content['story_en']
            ?? (is_file($storyFile) ? trim(file_get_contents($storyFile)) : null)
            ?: $content['story_en_placeholder'];

        Setting::put([
            ...$content['settings'],
            'story_en' => $this->unindent($story),
            'story_fil' => $this->unindent($content['story_fil']),
        ]);

        if (TimelineEvent::count() === 0) {
            foreach ($content['timeline'] as $i => $event) {
                TimelineEvent::create([...$event, 'sort_order' => $i]);
            }
        }

        if (Need::count() === 0) {
            foreach ($content['needs'] as $i => $need) {
                Need::create([...$need, 'sort_order' => $i]);
            }
        }
    }

    /** Heredocs keep their indentation; strip it so paragraphs render cleanly. */
    private function unindent(string $text): string
    {
        return trim(preg_replace('/^[ \t]+/m', '', str_replace("\r\n", "\n", $text)));
    }
}
