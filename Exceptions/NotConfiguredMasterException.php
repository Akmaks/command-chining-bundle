<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Exceptions;

class NotConfiguredMasterException extends CommandChainingBundleException
{
    /**
     * NotMasterCommandException constructor.
     *
     * @param string $commandName Command name
     */
    public function __construct(string $commandName)
    {
        $message = sprintf(
            'Main command %s not configured correctly',
            $commandName
        );

        parent::__construct($message);
    }
}
