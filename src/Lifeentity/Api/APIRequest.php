<?php namespace Lifeentity\Api;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Request;
use Lifeentity\Api\Input\InputData;

class APIRequest {

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $method
     * @param $resourceName
     * @param $uri
     * @param $inputs
     * @param $key
     * @param $signature
     * @throws APIException
     * @return mixed
     */
    public function call($method, $resourceName, $uri, $inputs, $key, $signature)
    {
        // Get resource data containing: action and arguments
        $resourceData = ResourceData::make($uri, $method);

        // If can't get resource by name from the respoitory throw an exception because resource doesn't exists
        if(! $resource = $this->app->make('Lifeentity\Api\ResourceRepository')->getByName($resourceName))
        {
            throw new APIException("We can't find this resource in our application: {{$resourceName}}");
        }

        // Get application by the api key
        if(! $application = $this->app->make('Lifeentity\Api\APIApplication')->byApiKey($key)->first())
        {
            throw new APIPermissionException("The api key is incorrect.");
        }

        // Check signature
        if(! $application->checkSignature($signature))
        {
            throw new APIPermissionException("The api signature is incorrect.");
        }

        // Check if this application has permissions to access this resource and action
        if(! $application->checkPermissions($resource->name(), $resourceData->getAction()))
        {
            throw new APIPermissionException("You don't have permissions to request `{$resourceData->getAction()}` on this resource: {{$resource->name()}}");
        }

        // Set inputs for this resource
        $resource->setInputs(new InputData($inputs));

        // Now every thing is ready, call this resource
        return $resource->call($resourceData);
    }

} 