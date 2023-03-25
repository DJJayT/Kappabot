<?php

namespace Commands;

use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;

use function Common\messageWithContent;

class Ping extends BaseCommand
{
    protected static string|array $name = "ping";

    public static function handler(Interaction $interaction): void
    {
        $interaction->respondWithMessage(messageWithContent("Pong :ping_pong:"));
    }

    public static function getConfig(): CommandBuilder|array
    {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Ping the bot")
        ;
    }
}
