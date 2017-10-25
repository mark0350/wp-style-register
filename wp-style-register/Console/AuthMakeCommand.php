<?php

namespace App\WpStyleRegister;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class AuthMakeCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:wp-auth
                    {--views : Only scaffold the authentication views}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic wordpress style login and registration views and routes';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'Wp-auth/login.stub' => 'auth/login.blade.php',
        'Wp-auth/register.stub' => 'auth/register.blade.php',
        'Wp-auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
        'Wp-auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php',
        'layouts/app.stub' => 'layouts/app.blade.php',
        'home.stub' => 'home.blade.php',
    ];

    protected $controllers = [
        'HomeController.stub' => 'HomeController.php',
	    'Wp-auth/ForgotPasswordController.stub' => 'Wp-auth/ForgotPasswordController.php',
	    'Wp-auth/LoginController.stub' => 'Wp-auth/LoginController.php',
	    'Wp-auth/RegisterController.stub' => 'Wp-auth/RegisterController.php',
	    'Wp-auth/ResetPasswordController.stub' => 'Wp-auth/ResetPasswordController.php'
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        if (! $this->option('views')) {

        	$this->exportControllers();

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/make/routes.stub'),
                FILE_APPEND
            );

            file_put_contents(
                app_path('Listeners/SendRegisteredNotification.php'),
                file_get_contents(__DIR__.'/stubs/make/listeners/SendRegisteredNotification.stub')
            );

	        file_put_contents(
		        app_path('Events/UserRegistered.php'),
		        file_get_contents(__DIR__.'/stubs/make/events/UserRegistered.stub')
	        );

	        file_put_contents(
		        app_path('Notifications/RegisterNotification.php'),
		        file_get_contents(__DIR__.'/stubs/make/notifications/RegisterNotification.stub')
	        );

	        file_put_contents(
	            app_path('User.php'),
                file_get_contents(__DIR__.'/stubs/make/models/User.stub')
            );

	        file_put_contents(
	            database_path('migrations/2014_10_12_000000_create_users_table.php'),
                file_get_contents(__DIR__.'/stubs/make/migrations/create_users_table.stub')
            );

        }

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = resource_path('views/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = resource_path('views/auth/passwords'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = app_path('Http/Controllers/Wp-auth'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = app_path('Events'))) {
            mkdir($directory, 0755, true);
        }

	    if (! is_dir($directory = app_path('Listeners'))) {
		    mkdir($directory, 0755, true);
	    }

	    if (! is_dir($directory = app_path('Notifications'))) {
		    mkdir($directory, 0755, true);
	    }

    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/stubs/make/views/'.$key,
                $view
            );
        }
    }

	/**
	 * Compiles the HomeController stub.
	 *
	 * @param $controller
	 *
	 * @return string
	 */
    protected function compileControllerStub( $controller )
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents($controller)
        );
    }

    protected function exportControllers()
    {
	    foreach ($this->controllers as $key => $value) {
		    if (file_exists($controller = app_path('Http/Controllers/'.$value)) && ! $this->option('force')) {
			    if (! $this->confirm("The [{$value}] controller already exists. Do you want to replace it?")) {
				    continue;
			    }
		    }

		    file_put_contents(
			    $controller,
                file_get_contents(__DIR__.'/stubs/make/controllers/'.$key)
		    );

	    }
    }
}
