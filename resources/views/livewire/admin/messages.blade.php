<div>
    <h1 class="text-3xl font-semibold">Contact messages</h1>
    <p class="mt-1 text-lg text-muted">Messages from the contact form. Tap a message to read it.</p>

    @if ($messages->isEmpty())
        <p class="card mt-6 text-lg text-muted">No messages yet.</p>
    @else
        <ul class="mt-6 space-y-3" role="list">
            @foreach ($messages as $msg)
                <li wire:key="msg-{{ $msg->id }}" class="card p-0">
                    <button type="button" wire:click="toggle({{ $msg->id }})" class="flex w-full items-start gap-3 p-5 text-left" aria-expanded="{{ $open === $msg->id ? 'true' : 'false' }}">
                        <span @class(['mt-2 size-3 shrink-0 rounded-full', 'bg-gold-500' => ! $msg->read_at, 'bg-transparent' => $msg->read_at])>
                            <span class="sr-only">{{ $msg->read_at ? '' : 'New' }}</span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span @class(['block text-lg', 'font-bold' => ! $msg->read_at, 'font-semibold' => $msg->read_at])>{{ $msg->name }}</span>
                            <span class="block text-base text-muted">{{ __('site.contact.topics.'.$msg->topic, [], 'en') }} · {{ $msg->created_at->format('M j, Y g:i A') }}</span>
                            @if ($open !== $msg->id)
                                <span class="mt-1 block truncate text-base text-muted">{{ $msg->message }}</span>
                            @endif
                        </span>
                    </button>

                    @if ($open === $msg->id)
                        <div class="border-t border-line p-5">
                            <p class="text-lg whitespace-pre-line">{{ $msg->message }}</p>
                            <dl class="mt-4 space-y-1 text-base">
                                <div><dt class="inline font-semibold">Email:</dt> <dd class="inline break-all">{{ $msg->email }}</dd></div>
                                @if ($msg->phone)
                                    <div><dt class="inline font-semibold">Phone:</dt> <dd class="inline">{{ $msg->phone }}</dd></div>
                                @endif
                            </dl>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="mailto:{{ $msg->email }}?subject={{ rawurlencode('Re: your message about David') }}" class="btn-primary"><x-icon name="mail" class="size-5" /> Reply by email</a>
                                <button type="button" wire:click="delete({{ $msg->id }})" wire:confirm="Delete this message? This cannot be undone."
                                        class="btn-quiet text-[#9a3412]"><x-icon name="trash" class="size-5" /> Delete</button>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
</div>
