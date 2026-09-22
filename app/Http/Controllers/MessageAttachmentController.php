<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageAttachmentController
{
    public function __invoke(Message $message): StreamedResponse
    {
        Gate::authorize('download', $message);

        abort_unless($message->attachment_path !== null, 404);

        $disk = Storage::disk(config('rigo.private_disk', 'local'));
        abort_unless($disk->exists($message->attachment_path), 404);

        return $disk->download($message->attachment_path, basename($message->attachment_path));
    }
}
