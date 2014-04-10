<?php namespace Lifeentity\Api;

class APIPermission {

	/**
	 * Permissions array
	 * @var array
	 */
	protected $permissions;

    /**
     * @var array
     */
    protected $keys;

    /**
     * Constructor
     * @param array $permissions
     * @param array $keys
     */
	public function __construct(array $permissions, array $keys)
	{
		$this->permissions = $permissions;
        $this->keys = $keys;
	}

    /**
     * Returns true if the api access is allowed to use this
     * @param \Lifeentity\Api\Resource|Resource $resource
     * @param  ResourceRequest $request
     * @param $apiKey
     * @return boolean
     */
	public function allowed(Resource $resource, ResourceRequest $request, $apiKey)
	{
        $resourceAccessLevels = $this->getResourceAccessLevels($resource->name());

        // Get current access level from api key
        $accessLevel = $this->getApiAccessLevel($apiKey);

        // If your access level is in the resource access levels then see if you have access to this method
        if(in_array($accessLevel, $resourceAccessLevels))
        {
            $allowedActions = $this->getAllowedAccessLevelActions($resource->name(), $accessLevel);

            return in_array($request->getAction(), $allowedActions);
        }

        // You are not in the resource access levels then you have access to all resource actions
        return true;
	}

    /**
     * Get allowed actions for this access level
     *
     * @param $name
     * @param $level
     * @return array
     */
    protected function getAllowedAccessLevelActions($name, $level)
    {
        return $this->permissions[$name][$level];
    }

    /**
     * @param $name
     * @return array
     */
    protected function getResourceAccessLevel($name)
    {
        if(isset($this->permissions[$name]))
        {
            return array_keys($this->permissions[$name]);
        }

        return array();
    }

    /**
     * @param $key
     * @return string
     */
    protected function getApiAccessLevel($key)
    {
        foreach($this->keys as $cKey => $level)
        {
            if($cKey === $key)
            {
                return $level;
            }
        }
    }
}