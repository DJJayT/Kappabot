<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

class Pause extends BaseCommand {
    
    protected static string|array $name = "pause";
    
    public static function handler(Interaction $interaction): void {
        $guild = $interaction->guild_id;
        $discord = getDiscord();
        $voiceClient = $discord->getVoiceClient($guild);
        
        if (!isset($voiceClient)) {
            $interaction->respondWithMessage(messageWithContent("The bot is not in a voice channel!"), true);
            return;
        }
        
        if ($interaction->member->getVoiceChannel() !== $voiceClient->getChannel()) {
            $interaction->respondWithMessage(messageWithContent("You're not in the same voice channel!"), true);
            return;
        }
        
        if (!$voiceClient->isPaused()) {
            $voiceClient->pause();
            $interaction->respondWithMessage(messageWithContent("Music paused."), false);
        } else {
            $voiceClient->unpause();
            $interaction->respondWithMessage(messageWithContent("Music resumed."), false);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Pause or unpause the music")
            ->setDmPermission(false);
    }
}