<?php namespace Lifeentity\Api;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Route::group($this->getRouteConfig('resources'), function()
        {
            // Web services request
            Route::any('{resource}/{uri?}', function($resource, $uri = '')
            {
                return App::make('Lifeentity\Api\APIRequest')

                    ->call(Request::method(), $resource, $uri, Input::except('key', 'signature'), Input::get('key'), Input::get('signature'));

            })->where('uri', '.*');
        });
    }

    /**
     * @param $name
     * @return array
     */
    protected function getRouteConfig($name)
    {
        $routeConfig = array();

        $domain = $this->app['config']->get("api::config.routes.{$name}.domain");
        $prefix = $this->app['config']->get("api::config.routes.{$name}.prefix");

        if($domain) $routeConfig['domain'] = $domain;
        if($prefix) $routeConfig['prefix'] = $prefix;

        return $routeConfig;
    }

    /**
     * Add application management routes
     */
    protected function applicationManagementRoutes()
    {
        $routeConfig = $this->getRouteConfig('application');

        $routeConfig['filter'] = $this->app['config']->get('api::config.filters.creation');

        Route::group($routeConfig, function()
        {
            Route::get('/', function()
            {
                $applications = APIApplication::orderBy('id', 'DESC')->get();

                $baseUrl = Request::url();

                return View::make('api::application', compact('applications', 'baseUrl'));
            });

            Route::get('/delete/{id}', function($id)
            {
                APIApplication::find($id)->delete();

                return Redirect::back();
            });

            Route::post('/update/{id}', function($id)
            {
                $application = APIApplication::find($id);

                $application->key = Input::get('key');
                $application->secret = Input::get('secret');

                $application->setPermissionsString(Input::get('permissions'));

                $application->save();

                return Redirect::back();
            });

            Route::post('/create', function()
            {
                APIApplication::quickCreate(Input::get('key'), Input::get('secret'), Input::get('permissions'));

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
