<?php

namespace WpStyleRegister;

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
		'Wp-auth/login.stub'           => 'auth/login.blade.php',
		'Wp-auth/register.stub'        => 'auth/register.blade.php',
		'Wp-auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
		'Wp-auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php',
		'layouts/app.stub'             => 'layouts/app.blade.php',
		'home.stub' => 'home.blade.php',
    ];

	protected $appFiles = [
		'controllers/HomeController.stub'                   => 'Http/Controllers/HomeController.php',
		'controllers/Wp-auth/ForgotPasswordController.stub' => 'Http/Controllers/Wp-auth/ForgotPasswordController.php',
		'controllers/Wp-auth/LoginController.stub'          => 'Http/Controllers/Wp-auth/LoginController.php',
		'controllers/Wp-auth/RegisterController.stub'       => 'Http/Controllers/Wp-auth/RegisterController.php',
		'controllers/Wp-auth/ResetPasswordController.stub'  => 'Http/Controllers/Wp-auth/ResetPasswordController.php',
		'listeners/SendRegisteredNotification.stub'         => 'Listeners/SendRegisteredNotification.php',
		'events/UserRegistered.stub'                        => 'Events/UserRegistered.php',
		'notifications/RegisterNotification.stub'           => 'Notifications/RegisterNotification.php',
		'models/User.stub'                                  => 'User.php',

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

        	$this->exportAppFiles();

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/make/routes.stub'),
                FILE_APPEND
            );


	        if (file_exists($file = database_path('migrations/2014_10_12_000000_create_users_table.php')) && ! $this->option('force')) {
		        if ($this->confirm("The migrations/2014_10_12_000000_create_users_table.php already exists. Do you want to replace it?")) {
			        file_put_contents(
				        $file,
				        file_get_contents(__DIR__.'/stubs/make/migrations/create_users_table.stub')
			        );
		        }
	        }

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

    protected function exportAppFiles()
    {
	    foreach ($this->appFiles as $key => $value) {
		    if (file_exists($file = app_path($value)) && ! $this->option('force')) {
			    if (! $this->confirm("The [{$value}] file already exists. Do you want to replace it?")) {
				    continue;
			    }
		    }

		    file_put_contents(
			    $file,
                file_get_contents(__DIR__.'/stubs/make/'.$key)
		    );

	    }
    }
}
