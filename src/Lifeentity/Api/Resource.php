<?php namespace Lifeentity\Api;

use Illuminate\Support\Facades\App;
use Lifeentity\Api\Input\InputData;

abstract class Resource {

    /**
     * Input data
     * @var InputData
     */
    protected $inputs;

    /**
     * Get inputs
     * @return InputData
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * Set inputs data
     * @param InputData $inputs
     */
    public function setInputs(InputData $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Call this method with arguments on this resource
     * @param  ResourceData $data
     * @return mixed
     */
    public function call(ResourceData $data)
    {
        return call_user_func_array(array($this, $data->getAction()), $data->getArguments());
    }

    /**
     * Check name
     * @param  string $name
     * @return boolean
     */
    public function check($name)
    {
        return strtolower($this->name()) === strtolower($name);
    }

    /**
     * Get Resource name
     * @return string
     */
    abstract public function name();

    /**
     * Make another resource request
     * @param $resourceName
     * @param ResourceData $resourceData
     * @throws APIException
     * @return mixed
     */
    public function request($resourceName, ResourceData $resourceData)
    {
        // If can't get resource by name from the respoitory throw an exception because resource doesn't exists
        if(! $resource = App::make('Lifeentity\Api\ResourceRepository')->getByName($resourceName))
        {
            throw new APIException("We can't find this resource in our application: {{$resourceName}}");
        }

        return $resource->call($resourceData);
    }
}