<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

class Connect extends BaseCommand {
    
    protected static string|array $name = "connect";
    
    public static function handler(Interaction $interaction): void {
        $voiceChannel = $interaction->member->getVoiceChannel();
        $guild = $interaction->guild_id;
        $discord = getDiscord();
        $voiceClient = $discord->getVoiceClient($guild);
        
        if (isset($voiceChannel) && !isset($voiceClient)) {
            $discord->joinVoiceChannel($voiceChannel);
            $interaction->respondWithMessage(messageWithContent("Bot successfully connected voice channel"), false);
            
        } else if (isset($voiceClient)) {
            $interaction->respondWithMessage(messageWithContent("Bot is already in a voice channel"), true);
        } else {
            $interaction->respondWithMessage(messageWithContent("You're not in a voice channel!"), true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Connects the bot to a voice channel")
            ->setDmPermission(false);
    }
}