<?php namespace Lifeentity\Api;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lifeentity\Api\Input\InputData;

class ApiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->package('lifeentity/api');

        // Set routes for application management if enabled
        if($this->app['config']->get('api::config.application_management'))
        {
            $this->applicationManagementRoutes();
        }

        $this->webServicesRoutes();
	}

    /**
     * Web services routes
     */
    protected function webServicesRoutes()
    {
        Route::group($this->routeConfig(), function()
        {
            // Web services request
            Route::any('resource/{resource}/{uri?}', function($resource, $uri = '')
            {
                return App::make('Lifeentity\Api\APIRequest')

                    ->call(Request::method(), $resource, $uri, Input::except('key', 'signature'), Input::get('key'), Input::get('signature'));

            })->where('uri', '.*');
        });
    }

    /**
     * Get route configurations
     *
     * @return array
     */
    protected function routeConfig()
    {
        $routeConfig = array();

        $domain = $this->app['config']->get('api::config.routes.domain');
        $prefix = $this->app['config']->get('api::configroutes.prefix');

        if($domain) $routeConfig['domain'] = $domain;
        if($prefix) $routeConfig['prefix'] = $prefix;

        return compact('domain', 'prefix');
    }

    /**
     * Add application management routes
     */
    protected function applicationManagementRoutes()
    {
        $routeConfig = $this->routeConfig();

        $routeConfig['filter'] = $this->app['config']->get('api::config.filters.creation');

        Route::group($routeConfig, function()
        {
            Route::get('/application/create', function()
            {
                $applications = \Lifeentity\Api\APIApplication::orderBy('id', 'DESC')->get();

                return View::make('api::application', compact('applications'));
            });

            Route::post('/application/update/{id}', function($id)
            {
                $application = \Lifeentity\Api\APIApplication::find($id);

                $application->key = Input::get('key');
                $application->secret = Input::get('secret');

                $application->setPermissionsString(Input::get('permissions'));

                $application->save();

                return Redirect::back();
            });

            Route::post('/api/application/create', function()
            {
                \Lifeentity\Api\APIApplication::quickCreate(Input::get('key'), Input::get('secret'), Input::get('permissions'));

                return Redirect::back();
            });
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}
}
