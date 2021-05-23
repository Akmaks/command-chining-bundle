<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Exceptions;

class NotMasterCommandException extends CommandChainingBundleException
{
    /**
     * NotMasterCommandException constructor.
     *
     * @param string $commandName Command name
     */
    public function __construct(string $commandName)
    {
        $message = sprintf(
            'Command %s is a part of the chain, but it\'s not a master',
            $commandName
        );

        parent::__construct($message);
    }
}
