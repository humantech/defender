<?php

/**
 * Created by PhpStorm.
 * User: vluzrmos
 * Date: 12/07/15
 * Time: 01:01.
 */
namespace Artesaos\Defender\Testing;

class CommandsTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrate([
            $this->stubsPath('database/migrations'),
            $this->resourcePath('migrations'),
        ]);

        $this->seed([
            'UserTableSeeder',
        ]);
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [
            'Artesaos\Defender\Providers\DefenderServiceProvider',
        ];
    }

    /**
     * Creating a Permission.
     */
    public function testCommandShouldMakeAPermission()
    {
        $this->artisan('defender:make:permission', ['name' => 'a.permission', 'readableName' => 'A permission.']);

        $this->seeInDatabase(
            config('defender.permission_table', 'permissions'),
            [
                'name' => 'a.permission',
                'readable_name' => 'A permission.',
            ]
        );
    }

    /**
     * Creating a permission to User.
     */
    public function testCommandShouldMakeAPermissionToUser()
    {
        $this->artisan('defender:make:permission', ['name' => 'user.index', 'readableName' => 'List Users', '--user' => 1]);

        $this->seeInDatabase(
            config('defender.permission_table', 'permissions'),
            [
                'name' => 'user.index',
                'readable_name' => 'List Users',
            ]
        );

        $user = User::find(1);

        $this->assertEquals('user.index', $user->permissions->where('name', 'user.index')->first()->name);
    }
}
