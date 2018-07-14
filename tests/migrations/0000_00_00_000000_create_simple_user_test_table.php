<?php

use APPelit\LaraSauce\Util\LaraSauceMigration;
use Illuminate\Database\Migrations\Migration;

class CreateSimpleUserTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LaraSauceMigration::repositories('simple_user_test');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LaraSauceMigration::dropRepositories('simple_user_test');
    }
}
