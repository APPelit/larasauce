<?php

namespace APPelit\LaraSauce\Commands;

use APPelit\LaraSauce\LaraSauce;
use APPelit\LaraSauce\Projection\RebuildableProjection;
use EventSauce\EventSourcing\Consumer;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

final class RebuildProjection extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara-sauce:rebuild_projection
     {--f|force : Answer yes on all questions}
     {projection : The projection to rebuild}
     {roots?* : The aggregate root(s) to rebuild}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the specified projection (for the specified roots)';

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

        $projection = $this->argument('projection');
        if (
        is_object($projection) ?
            !($projection instanceof RebuildableProjection) :
            !in_array(RebuildableProjection::class, class_implements($projection))
        ) {
            $this->error(sprintf(
                'Projection [%s] does not implement [%s]',
                is_object($projection) ? get_class($projection) : $projection,
                RebuildableProjection::class
            ));
            return;
        }

        $roots = $this->argument('roots');
        if (is_string($roots)) {
            $roots = explode(',', $roots);
        }

        if (empty($roots)) {
            if (
                !$this->option('force') &&
                !$this->confirm('No aggregate roots provided, do you wish to rebuild this projection for all applicable aggregate roots?')
            ) {
                return;
            }

            $roots = LaraSauce::roots();
        }

        if (empty($roots)) {
            $this->warn('No roots found');
            return;
        }

        $projectionClass = is_object($projection) ? get_class($projection) : $projection;

        $roots = collect($roots)
            ->mapWithKeys(function (string $root) use ($projectionClass) {
                return [
                    $root => LaraSauce::rootConfiguration($root)
                        ->getConsumers()
                        ->filter(function (Consumer $consumer) use ($projectionClass) {
                            return get_class($consumer) === $projectionClass || in_array($projectionClass, class_parents($consumer));
                        })
                ];
            })
            ->filter(function (Collection $consumers) {
                return $consumers->count();
            });

        if ($roots->isEmpty()) {
            $this->error('No aggregate roots use the specified projection');
            return;
        }

        /**
         * @var Collection|RebuildableProjection[] $projections
         */
        foreach ($roots as $root => $projections) {
            $this->info(sprintf('Rebuilding root [%s]', $root));

            $messageRepository = LaraSauce::rootConfiguration($root)->getRepository();

            if (!method_exists($messageRepository, 'retrieveEverything')) {
                $this->error(sprintf(
                    'Repository [%s] for root [%s] does not support retrieveEverything, skipping',
                    $root,
                    get_class($messageRepository)
                ));
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
