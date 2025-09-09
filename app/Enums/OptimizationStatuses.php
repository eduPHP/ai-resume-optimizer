<?php

namespace App\Enums;

enum OptimizationStatuses: string
{
    case Processing = 'processing';
    case Complete = 'complete';
    case Failed = 'failed';
    case Draft = 'draft';

    public function title(): string
    {
        return str($this->value)->title()->toString();
    }
}
