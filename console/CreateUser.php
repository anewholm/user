<?php

namespace Acorn\User\Console;

use Winter\Storm\Console\Command;
use Acorn\User\Models\User;
use \Backend\Models\User as BackendUser;
use \Symfony\Component\Console\Output\ConsoleOutput;
use \Symfony\Component\Console\Input\ArgvInput;

class CreateUser extends Command
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'user:create-user';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'user:create-user
        {username : For a specific backend username or all backend users}
        {password=password : The backend / front-end login password}
        {email? : The backend & front-end email}
        {--s|system-user : Set the user as a system user.}
        {--d|set-defaults : Set the default user preferences with user:set-defaults.}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}
        {--c|create-backend : Create the backend user if not found.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Create a Front-end user and create/attach an associated BackendUser';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $username      = $this->argument('username');
        $password      = $this->argument('password');
        $email         = $this->argument('email');

        $systemUser    = $this->option( 'system-user');
        $setDefaults   = $this->option( 'set-defaults');
        $force         = $this->option( 'force');
        $createBackend = $this->option( 'create-backend');

        if (!$email) {
            $email = "$username@nowhere.org";
            $this->info( "Email defaulted to $email");
        }

        $backendUser = BackendUser::where('login', $username)->first();
        if (!$backendUser && $createBackend) {
            $this->info( "Creating backend user $username");
            $backendUser = BackendUser::create([
                'login'    => $username,
                'password' => $password,
                'password_confirmation' => $password,
                'email'    => $email
            ]);
        }

        if ($backendUser) {
            $user = NULL;
            if ($backendUser->acorn_user_user_id) {
                $user = User::find($backendUser->acorn_user_user_id);
                if ($force) $this->info( "$username backend user already attached to $user->name, forcing change...");
                else        $this->error("$username backend user already attached to $user->name");
            }
            
            if (!$user || $force) {
                $user = User::where('username', $username)->first();
                if (!$user) {
                    $user = User::create([
                        'name'     => ucfirst($username),
                        'username' => $username,
                        'email'    => $email,
                        'password' => $password,
                        'is_system_user' => $systemUser
                    ]);
                } else {
                    $this->output->writeln("$username user already exists. Attaching...");
                }
                $backendUser->acorn_user_user_id = $user->id;
                $backendUser->save();
                $this->info("$username attached");

                if ($setDefaults) {
                    // Run user:set-defaults
                    $input    = new ArgvInput([
                        'user:set-defaults',
                        $username
                    ]);
                    $output   = new ConsoleOutput();
                    $command  = new SetDefaults();
                    $command->setLaravel($this->laravel);
                    $exitCode = $command->run($input, $output);
                }
            }
        } else {
            $this->error("Backend user $username not found");
        }
    }

    // TODO: Provide autocomplete suggestions for the "myCustomArgument" argument
    // public function suggestMyCustomArgumentValues(): array
    // {
    //     return ['value', 'another'];
    // }
}
