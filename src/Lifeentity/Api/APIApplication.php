<?php namespace Lifeentity\Api;

use Illuminate\Support\Facades\Crypt;

class APIApplication extends \Lifeentity\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'api_applications';

    /**
     * @var array
     */
    protected $fillable = array('key', 'secret');

    /**
     * @var array
     */
    protected $hidden = array('secret');

    /**
     * @param $key
     * @param $secret
     * @param $permissionsString
     */
    public static function quickCreate($key, $secret, $permissionsString)
    {
        $application = static::create(compact('key', 'secret'));

        $application->setPermissionsString($permissionsString);
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getPermissionsString($separator = ':')
    {
        $permissionsString = '';

        foreach($this->permissions as $permission)
        {
            $permissionsString .= $permission->resource . $separator . $permission->action . "\n";
        }

        return $permissionsString;
    }

    /**
     * Add permissions from permission strings
     *
     * @param $permissionsString
     * @param string $separator
     */
    public function setPermissionsString($permissionsString, $separator = ':')
    {
        // First delete all permissions
        $this->permissions()->delete();

        $permissions = explode("\n", $permissionsString);

        foreach($permissions as $permission)
        {
            if(! $permission) continue;

            $pieces = explode($separator, $permission);

            $resource = $pieces[0];
            $action   = $pieces[1];

            // Create new permission for this application.
            $this->permissions()->create(compact('resource', 'action'));
        }
    }

    /**
     * @param $query
     * @param $apiKey
     * @return mixed
     */
    public function scopeByApiKey($query, $apiKey)
    {
        return $query->where('key', $apiKey);
    }

    /**
     * @return string
     */
    public function makeSignature()
    {
        return $this->generateSignature($this->key, $this->secret, time());
    }

    /**
     * @param $key
     * @param $timestamp
     * @param $secret
     * @return string
     */
    protected function generateSignature($key, $secret, $timestamp)
    {
        return md5($key.$timestamp.$secret);
    }

    /**
     * @param $signature
     * @param $tolerance (In seconds)
     * @return bool
     */
    public function checkSignature($signature, $tolerance = 4)
    {
        $current = time();

        for($i = 0; $i < $tolerance; $i ++)
        {
            if($signature === $this->generateSignature($this->key, $this->secret, $current)) {

                return true;
            }

            $current ++;
        }

        return false;
    }

    /**
     * @param $resource
     * @param $action
     * @return bool
     */
    public function checkPermissions($resource, $action)
    {
        foreach($this->permissions as $permission)
        {
            if($permission->match($resource, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $secret
     */
    public function setSecretAttribute($secret)
    {
        $this->attributes['secret'] = Crypt::encrypt($secret);
    }

    /**
     * @param $secret
     * @return mixed
     */
    public function getSecretAttribute($secret)
    {
        return Crypt::decrypt($secret);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(APIPermission::getClass(), 'application_id');
    }
}