<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Admin User';

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
        $email = $this->output->ask('Email');
        $password = $this->output->askHidden('Password');

        $user = new User();
        $user->name = $email;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        $this->output->writeln("DONE: {$user->id}");

        return 0;
    }
}
