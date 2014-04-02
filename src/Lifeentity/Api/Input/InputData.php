<?php namespace Lifeentity\Api\Input;


class InputData {

	/**
	 * Data for this input
	 * @var array
	 */
	protected $data = array();

	/**
	 * Constructor method
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * Get input data required
	 * @param  string $key
	 * @return string
	 * @throws InputException If key not found in the data array
	 */
	public function getRequired($key)
	{
		if(! $this->has($key))
		{
			throw new InputException("This key {{$key}} is required to continue this request");
		}

		return $this->get($key);
	}

	/**
	 * Has key
	 * @param  string  $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Get input data by key
	 * @param  string $key
	 * @param  string $default
	 * @return mixed
	 */
	public function get($key, $default = '')
	{
		return $this->has($key) ? $this->data[$key] : $default;
	}

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }
}