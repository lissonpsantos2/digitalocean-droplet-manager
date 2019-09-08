<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use GrahamCampbell\DigitalOcean\Facades\DigitalOcean;
use Exception;

class CreateDroplet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:droplet {droplet_name} {droplet_size} {total_count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create droplet';

    /**
     * The droplet sizes.
     *
     * @var array
     */
    protected $droplet_sizes = [
        '1x' => 's-1vcpu-1gb', // U$ 5,00
        '2x' => 's-1vcpu-2gb', // U$ 10,00
        '3x' => 's-1vcpu-3gb', // U$ 15,00
        '4x' => 's-2vcpu-4gb', // U$ 20,00
        '8x' => 's-4vcpu-8gb', // U$ 40,00
    ];

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
    public function handle()
    {
        $droplet_image_name = env('DO_DEFAULT_IMAGE');
        $default_region = env('DO_DEFAULT_REGION');
        $default_tag = env('DO_DROPLET_DEFAULT_TAG');

        if (!$droplet_image_name) {
            throw new Exception("DO_DEFAULT_IMAGE env variable is not set!");
        }

        if (!$default_region) {
            throw new Exception("DO_DEFAULT_REGION env variable is not set!");
        }

        if (!$default_tag) {
            throw new Exception("DO_DROPLET_DEFAULT_TAG env variable is not set!");
        }

        $desired_images = collect(DigitalOcean::image()->getAll())
            ->where('name', $droplet_image_name);

        if (!$desired_images->count()) {
            throw new Exception("No $droplet_image_name image found!");
        }

        $desired_image_id = $desired_images->first()->id;

        $droplet_manager = DigitalOcean::droplet();

        $max_tries = 3;
        $error_messages = [];

        for ($i = 0; $i < $max_tries; $i++) {
            try {
                $this->_createDroplets(
                    $droplet_manager,
                    $default_region,
                    $desired_image_id,
                    $default_tag
                );
                break;
            } catch (Exception $e) {
                $error_messages[] = $e->getMessage();
            }
        }

        if ($i == $max_tries) {
            // ADD your notification here
        }
    }

    /**
     * Create all the desired droplets.
     *
     * @return void
     */
    private function _createDroplets($droplet_manager, $default_region, $image_id, $tag)
    {
        $total_count = (int) $this->argument('total_count');
        $droplet_name = $this->argument('droplet_name');
        $droplet_size = $this->argument('droplet_size');
        $cloud_config = file_get_contents(storage_path('docs/cloud-config.yaml'));
        $droplet_names = collect();

        for ($i = 0; $i < $total_count; $i++) {
            $custom_number = rand(10000, 99999);
            $name = $droplet_name . $droplet_size . '-' . $custom_number;
            $droplet_names->push($name);
        }

        $droplet_manager
            ->create(
                $droplet_names->toArray(),
                $default_region,
                $this->droplet_sizes[$droplet_size],
                $image_id,
                false,
                false,
                true,
                [],
                $cloud_config,
                true,
                [],
                [
                    $tag,
                ]
            );
    }
}
