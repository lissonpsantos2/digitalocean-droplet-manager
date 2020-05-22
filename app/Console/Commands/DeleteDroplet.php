<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use GrahamCampbell\DigitalOcean\Facades\DigitalOcean;
use Exception;

class DeleteDroplet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:droplet {droplet_name} {droplet_size}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete droplet';

    /**
     * Protected droplets.
     *
     * @var array
     */
    protected $protected_droplets = [
        'umbutech-prod-db',
        'umbutech-util',
        'umbutech-common',
        'umbutech-util-db',
        'umbutech-test',
        'umbutech-db-test',
        'umbutech-util',
    ];

    /**
     * Delete a new command instance.
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
        $droplet_manager = DigitalOcean::droplet();
        $name_pattern = $this->argument('droplet_name');
        $droplet_size = $this->argument('droplet_size');
        $droplets_name_size_match_ids = [];
        $droplets_name_match_ids = [];

        $droplets = $droplet_manager->getAll();


        foreach ($droplets as $key => $droplet) {
            if (!in_array($droplet->name, $this->protected_droplets) && preg_match('/^' . $name_pattern . $droplet_size . '-[0-9]{5}$/', $droplet->name)) {
                $droplets_name_size_match_ids[] = $droplet->id;
            }
            if (!in_array($droplet->name, $this->protected_droplets) && preg_match('/^' . $name_pattern . '*/', $droplet->name)) {
                $droplets_name_match_ids[] = $droplet->id;
            }
        }

        if (env('DO_VERIFY_ON_DROPLET_DELETION') && count($droplets_name_size_match_ids) == count($droplets_name_match_ids)) {
            throw new Exception('Something went wrong, After the scheduled deletion there will be no server left!');
        }

        foreach ($droplets_name_size_match_ids as $droplet_id) {
            $droplet_manager->delete($droplet_id);
        }
    }
}
