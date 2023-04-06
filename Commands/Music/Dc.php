<?php

namespace Commands\Music;

use Discord\Builders\CommandBuilder;
use Discord\Parts\Permissions\RolePermission;
use function Common\getDiscord;

class Dc extends Disconnect {
    protected static string|array $name = "dc";
    
    public static function getConfig(): CommandBuilder|array {
        $permissions = new RolePermission(getDiscord());
        $permissions->move_members = true;
        return (new CommandBuilder())
            ->setName(self::getBaseCommandName())
            ->setDescription("Shorthand for disconnect command")
            ->setDmPermission(false)
            ->setDefaultMemberPermissions($permissions->bitwise);
    }
}