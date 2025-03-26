<?php

namespace Acorn\User\Console;

use Config;
use App;
use Winter\Storm\Console\Command;
use Acorn\User\Models\User;
use \Backend\Models\User as BackendUser;
use \Backend\Models\UserPreference;
use \Backend\Models\Preference as PreferenceModel;
use \Backend\Models\BrandSetting;

class SetDefaults extends Command
{
    use \Backend\Tests\Concerns\InteractsWithAuthentication;

    protected $app;

    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'user:set-defaults';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'user:set-defaults
        {username : For a specific Acorn user or all users}
        {locale=ku : Backend display locale}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Set timezone (Europe/Istanbul), language (ku) and menus (top) for given user(s)';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $username = $this->argument('username');
        $locale   = $this->argument('locale');
     
        if ($backendUser = BackendUser::where('login', $username)->first()) {
            // Save preferences...
            $up    = UserPreference::forUser($backendUser);
            $value = $up->get('backend::backend.preferences');
            $value['locale'] = $locale;
            $value['fallback_locale'] = 'en';
            $value['timezone'] = 'Europe/Istanbul';
            $value['icon_location'] = 'inline'; // Or tile
            $value['menu_location'] = 'top';
            $up->set('backend::backend.preferences', $value);
            
            $this->info("$username ($backendUser->id) defaults set");
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
