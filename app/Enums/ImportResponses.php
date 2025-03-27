<?php

namespace App\Enums;

enum ImportResponses: string
{
    case LOG_SAVED_IN = '"Arquivo salvo em:';

    case FILE_ADDED_TO_IMPORT_QUEUE = 'Arquivo adicionado a fila de importação';

    case FAILED_TO_PROCESS_FILE = 'Falha ao processar o CSV:';

    case VALIDATION_ERROR = 'Erro de validação:';

    case FILE_NOT_FOUND = 'Arquivo não encontrado';

    case ERROR_OPENING_FILE = 'Erro ao abrir arquivo:';
}
