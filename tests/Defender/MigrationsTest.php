<?php

namespace Artesaos\Defender\Testing;

/**
 * Class DefenderTest.
 */
class MigrationsTest extends AbstractTestCase
{
    /**
     * Array of service providers.
     * @var array
     */
    protected $providers = [
        'Artesaos\Defender\Providers\DefenderServiceProvider',
    ];

    /**
     * Should migrate.
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrate([
            $this->stubsPath('database/migrations'),
            $this->resourcePath('migrations'),
        ]);
    }

    /**
     * Should all tables exists.
     */
    public function testShouldSeeTablesOnDatabase()
    {
        /** @var \Illuminate\Database\Schema\Builder $schema */
        $schema = $this->app['db']->connection()->getSchemaBuilder();

        $tables = [
            /*
             * Users Table.
             */
            config('auth.table', 'users') => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],

            /*
             * Permissions Table.
             */
            config('defender.permission_table', 'permissions') => [
                'id',
                'name',
                'readable_name',
                'created_at',
                'updated_at',
            ],

            /*
             * Permissions User Relational Table.
             */
            config('defender.permission_user_table', 'permission_user') => [
                'user_id',
                config('defender.permission_key', 'permission_id'),
                'value',
                'expires',
            ],
        ];

        foreach ($tables as $table => $columns) {
            $this->assertTrue($schema->hasTable($table), sprintf(
                'Table \'%s\' not found in database.',
                $table
            ));

            /*
             * Intentional foreach.
             * This could be replaced by:
             * $this->assertTrue($schema->hasColumns($table, $columns)).
             */
            foreach ($columns as $col) {
                $this->assertTrue(
                    $schema->hasColumn($table, $col),
                    sprintf('Table \'%s\' has no column \'%s\' in database.', $table, $col)
                );
            }
        }
    }
}
