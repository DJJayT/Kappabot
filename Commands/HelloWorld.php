<?php

namespace Commands;

use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use function Common\messageWithContent;

class HelloWorld extends BaseCommand {
    protected static array|string $name = "helloworld";
    
    public static function handler(Interaction $interaction): void {
        $interaction->respondWithMessage(messageWithContent("Hello World!"));
    }
    
    public static function autocomplete(Interaction $interaction): void {
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Hello World Command");
    }
}
