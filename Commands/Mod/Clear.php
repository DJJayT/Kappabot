<?php

namespace Commands\Mod;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Permissions\Permission;
use function Common\getDiscord;
use function Common\messageWithContent;

class Clear extends BaseCommand {
    
    protected static string|array $name = "clear";
    
    public static function handler(Interaction $interaction): void {
        $messageCount = $interaction->data->options->offsetGet('amount')?->value ?? 10;
        $channelId = $interaction->channel_id;
        
        if ($messageCount > 100) {
            $interaction->respondWithMessage(messageWithContent("Maximum of 100 messages"), true);
            return;
        }
        
        $discord = getDiscord();
        try {
            $discord->getChannel($channelId)
                ->getMessageHistory(['limit' => $messageCount])
                ->done(function ($messages) use ($discord, $channelId, $interaction, $messageCount) {
                    $discord->getChannel($channelId)
                        ->deleteMessages($messages)
                        ->done(function () use ($interaction, $messageCount) {
                            $interaction->respondWithMessage(messageWithContent("The last $messageCount messages were deleted"),
                                true);
                        });
                });
        } catch (NoPermissionsException) {
            $interaction->respondWithMessage(messageWithContent("The bot doesn't have the permissions to do that"),
                true);
        }
    }
    
    public static function getConfig(): CommandBuilder|array {
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Remove messages")
            ->setDmPermission(false)
            ->setDefaultMemberPermissions(Permission::TEXT_PERMISSIONS['manage_messages'])
            ->addOption((new Option(getDiscord()))
                ->setName('amount')
                ->setDescription("How much messages should be removed")
                ->setType(Option::INTEGER)
                ->setRequired(false)
            );
    }
}