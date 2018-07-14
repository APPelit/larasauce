<?php

namespace APPelit\LaraSauce\Commands;

use APPelit\LaraSauce\LaraSauce;
use APPelit\LaraSauce\Projection\RebuildableProjection;
use EventSauce\EventSourcing\Consumer;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Container\Container;

final class RebuildProjections extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara-sauce:rebuild_projections
     {--f|force : Answer yes on all questions}
     {roots?* : The aggregate root(s) to rebuild}';

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
                !$this->confirm('No aggregate roots provided, do you wish to rebuild all aggregate root projections?')
            ) {
                return;
            }

            $roots = LaraSauce::roots();
        }

        if (empty($roots)) {
            $this->warn('No roots found');
            return;
        }

        foreach ($roots as $root) {
            $this->info(sprintf('Rebuilding root [%s]', $root));

            $configuration = LaraSauce::rootConfiguration($root);

            $messageRepository = $configuration->getRepository();

            if (!method_exists($messageRepository, 'retrieveEverything')) {
                $this->error(sprintf(
                    'Repository [%s] for root [%s] does not support retrieveEverything, skipping',
                    $root,
                    get_class($messageRepository)
                ));
                continue;
            }

            $projections = $configuration->getConsumers()->filter(function (Consumer $consumer) {
                return $consumer instanceof RebuildableProjection;
            });

            if ($projections->isEmpty()) {
                $this->error(sprintf('No rebuildable projections for root [%s], skipping', $root));
                continue;
            }

            $this->warn(sprintf('Starting rebuild for root [%s], do not stop this process', $root));

            $projections->each(function (RebuildableProjection $projection) {
                $projection->reset();
            });

            $progressBar = $this->output->createProgressBar();

            if (
                method_exists($messageRepository, 'countEverything') &&
                ($total = $messageRepository->countEverything()) >= 0
            ) {
                $progressBar->setMaxSteps($total);
            }

            foreach ($messageRepository->retrieveEverything() as $message) {
                $projections->each(function (Consumer $projection) use ($message) {
                    $projection->handle($message);
                });

                $progressBar->advance();
            }

            $progressBar->finish();

            if (count($roots) > 1) {
                $this->info(sprintf('Rebuild of root [%s] completed', $root));
            }
        }

        $this->info('Rebuild completed');
        return;
    }
}
