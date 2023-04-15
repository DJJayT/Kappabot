<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use PHP_CodeSniffer\Tokenizers\PHP;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;
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
            $interaction->respondWithMessage(messageWithContent("Something went wrong"));
            return;
        }
        
        $interaction->acknowledgeWithResponse()
            ->then(function () use ($link, $voiceClient, $interaction) {
                if (preg_match('/^(https?:\/\/)?((www\.)?youtube\.com|youtu\.be)\/.+$/', $link)) {
                    
                    [$songTitle, $filePath] = self::downloadVideo($link);
                    
                } else {
                    $interaction->sendFollowUpMessage(messageWithContent("Provided link must be a youtube link"));
                    return;
                }
                
                $voiceClient->playFile($filePath)
                    ->otherwise(function () use ($interaction) {
                        $interaction->sendFollowUpMessage(messageWithContent("There is currently a song playing - Queue not implemented yet"));
                    });
                
                $interaction->sendFollowUpMessage(messageWithContent("Now Playing: $songTitle"));
                
            });
        
        
    }
    
    public static function downloadVideo(string $link): array {
        $yt = new YoutubeDl();
        $yt->setBinPath('yt-dlp');
        
        $video = $yt->download(Options::create()
            ->downloadPath(__ROOT__ . '/temp/music')
            ->restrictFileNames(true)
            ->extractAudio(true)
            ->audioFormat('mp3')
            ->audioQuality('0') // best
            ->url($link)
        );
        
        $video = $video->getVideos()[0];
        $songTitle = $video->getTitle();
        $filePath = $video->getFile();
        
        return [$songTitle, $filePath];
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