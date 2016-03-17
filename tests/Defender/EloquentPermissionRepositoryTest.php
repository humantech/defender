<?php

namespace Artesaos\Defender\Testing;

use Artesaos\Defender\Permission;
use Artesaos\Defender\Repositories\Eloquent\EloquentPermissionRepository;

/**
 * Class EloquentPermissionRepositoryTest.
 */
class EloquentPermissionRepositoryTest extends AbstractTestCase
{
    /**
     * Array of service providers.
     * @var array
     */
    protected $providers = [
        'Artesaos\Defender\Providers\DefenderServiceProvider',
    ];

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

        $this->defender = new Defender($this->app, $this->app['defender.permission']);
        $this->defender->setUser(User::first());
    }

    /**
     * Asserting if the User model has traits.
     */
    public function testUserShouldHasPermissionsTrait()
    {
        $this->assertUsingTrait(
            'Artesaos\Defender\Traits\HasDefender',
            'Artesaos\Defender\Testing\User'
        );

        $this->assertUsingTrait(
            'Artesaos\Defender\Traits\Permissions\InteractsWithPermissions',
            'Artesaos\Defender\Testing\User'
        );

        $this->assertUsingTrait(
            'Artesaos\Defender\Traits\Users\HasPermissions',
            'Artesaos\Defender\Testing\User'
        );
    }

    /**
     * Testing the criation of permissions.
     */
    public function testShouldCreatePermission()
    {
        $this->createPermission('users.index');

        $this->createPermission('users.create', 'Create Users');

        /** @var Permission $permission */
        /** @var User $user */
        list($permission, $user) = $this->createAndAttachPermission(
            'users.delete',
            ['name' => 'admin'],
            'Delete users'
        );

        $this->assertTrue($permission->users()->get()->contains($user->id));

        $this->assertTrue($user->existPermission('users.delete'));

        $this->assertInstanceOf(
            'Artesaos\Defender\Pivots\PermissionUserPivot',
            $user->permissions->first()->pivot
        );
    }

    /**
     * Testing the criation of permissions and domain associations.
     */
    public function testShouldCreatePermissionToModuleAndAttachToDomain()
    {
        $user = $this->defender->getUser();

        $permission = $this->defender->createPermission('users.create', 'Create Users', 1);

        $user->attachPermission($permission, ['domain_id' => 1]);

        $this->assertTrue($user->hasPermission('users.create', 1, 1));

        $this->assertFalse($user->hasPermission('users.create', 2, 1));

        $this->assertFalse($user->hasPermission('users.create', 1, 2));
    }

    /**
     * Create a permission and assert to see in database.
     * @param string $name
     * @param string $readableName
     * @return Permission
     */
    protected function createPermission($name, $readableName = null)
    {
        /** @var EloquentPermissionRepository $repository */
        $repository = $this->app['defender.permission'];

        $permission = $repository->create($name, $readableName);

        $where['name'] = $name;

        if ($readableName) {
            $where['readable_name'] = $readableName;
        }

        $this->seeInDatabase(
            config('defender.permission_table', 'permissions'),
            $where
        );

        return $permission;
    }

    /**
     * Create and Attach a Permission to User.
     * @param string $permission
     * @param User|array $user User or array of where clausules.
     * @param string $readableName Permission readable name.
     * @return array Array containing created $permission and $user.
     */
    protected function createAndAttachPermission($permission, $user, $readableName = null)
    {
        $permission = $this->createPermission($permission, $readableName);

        if (! ($user instanceof User)) {
            $user = User::where($user)->first();
        }

        $permission->users()->attach($user);

        $this->seePermissionAttachedToUserInDatabase($permission, $user);

        return [$permission, $user];
    }

    /**
     * Assert to see in Database a Permission attached to User.
     * @param Permission $permission
     * @param User $user
     */
    protected function seePermissionAttachedToUserInDatabase(Permission $permission, User $user)
    {
        $this->seeInDatabase(
            config('defender.permission_user_table', 'permission_user'),
            [
                config('defender.permission_key', 'permission_id') => $permission->id,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Assert to not see in Database a Permission attached to User.
     * @param Permission $permission
     * @param User $user
     */
    protected function notSeePermissionAttachedToUserInDatabase(Permission $permission, User $user)
    {
        $this->notSeeInDatabase(
            config('defender.permission_user_table', 'permission_user'),
            [
                config('defender.permission_key', 'permission_id') => $permission->id,
                'user_id' => $user->id,
            ]
        );
    }
}
