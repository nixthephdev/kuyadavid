<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Need;
use App\Models\Post;
use App\Models\Prayer;
use App\Models\Setting;
use App\Models\ShopItem;
use App\Models\TimelineEvent;
use App\Support\Fundraising;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'fund' => Fundraising::current(),
            'needs' => Need::ordered()->take(5)->get(),
            'posts' => Post::published()->take(3)->get(),
            'prayers' => Prayer::approved()->take(3)->get(),
            'prayerCount' => Prayer::whereNotNull('approved_at')->count(),
        ]);
    }

    public function story(): View
    {
        $filipino = app()->getLocale() === 'fil' && filled(Setting::get('story_fil'));

        return view('pages.story', [
            'story' => Setting::get($filipino ? 'story_fil' : 'story_en', ''),
            'isFilipino' => $filipino,
            'photos' => Setting::getJson('story_photos'),
            'videos' => Setting::getJson('story_videos'),
            'timeline' => TimelineEvent::ordered()->get(),
            'proof' => Setting::getJson('medical_proof'),
        ]);
    }

    public function help(): View
    {
        return view('pages.help', [
            'fund' => Fundraising::current(),
            'needs' => Need::ordered()->get(),
            'shop' => ShopItem::ordered()->get(),
        ]);
    }

    public function updates(): View
    {
        return view('pages.updates', [
            'posts' => Post::published()->paginate(10),
        ]);
    }

    public function update(Post $post): View
    {
        abort_if($post->published_at->isFuture(), 404);

        return view('pages.update', ['post' => $post]);
    }

    public function transparency(): View
    {
        return view('pages.transparency', [
            'expenses' => Expense::orderByDesc('spent_on')->orderByDesc('id')->get(),
            'totalSpent' => (float) Expense::sum('amount'),
            'fund' => Fundraising::current(),
        ]);
    }

    public function prayers(): View
    {
        return view('pages.prayers');
    }

    public function contact(Request $request): View
    {
        $topic = $request->query('topic');

        return view('pages.contact', [
            'topic' => is_string($topic) ? $topic : 'general',
        ]);
    }
}
