<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Core;

final class Module
{
    public const MODULE_ID = 'modules_GingerPayments-oxid';

    public const OEMT_GREETING_TEMPLATE_VARNAME = 'oemt_greeting';

    public const OEMT_COUNTER_TEMPLATE_VARNAME = 'oemt_greeting_counter';

    public const DEFAULT_PERSONAL_GREETING_LANGUAGE_CONST = 'OEMODULETEMPLATE_GREETING_GENERIC';
}
