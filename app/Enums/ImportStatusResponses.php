<?php

namespace App\Enums;

enum ImportStatusResponses: string
{
    case PROCESSING = 'processing';

    case COMPLETED = 'completed';

    case FAILED = 'failed';

    case PENDING = 'pending';
}
