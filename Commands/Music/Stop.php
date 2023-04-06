<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

class Stop extends BaseCommand {
    
    protected static string|array $name = "stop";
    
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
        
        if ($voiceClient->isSpeaking()) {
            $voiceClient->stop();
            $interaction->respondWithMessage(messageWithContent("Music stopped"), false);
        } else {
            $interaction->respondWithMessage(messageWithContent("Bot is not playing music"), true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Stopps the current music played")
            ->setDmPermission(false);
    }
}