<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSupportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:support-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate support files (ide-helper, model attribute hinting)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('ide-helper:generate');
        $this->call('ide-helper:meta');

        $this->call('ide-helper:models', [
            '--write' => true,
            '--reset' => true,
        ]);

        return 0;
    }
}
