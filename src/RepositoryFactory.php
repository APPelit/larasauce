<?php

namespace APPelit\LaraSauce;

interface RepositoryFactory
{
    /**
     * @param string $root
     * @param array $config
     * @return void
     */
    public function register(string $root, array $config);

    /**
     * @param string $root
     * @return \EventSauce\EventSourcing\AggregateRootRepository
     */
    public function root(string $root): \EventSauce\EventSourcing\AggregateRootRepository;
}
