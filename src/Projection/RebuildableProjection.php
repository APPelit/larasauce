<?php

namespace APPelit\LaraSauce\Projection;

interface RebuildableProjection extends Projection
{
    /**
     * @return void
     */
    public function reset();
}
