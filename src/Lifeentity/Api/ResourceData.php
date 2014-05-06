<?php namespace Lifeentity\Api;

class ResourceData {

    /**
     * Action name
     * @var string
     */
    protected $action;

    /**
     * Request method (GET, PUT, POST, DELETE)
     * @var string
     */
    protected $method;

    /**
     * Arguments array
     * @var array
     */
    protected $arguments = array();

    /**
     * Constructor
     * @param string $action
     * @param array $arguments
     * @param string $method
     */
    public function __construct($action, $method, array $arguments = array())
    {
        $this->action    = $action;
        $this->arguments = $arguments;
        $this->method    = strtoupper($method);
    }

    /**
     * Factory method
     * @param  string $uri
     * @param  string $method
     * @return ResourceData
     */
    public static function make($uri, $method)
    {
        $arguments = explode('/', $uri);

        $action = array_shift($arguments);

        return new static($action, $method, $arguments);
    }

    /**
     * Get action name
     * @return string
     */
    public function getAction()
    {
        list($action, ) = $this->getActionAndArguments();

        return $action;
    }

    /**
     * Get arguments
     * @return array
     */
    public function getArguments()
    {
        list(, $arguments) = $this->getActionAndArguments();

        return $arguments;
    }

    /**
     * This will return the resource action and arguments
     *
     * @return array
     */
    protected function getActionAndArguments()
    {
        // resource/:id
        if(is_numeric($this->action) && empty($this->arguments))
        {
            // GET resource/:id
            if($this->method === 'GET')
            {
                return array('show', array($this->action));
            }

            // POST resource/:id
            elseif($this->method === 'PUT')
            {
                return array('update', array($this->action));
            }

            // PUT resource/:id
            elseif($this->method === 'DELETE')
            {
                return array('destroy', array($this->action));
            }
        }


        // resource/
        elseif(empty($this->action) && empty($this->arguments))
        {
            // GET resource/
            if($this->method === 'GET')
            {
                return array('index', array());
            }

            // Post resource/
            elseif($this->method === 'POST')
            {
                return array('store', array());
            }
        }

        // Default action and arguments
        return array(Str::camel(strtolower($this->method).'-'.$this->action), $this->arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return rtrim($this->method .' /'. $this->action.'/'.implode('/',$this->arguments), '/');
    }
}