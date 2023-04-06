<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use function Common\getDiscord;
use function Common\messageWithContent;

/***
 * Command is not functioning!
 * Don't use it atm
 */
class Volume extends BaseCommand {
    
    protected static string|array $name = "volume";
    
    public static function handler(Interaction $interaction): void {
        $guild = $interaction->guild_id;
        $volumeNumber = $interaction->data->options->offsetGet('number')->value;
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
        
        $voiceClient->setVolume($volumeNumber);
        $interaction->respondWithMessage(messageWithContent("Volume changed to $volumeNumber"), false);
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Plays a song in the voice channel")
            ->setDmPermission(false)
            ->addOption((new Option(getDiscord()))
                ->setName('number')
                ->setMinValue(0)
                ->setMaxValue(100)
                ->setDescription("The value of the volume (0-100)")
                ->setType(Option::INTEGER)
                ->setRequired(true)
            );
    }
}