<?php

namespace Modules\WAConnect;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Queue\SerializesModels;

class MessageDTO implements Arrayable
{
    public function __construct(
        public ?string $id = null,
        public ?string $body = null,
        public ?string $phone = null,
        public ?MessageDTO $replyTo = null,
    )
    {
        //
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromCache($key): ?static
    {
        if (!cache()->has($key)) {
            return null;
        }

        $originalMessage = json_decode(cache($key), true);

        return new MessageDTO(
            id: $originalMessage['id'],
            body: $originalMessage['body'],
            phone: $originalMessage['from'],
        );
    }
}
