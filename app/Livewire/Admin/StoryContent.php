<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\TimelineEvent;
use App\Support\ImageStore;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Title('Story, photos and headline')]
class StoryContent extends AdminComponent
{
    use WithFileUploads;

    public const TEXT_FIELDS = [
        'hero_headline', 'hero_subline', 'hero_headline_fil', 'hero_subline_fil',
        'hero_alt', 'story_en', 'story_fil', 'share_message', 'share_message_fil',
    ];

    public array $text = [];

    public ?string $hero = null;

    public $heroUpload = null;

    /** Story photos: [['src' => base, 'alt' => '', 'caption' => ''], …] */
    public array $photos = [];

    public array $photoUploads = [];

    public array $proof = [];

    public array $proofUploads = [];

    /** Timeline row being edited: null = none, 0 = new. */
    public ?int $eventId = null;

    public array $event = ['when_label' => '', 'title' => '', 'body' => ''];

    protected array $photoFields = ['heroUpload', 'photoUploads.*', 'proofUploads.*'];

    public function mount(): void
    {
        foreach (self::TEXT_FIELDS as $field) {
            $this->text[$field] = (string) Setting::get($field, '');
        }
        $this->hero = Setting::get('hero_photo');
        $this->photos = Setting::getJson('story_photos');
        $this->proof = Setting::getJson('medical_proof');
    }

    public function saveText(): void
    {
        $this->validate([
            'text.hero_headline' => ['required', 'string', 'max:140'],
            'text.hero_subline' => ['required', 'string', 'max:240'],
            'text.hero_headline_fil' => ['nullable', 'string', 'max:140'],
            'text.hero_subline_fil' => ['nullable', 'string', 'max:240'],
            'text.hero_alt' => ['nullable', 'string', 'max:200'],
            'text.story_en' => ['required', 'string', 'max:30000'],
            'text.story_fil' => ['nullable', 'string', 'max:30000'],
            'text.share_message' => ['nullable', 'string', 'max:1000'],
            'text.share_message_fil' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::put(array_map(fn ($v) => trim((string) $v) ?: null, $this->text));
        $this->notify('Text saved.');
    }

    public function updatedHeroUpload(): void
    {
        $this->validate(['heroUpload' => self::PHOTO_RULES]);
        $images = app(ImageStore::class);
        $new = $images->store($this->heroUpload, 'site');
        $images->delete($this->hero);
        $this->hero = $new;
        $this->heroUpload = null;
        Setting::put('hero_photo', $new);
        $this->notify('Main photo changed.');
    }

    public function updatedPhotoUploads(): void
    {
        $this->validate(['photoUploads.*' => self::PHOTO_RULES]);
        $images = app(ImageStore::class);
        foreach ($this->photoUploads as $upload) {
            $this->photos[] = ['src' => $images->store($upload, 'story'), 'alt' => '', 'caption' => ''];
        }
        $this->photoUploads = [];
        $this->savePhotos('Photos added. Please write a short description for each one.');
    }

    public function savePhotos(string $message = 'Photo descriptions saved.'): void
    {
        $this->validate([
            'photos.*.alt' => ['nullable', 'string', 'max:200'],
            'photos.*.caption' => ['nullable', 'string', 'max:200'],
        ]);
        Setting::put('story_photos', $this->photos);
        $this->notify($message);
    }

    public function removeStoryPhoto(int $index, ImageStore $images): void
    {
        $images->delete($this->photos[$index]['src'] ?? null);
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
        Setting::put('story_photos', $this->photos);
        $this->notify('Photo removed.');
    }

    public function updatedProofUploads(): void
    {
        $this->validate(['proofUploads.*' => self::PHOTO_RULES]);
        $images = app(ImageStore::class);
        foreach ($this->proofUploads as $upload) {
            // Documents need readable text, so keep quality higher.
            $this->proof[] = $images->store($upload, 'proof', minQuality: 60);
        }
        $this->proofUploads = [];
        Setting::put('medical_proof', $this->proof);
        $this->notify('Document added.');
    }

    public function removeProof(int $index, ImageStore $images): void
    {
        $images->delete($this->proof[$index] ?? null);
        unset($this->proof[$index]);
        $this->proof = array_values($this->proof);
        Setting::put('medical_proof', $this->proof);
        $this->notify('Document removed.');
    }

    public function editEvent(int $id): void
    {
        $event = $id ? TimelineEvent::findOrFail($id) : null;
        $this->eventId = $id;
        $this->event = [
            'when_label' => $event->when_label ?? '',
            'title' => $event->title ?? '',
            'body' => (string) ($event->body ?? ''),
        ];
        $this->resetValidation();
    }

    public function saveEvent(): void
    {
        $data = $this->validate([
            'event.when_label' => ['required', 'string', 'max:60'],
            'event.title' => ['required', 'string', 'max:200'],
            'event.body' => ['nullable', 'string', 'max:1000'],
        ])['event'];

        $event = $this->eventId ? TimelineEvent::findOrFail($this->eventId) : new TimelineEvent(['sort_order' => (int) TimelineEvent::max('sort_order') + 1]);
        $event->fill(array_map(fn ($v) => trim((string) $v) ?: null, $data))->save();

        $this->eventId = null;
        $this->notify('Timeline saved.');
    }

    public function deleteEvent(int $id): void
    {
        TimelineEvent::findOrFail($id)->delete();
        $this->eventId = null;
        $this->notify('Removed from the timeline.');
    }

    public function render()
    {
        return view('livewire.admin.story-content', [
            'timeline' => TimelineEvent::ordered()->get(),
        ]);
    }
}
