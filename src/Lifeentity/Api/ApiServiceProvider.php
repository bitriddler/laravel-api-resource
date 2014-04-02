<?php namespace Lifeentity\Api;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
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
        // Register web services request handling start point
        Route::get('/web-services/{resource}/{all?}', function($resource, $all = '')
        {
            // Create new resource request
            $request = ResourceRequest::make($all, Request::getMethod());

            // If couldn't get resource by name from the respoitory throw an exception
            if(! $resource = App::make('Lifeentity\Api\ResourceRepository')->getByName($resource))
            {
                throw new ResourceException("We can't find this resource in our application: {{$resource}}");
            }

            // Call this resource
            return App::make('Lifeentity\Api\ResourceManager')
                ->call($resource, $request, new InputData(Input::except('key')), Input::get('key'));

        })->where('all', '.*');
	}

    /**
     *
     */
    public function boot()
    {
        $this->package('lifeentity/api');

        $this->app->singleton('Lifeentity\Api\APIPermission', function()
        {
            return new APIPermission(Config::get('api::permissions'), Config::get('api::keys'));
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
