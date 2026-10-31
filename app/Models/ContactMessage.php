<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const TOPICS = ['general', 'medical', 'supplies', 'media'];

    protected $fillable = ['name', 'email', 'phone', 'topic', 'message', 'ip_hash', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
