<?php

namespace APPelit\LaraSauce\Util;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class LaraSauceMigration
{
    /**
     * Create all repositories
     *
     * @param string $table
     */
    public static function repositories(string $table)
    {
        static::messageRepository($table);
        static::snapshotRepository("{$table}_snapshots");
    }

    /**
     * Drop all repositories
     *
     * @param string $table
     */
    public static function dropRepositories(string $table)
    {
        static::dropMessageRepository($table);
        static::dropSnapshotRepository("{$table}_snapshots");
    }

    /**
     * Create the message repository
     *
     * @param string $tableName
     */
    public static function messageRepository(string $tableName)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->string('id', 36)->primary();
                $table->dateTime('time_of_recording', 6)->index();
                $table->string('aggregate_root_id', 36)->nullable()->index();
                $table->string('aggregate_root_type')->nullable()->index();
                $table->string('aggregate_root_version')->default(0)->index();
                $table->string('event_id', 36);
                $table->string('event_type');
                $table->json('payload');

                $table->unique(
                    ['aggregate_root_id', 'aggregate_root_type', 'aggregate_root_version'],
                    "{$tableName}_aggregate_root_unique"
                );
                $table->index(
                    ['aggregate_root_type', 'aggregate_root_id'],
                    "{$tableName}_aggregate_root_idx1"
                );
                $table->index(
                    ['aggregate_root_id', 'aggregate_root_type'],
                    "{$tableName}_aggregate_root_idx2"
                );
            });
        }
    }

    /**
     * Create the snapshot repository
     *
     * @param string $tableName
     */
    public static function snapshotRepository(string $tableName)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->string('aggregate_root_id', 36);
                $table->string('aggregate_root_type');
                $table->unsignedBigInteger('version')->default(0);
                $table->json('state')->nullable();

                $table->primary(['aggregate_root_id', 'aggregate_root_type'], "{$tableName}_primary");
            });
        }
    }

    /**
     * Drop the message repository
     *
     * @param string $tableName
     */
    public static function dropMessageRepository(string $tableName)
    {
        Schema::dropIfExists($tableName);
    }

    /**
     * Drop the snapshot repository
     *
     * @param string $tableName
     */
    public static function dropSnapshotRepository(string $tableName)
    {
        Schema::dropIfExists($tableName);
    }
}
