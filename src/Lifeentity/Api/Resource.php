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
	 * @param  ResourceRequest $request
	 * @return mixed
	 */
	public function call(ResourceRequest $request)
	{
		return call_user_func_array(array($this, $request->getAction()), $request->getArguments());
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
	 * Get all resources
	 * GET /{resource}/
	 * @return mixed
	 */
	abstract public function index();

	/**
	 * Get this one resource
	 * GET /{resource}/id
	 * @param  int $id
	 * @return mixed
	 */
	abstract public function show($id);

	/**
	 * Store this resource
	 * POST /{resource}/
	 * @return mixed
	 */
	abstract public function store();

	/**
	 * Update this resource
	 * PUT /{resource}/id
	 * @param  int $id
	 * @return mixed
	 */
	abstract public function update($id);

	/**
	 * Destory this resource
	 * DELETE /{resource}/id
	 * @param  int $id
	 * @return mixed
	 */
	abstract public function destroy($id);

}