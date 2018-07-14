<?php

use APPelit\LaraSauce\Util\LaraSauceMigration;
use Illuminate\Database\Migrations\Migration;

class CreateCommandTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LaraSauceMigration::repositories('command_test');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LaraSauceMigration::dropRepositories('command_test');
    }
}
