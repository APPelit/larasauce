<?php

namespace APPelit\LaraSauce\Commands;

use APPelit\LaraSauce\LaraSauce;
use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot;
use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRootRepository;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Container\Container;

final class Snapshot extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara-sauce:snapshot
     {--scheduled : This is a scheduled snapshot}
     {--full : Perform a full rebuild}
     {roots?* : The aggregate root(s) to snapshot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild all rebuildable projections (for the specified roots)';

    /** @var Container */
    private $container;

    /**
     * Create a new command instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $roots = $this->argument('roots');
        if (is_string($roots)) {
            $roots = explode(',', $roots);
        }

        if (empty($roots)) {
            if (
                !$this->option('scheduled') &&
                !$this->confirm('No aggregate roots provided, do you wish to rebuild all aggregate root projections?')
            ) {
                return;
            }

            $roots = LaraSauce::roots();
        }

        $roots = array_filter($roots, function (string $root) {
            return is_subclass_of($root, SnapshotAggregateRoot::class) && LaraSauce::root($root) instanceof SnapshotAggregateRootRepository;
        });

        if (empty($roots)) {
            $this->warn('No snapshotable roots found');
            return;
        }

        $progressBar = $this->output->createProgressBar(count($roots));

        foreach ($roots as $root) {
            /** @var SnapshotAggregateRootRepository $repository */
            $repository = LaraSauce::root($root);

            $this->warn(sprintf('Starting snapshot for root [%s], do not stop this process', $root));

            $repository->snapshotAll($this->option('full'));

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info('Snapshot completed');
        return;
    }
}
