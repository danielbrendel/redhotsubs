<?php 

class TestCommand implements Asatru\Commands\Command  {
    public function handle($args)
    {
        echo "Amount of arguments: " . count($args) . "\n";

        foreach ($args as $key => $arg) {
            echo "#{$key} {$arg->getValue()} ({$arg->getType()})\n";
        }
    }
}