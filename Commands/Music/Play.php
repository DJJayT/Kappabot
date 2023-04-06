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
        $voiceClient = $discord->getVoiceClient($guild);
        
        if (isset($voiceChannel) && !isset($voiceClient)) {
            $discord->joinVoiceChannel($voiceChannel)
                ->done(function () use ($interaction, $guild, $link) {
                    Play::playTrack($interaction, $guild, $link);
                });
            
        } else if (isset($voiceClient)) {
            Play::playTrack($interaction, $guild, $link);
        } else {
            $interaction->respondWithMessage(messageWithContent("You're not in a voice channel!"), true);
        }
    }
    
    public static function playTrack(Interaction $interaction, string $guild, string $link): void {
        $discord = getDiscord();
        $voiceClient = $discord->getVoiceClient($guild);
        
        if (!isset($voiceClient)) {
            $interaction->respondWithMessage(messageWithContent("Something went wrong"), true);
        }
        
        echo PHP_EOL . "Link Option: " . $link . PHP_EOL;
        
        $songText = "Hier könnte Ihre Werbung stehen!";
        
        $ffmpegArgs = [
        
        ];
        
        //$voiceClient->ffmpegEncode(null, $ffmpegArgs);
        //$voiceClient->playOggStream();
        
        //$voiceClient->playFile()->otherwise(function() use ($interaction) {
        //    $interaction->respondWithMessage(messageWithContent("There is currently a song playing - Queue not implemented yet"), false);
        //});
        
        $interaction->respondWithMessage(messageWithContent("Now Playing: $songText"), false);
        //$botVoiceClient->start();
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