<?php namespace Lifeentity\Api;

class APIPermission extends \Lifeentity\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'api_permissions';

    /**
     * @var array
     */
    protected $fillable = array('resource', 'action');

    /**
     * @param $resource
     * @param $action
     * @return bool
     */
    public function match($resource, $action)
    {
        // Match resource and action
        return $this->matchResource($resource) && $this->matchAction($action);
    }

    /**
     * @param $resource
     * @return bool
     */
    public function matchResource($resource)
    {
        return $this->matchStrings($this->resource, $resource);
    }

    /**
     * @param $action
     * @return bool
     */
    public function matchAction($action)
    {
        return $this->matchStrings($this->action, $action);
    }

    /**
     * @param $regex
     * @param $subject
     * @return bool
     */
    protected function matchStrings($regex, $subject)
    {
        // If it's a regex then preg match with the given resource
        if( preg_match('/^\/.*\/[a-z]*$/', $regex)) {

            return preg_match($regex, $subject) !== 0 && preg_match($regex, $subject) !== false;
        }

        return $subject == $regex;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function application()
    {
        return $this->belongsTo(APIApplication::getClass(), 'application_id');
    }
}