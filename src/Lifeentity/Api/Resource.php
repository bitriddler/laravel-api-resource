<?php namespace Lifeentity\Api;

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

}