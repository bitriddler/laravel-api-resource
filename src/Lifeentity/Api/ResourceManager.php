<?php namespace Lifeentity\Api;

use Lifeentity\Api\Input\InputData;

class ResourceManager {

	/**
	 * Resource permissions
	 * @var APIPermission
	 */
	protected $permissions;

	/**
	 * Resource permissions
	 * @param APIPermission $permissions
	 */
	public function __construct(APIPermission $permissions)
	{
		$this->permissions = $permissions;
	}

    /**
     * Call this resource with the given method, arguments and input data
     * @param \Lifeentity\Api\Resource|Resource $resource
     * @param  ResourceRequest $request
     * @param  InputData $inputs
     * @param $apiKey
     * @throws ResourceException
     * @return mixed
     */
	public function call(Resource $resource, ResourceRequest $request, InputData $inputs, $apiKey)
	{
		if($this->permissions->allowed($resource, $request, $apiKey))
		{
			// Set inputs for this resource
			$resource->setInputs($inputs);

			// Call the method with the arguments on this resource
			return $resource->call($request);
		}

		throw new ResourceException("You don't have permissions to request `{$request}` on this resource: {{$resource->name()}}");
	}

}