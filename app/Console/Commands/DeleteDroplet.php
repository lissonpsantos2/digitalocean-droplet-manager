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
        // ADD HERE YOUR PROTECTED DROPLETS
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

        $droplets = $droplet_manager->getAll();

        foreach ($droplets as $key => $droplet) {
            if (!in_array($droplet->name, $this->protected_droplets) && preg_match('/^' . $name_pattern . $droplet_size . '-[0-9]{5}$/', $droplet->name)) {
                $droplet_manager->delete($droplet->id);
            }
        }
    }
}
