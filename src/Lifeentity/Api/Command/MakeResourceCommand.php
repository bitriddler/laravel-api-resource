<?php namespace Lifeentity\Api\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeResourceCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'api:resource';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create new api resource.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        // Get resources as an array
        $resources = $this->argument('resources');

        $resources = explode(',', $resources);

        // Path to the resource classes
        $path      = app_path($this->option('path'));

        // Namespace for the resource classes
        $namespace = $this->option('namespace');

        // Template location
        $template = file_get_contents(__DIR__.'/resource_template.txt');

        foreach($resources as $resource)
        {
            $resource = trim($resource);

            // Resource filename
            $fileName = ucfirst($resource).'Resource';

            // Destination for the file
            $destination = "$path/$fileName.php";

            // Get source code
            $source = $this->createResourceTemplate($resource, $namespace, $template);

            // If file exists then ask if he wants to override
            if(file_exists($destination) && $this->ask('This will override the resource? [y|n] ') !== 'y')
            {
                continue;
            }

            file_put_contents($destination, $source);
        }
	}

    /**
     * @param $resource
     * @param $namespace
     * @param $template
     * @return mixed
     */
    protected function createResourceTemplate($resource, $namespace, $template)
    {
        $template = str_replace('{namespace}', $namespace ? "namespace $namespace;": '', $template);
        $template = str_replace('{Resource}'  , ucfirst($resource), $template);
        $template = str_replace('{resource}'  , $resource, $template);
        $template = str_replace('{resources}' , Str::plural($resource), $template);

        return $template;
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('resources', InputArgument::REQUIRED, 'Resources names.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'An example option.', 'resources/controllers'),
            array('namespace', null, InputOption::VALUE_OPTIONAL, 'An example option.', ''),
		);
	}

}
