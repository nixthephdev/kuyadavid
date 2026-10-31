<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Post;
use App\Models\Prayer;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * SAMPLE content for local previews and browser tests only.
 * Never run this in production: php artisan db:seed --class=DemoContentSeeder
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Refusing to add sample content in production.');

            return;
        }

        // Sample progress only. Donation details are never faked: they come from brand/donate.txt.
        Setting::put([
            'goal_amount' => 500000,
            'raised_amount' => 137500,
            'progress_updated_at' => now()->toIso8601String(),
        ]);

        foreach (range(1, 4) as $i) {
            Post::create([
                'title' => "Sample update {$i} (demo only)",
                'body' => "This is sample text for a preview of the Updates page. The family will write real updates in the admin area.\n\nA second paragraph shows how longer updates look on a phone.",
                'published_at' => now()->subDays(10 * $i),
            ]);
        }

        Expense::create(['spent_on' => now()->subDays(5), 'item' => 'Sample: medicine for one month', 'amount' => 12500, 'note' => 'Demo entry']);
        Expense::create(['spent_on' => now()->subDays(20), 'item' => 'Sample: laboratory tests', 'amount' => 8350.50]);

        $samples = [
            ['Maria', 'Philippines', 'Sample prayer: praying for strength and healing for you both.'],
            [null, 'Canada', 'Sample message: you are not alone. Sending love from far away.'],
            ['John', 'United States', 'Sample prayer: may God give your wife rest and peace.'],
        ];
        foreach ($samples as [$name, $country, $message]) {
            Prayer::create(compact('name', 'country', 'message') + ['approved_at' => now()]);
        }
        Prayer::create(['name' => 'Pending visitor', 'country' => 'Japan', 'message' => 'Sample message waiting for approval.']);
    }
}
