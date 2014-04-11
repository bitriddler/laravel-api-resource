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
        // Get current access level from api key
        $accessLevel = $this->getApiAccessLevel($apiKey);

        if(! $accessLevel) return false;

        $only = $this->getAccessLevelOnly($accessLevel);

        $except = $this->getAccessLevelExcept($accessLevel);

        $qualifiedName = $resource->name().'@'.$request->getAction();

        // If action is not in only array return false
        if(!empty($only) && ! in_array($qualifiedName, $only)) return false;

        // If action is in except array return false
        if(!empty($except) && in_array($qualifiedName, $except)) return false;

        return true;
    }

    /**
     * @param $accessLevel
     * @return array
     */
    protected function getAccessLevelOnly($accessLevel)
    {
        if(isset($this->permissions[$accessLevel]) && isset($this->permissions[$accessLevel]['only']))
        {
            return $this->permissions[$accessLevel]['only'];
        }

        return array();
    }

    /**
     * @param $accessLevel
     * @return array
     */
    protected function getAccessLevelExcept($accessLevel)
    {
        if(isset($this->permissions[$accessLevel]) && isset($this->permissions[$accessLevel]['except']))
        {
            return $this->permissions[$accessLevel]['except'];
        }

        return array();
    }

    /**
     * @param $key
     * @return string
     */
    protected function getApiAccessLevel($key)
    {
        return isset($this->keys[$key]) ? $this->keys[$key] : '';
    }
}