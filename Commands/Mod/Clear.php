<?php

namespace Commands\Mod;

use Commands\BaseCommand;
use Discord\Builders\CommandBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Permissions\RolePermission;
use function Common\getDiscord;
use function Common\messageWithContent;

class Clear extends BaseCommand {
    
    protected static string|array $name = "clear";
    
    /***
     * Handles the clear command
     * Deletes the last x messages in the channel
     * @param Interaction $interaction
     * @return void
     * @throws NoPermissionsException
     */
    public static function handler(Interaction $interaction): void {
        $messageCount = $interaction->data->options->offsetGet('amount')?->value ?? 10;
        $channelId = $interaction->channel_id;
        
        if ($messageCount > 100) {
            $interaction->respondWithMessage(messageWithContent("Maximum of 100 messages"), true);
            return;
        }
        
        $discord = getDiscord();
        $discord->getChannel($channelId)
            ->getMessageHistory(['limit' => $messageCount])
            ->otherwise(function () use ($interaction) {
                $interaction->respondWithMessage(messageWithContent("The bot doesn't have the permissions to read the channels messages"),
                    true);
            })
            ->done(function ($messages) use ($discord, $channelId, $interaction, $messageCount) {
                $discord->getChannel($channelId)
                    ->deleteMessages($messages)
                    ->otherwise(function () use ($interaction) {
                        $interaction->respondWithMessage(messageWithContent("The bot doesn't have the permissions to delete messages"),
                            true);
                    })
                    ->done(function () use ($interaction, $messageCount) {
                        $interaction->respondWithMessage(messageWithContent("The last $messageCount messages were deleted"),
                            true);
                    });
            });
        
    }
    
    public static function getConfig(): CommandBuilder|array {
        $permissions = new RolePermission(getDiscord());
        $permissions->manage_messages = true;
        
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Remove messages")
            ->setDmPermission(false)
            ->setDefaultMemberPermissions($permissions->bitwise)
            ->addOption((new Option(getDiscord()))
                ->setName('amount')
                ->setDescription("How much messages should be removed")
                ->setType(Option::INTEGER)
                ->setRequired(false)
            );
    }
}