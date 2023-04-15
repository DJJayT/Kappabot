<?php

use CommandString\Env\Env;
use Discord\Discord;
use Discord\WebSockets\Intents;

require_once __DIR__ . "/vendor/autoload.php";
$rootDir = __DIR__;

function delete_directory($dirname) {
    if (is_dir($dirname))
        $dir_handle = opendir($dirname);
    if (!$dir_handle)
        return false;
    while ($file = readdir($dir_handle)) {
        if ($file !== "." && $file !== "..") {
            if (!is_dir($dirname . "/" . $file))
                unlink($dirname . "/" . $file);
            else
                delete_directory($dirname . '/' . $file);
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

define('__ROOT__', $rootDir);


if (is_dir(__DIR__ . '/temp/music')) {
    delete_directory(__DIR__ . '/temp/music');
    if (!mkdir($concurrentDirectory = __DIR__ . '/temp/music') && !is_dir($concurrentDirectory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
    }
}

//Create temp folder
if (!is_dir(__DIR__ . '/temp')) {
    if (!mkdir(__DIR__ . '/temp') && !is_dir(__DIR__ . '/temp')) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', __DIR__ . '/temp'));
    }
    if (!mkdir($concurrentDirectory = __DIR__ . '/temp/music') && !is_dir($concurrentDirectory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', __DIR__ . '/temp/music/'));
    }
}

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
    \Commands\Music\Dc::class,
    \Commands\Music\Pause::class,
    \Commands\Music\Volume::class,
    \Commands\Music\Connect::class,
    \Commands\Music\Stop::class,
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