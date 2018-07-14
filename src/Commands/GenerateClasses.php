<?php

namespace APPelit\LaraSauce\Commands;

use APPelit\LaraSauce\LaraSauce;
use APPelit\LaraSauce\Projection\RebuildableProjection;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

final class GenerateClasses extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara-sauce:generate_classes
     {--f|force : Answer yes on all questions}
     {roots?* : The aggregate root(s) to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the commands and events for the specified root(s) from config';

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
        if (!$this->option('force') && !$this->confirmToProceed()) {
            return;
        }

        $roots = $this->argument('roots');
        if (is_string($roots)) {
            $roots = explode(',', $roots);
        }

        if (empty($roots)) {
            if (
                !$this->option('force') &&
                !$this->confirm('No aggregate roots provided, do you wish to generate classes for all applicable aggregate roots?')
            ) {
                return;
            }

            $roots = LaraSauce::roots();

            $roots = array_combine($roots, array_fill(0, count($roots), false));
        } else {
            $roots = array_combine($roots, array_fill(0, count($roots), true));
        }

        if (empty($roots)) {
            $this->warn('No roots found');
            return;
        }

        $progressBar = $this->output->createProgressBar(count($roots));

        /**
         * @var Collection|RebuildableProjection[] $projections
         */
        foreach ($roots as $root => $explicit) {
            $this->info(sprintf('Generating classes for root [%s]', $root));

            LaraSauce::generateClasses($root, $explicit);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info('Generation completed');
        return;
    }
}
