<?php

namespace Commands\Bot;

use Commands\BaseCommand;
use CommandString\Env\Env;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

class Shutdown extends BaseCommand {
    
    protected static string|array $name = "shutdown";
    protected static string $guild = "405699319560142859"; //My Discord
    
    public static function handler(Interaction $interaction): void {
        if($interaction->member->id === Env::get("owner-id")) { //My Discord ID
            $interaction->respondWithMessage(messageWithContent("Shutting down..."), true)->done(function() {
                $discord = getDiscord();
                $discord->close(false);
                $loop = $discord->getLoop();
                $loop->futureTick([$loop, 'stop']);
            });
        } else {
            $interaction->respondWithMessage(messageWithContent("Das darfst du nicht :P"), true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Shuts down the Bot - Only for DJJayT!");
    }
}