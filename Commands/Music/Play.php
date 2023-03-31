<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

class Play extends BaseCommand {
    
    protected static string|array $name = "play";
    
    public static function handler(Interaction $interaction): void {
        $voiceChannel = $interaction->member->getVoiceChannel();
        $guild = $interaction->guild_id;
        $link = $interaction->data->options->offsetGet('url')->value;
        $discord = getDiscord();
        $botVoiceChannel = $discord->getVoiceClient($guild);
        echo "Link Option: " . $link;
        
        if (isset($voiceChannel) && !isset($botVoiceChannel)) {
            $discord->joinVoiceChannel($voiceChannel)->done(function() use ($interaction) {
                $interaction->respondWithMessage(messageWithContent("Joined Channel - WIP"), true);
            });
            
        } else if(isset($botVoiceChannel)) {
            $interaction->respondWithMessage(messageWithContent("The bot is already in a channel!"), true);
        } else {
            $interaction->respondWithMessage(messageWithContent("You're not in a voice channel!"), true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Plays a song in the voice channel")
            ->setDmPermission(false)
            ->addOption((new Option(getDiscord()))
                ->setName('url')
                ->setDescription("The Link to your music title (YouTube)")
                ->setType(Option::STRING)
                ->setRequired(true)
            );
    }
}