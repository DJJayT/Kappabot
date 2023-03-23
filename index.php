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
    "intents" => Intents::getDefaultIntents()
]);

//Commands
$env->commands = [
    Commands\Ping::class,
    Commands\Guild::class
];

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
