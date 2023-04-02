<?php

use CommandString\Env\Env;
use Discord\Discord;
use Discord\WebSockets\Intents;

require_once __DIR__ . "/vendor/autoload.php";

//Env
$env = Env::createFromJsonFile("./env.json");

//Discord
$env->discord = new Discord([
    "token" => $env->token,
    "intents" => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS,
    "loadAllMembers" => true,
]);

//All commands related to the bots-administration
$botCommands = [
    \Commands\Bot\Shutdown::class,
];

//All commands related to music
$musicCommands = [
    \Commands\Music\Play::class,
    \Commands\Music\Disconnect::class,
];

$modCommands = [
    \Commands\Mod\Clear::class,
];

//All commands related for testing (beginning commands)
$testCommands = [
    Commands\Ping::class,
    Commands\Guild::class,
    Commands\HelloWorld::class,
];

//Commands
$env->commands = array_merge(
    $botCommands,
    $musicCommands,
    $modCommands,
    $testCommands
);

//Events
$env->events = [
    Events\Init::class
];

//Interactions
$env->interactions = [
    Interactions\GuildInfo::class
];

Events\Init::listen();

//Run
$env->discord->run();