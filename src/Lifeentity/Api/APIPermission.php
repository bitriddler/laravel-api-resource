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
        // Get the request access levels
        $accessLevels = $this->getRequestAccessLevels($resource->name(), $request->getAction());

        // Get current api key access level
        $accessLevel = $this->getAccessLevel($apiKey);

        // Check if this access level is in the access levels
        return in_array($accessLevel, $accessLevels);
	}

    /**
     * Return access levels that can access this resource and action
     *
     * @param $name
     * @param $action
     * @return array
     */
    protected function getRequestAccessLevels($name, $action)
    {
        $accessLevels = array();

        if(isset($this->permissions[$name]))
        {
            foreach($this->permissions[$name] as $access_level => $access_actions)
            {
                if(in_array($action, $access_actions))
                {
                    $accessLevels[] = $access_level;
                }
            }
        }

        return $accessLevels;
    }

    /**
     * @param $key
     * @return string
     */
    protected function getAccessLevel($key)
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