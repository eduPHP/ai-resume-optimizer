<?php

namespace Modules\WAConnect;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Messenger
{
    public function text(MessageDTO $message): void
    {
        $baseUrl = rtrim(config('wa-connect.base_url'), '/');

        try {
            Http::post($baseUrl . '/send', [
                'sessionId' => config('wa-connect.session_id'),
                'to' => $message->phone,
                'message' => $message->body,
                'replyTo' => [
                    'id' => $message->replyTo->id,
                    'message' => $message->replyTo->body,
                ],
            ]);
        } catch (HttpClientException|\Exception $exception) {
            Log::error($exception->getMessage(), [$message->toArray()]);
        }
    }
}
