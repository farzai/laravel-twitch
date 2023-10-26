<?php

namespace Farzai\Twitch\Commands;

use Illuminate\Console\Command;

class TwitchCommand extends Command
{
    public $signature = 'twitch';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
