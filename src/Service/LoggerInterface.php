<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Service;

interface LoggerInterface
{
    /**
     *
     * @param string $message
     */
    public function log(string $message): void;
}
