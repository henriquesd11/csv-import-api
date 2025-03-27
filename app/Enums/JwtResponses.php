<?php

namespace App\Enums;

enum JwtResponses:string
{
    case UNAUTHENTICATED = 'Não autenticado.';

    case ERROR = 'error';
}
