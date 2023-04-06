<?php

namespace Commands\Music;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Permissions\RolePermission;
use function Common\getDiscord;
use function Common\messageWithContent;

class Disconnect extends BaseCommand {
    
    protected static string|array $name = "disconnect";
    
    public static function handler(Interaction $interaction): void {
        $guild = $interaction->guild_id;
        $discord = getDiscord();
        $voiceClient = $discord->getVoiceClient($guild);
        
        if (isset($voiceClient)) {
            $voiceClient->close();
            $interaction->respondWithMessage(messageWithContent("Disconnected successfully"), false);
        } else {
            $interaction->respondWithMessage(messageWithContent("The bot is not in a voice channel!"), true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        $permissions = new RolePermission(getDiscord());
        $permissions->move_members = true;
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Disconnects from the voice channel")
            ->setDmPermission(false)
            ->setDefaultMemberPermissions($permissions->bitwise);
    }
}