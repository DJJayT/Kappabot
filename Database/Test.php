<?php

namespace Database;

class Test {
    public function __construct() {
        for($i = 0; $i < 100; $i++) {
            echo PHP_EOL;
        }
        echo "Test";
    }
}