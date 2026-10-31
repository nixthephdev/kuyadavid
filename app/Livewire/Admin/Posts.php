<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Support\ImageStore;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Updates')]
class Posts extends AdminComponent
{
    use WithFileUploads, WithPagination;

    public const MAX_PHOTOS = 10;

    /** null = showing the list; 0 = writing a new update; otherwise the update being edited. */
    public ?int $editing = null;

    public string $title = '';

    public string $body = '';

    public string $published_at = '';

    /** Photos already saved (image base paths). */
    public array $photos = [];

    /** Saved photos the family removed; deleted from disk when they press Save. */
    public array $removed = [];

    /** New photos waiting to be saved. */
    public array $uploads = [];

    /** The file picker; each pick is added to $uploads so earlier picks are kept. */
    public array $picked = [];

    protected array $photoFields = ['uploads.*', 'picked.*'];

    public function create(): void
    {
        $this->resetForm();
        $this->editing = 0;
        $this->published_at = now()->toDateString();
    }

    public function edit(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->resetForm();
        $this->editing = $post->id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->published_at = $post->published_at->toDateString();
        $this->photos = $post->photos ?? [];
    }

    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            $this->removed[] = $this->photos[$index];
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function updatedPicked(): void
    {
        $this->validate(['picked.*' => self::PHOTO_RULES]);
        $this->uploads = [...$this->uploads, ...$this->picked];
        $this->picked = [];
    }

    public function removeUpload(int $index): void
    {
        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
    }

    public function save(ImageStore $images): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:20000'],
            'published_at' => ['required', 'date'],
            'uploads' => ['array', 'max:'.(self::MAX_PHOTOS - count($this->photos))],
            'uploads.*' => self::PHOTO_RULES,
        ], [
            'uploads.max' => 'You can add up to '.self::MAX_PHOTOS.' photos to one update.',
        ]);

        foreach ($this->uploads as $upload) {
            $this->photos[] = $images->store($upload, 'updates');
        }

        $post = $this->editing ? Post::findOrFail($this->editing) : new Post;
        $isToday = $this->published_at === now()->toDateString();

        $post->fill([
            'title' => trim($this->title),
            'body' => trim($this->body),
            'photos' => array_values($this->photos),
            // Today's updates keep the current time so they sort above earlier ones.
            'published_at' => $isToday ? ($post->published_at?->isToday() ? $post->published_at : now()) : $this->published_at.' 09:00:00',
        ])->save();

        foreach ($this->removed as $base) {
            $images->delete($base);
        }

        $this->notify($this->editing ? 'Update saved.' : 'Update published.');
        $this->resetForm();
    }

    public function delete(int $id, ImageStore $images): void
    {
        $post = Post::findOrFail($id);

        foreach ($post->photos ?? [] as $base) {
            $images->delete($base);
        }

        $post->delete();
        $this->resetForm();
        $this->notify('Update deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset('editing', 'title', 'body', 'published_at', 'photos', 'removed', 'uploads', 'picked');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.posts', [
            'posts' => Post::latest('published_at')->paginate(15),
        ]);
    }
}
