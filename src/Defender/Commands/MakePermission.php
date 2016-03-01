<?php

namespace Artesaos\Defender\Commands;

use Illuminate\Console\Command;
use Artesaos\Defender\Contracts\Repositories\UserRepository;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;

/**
 * Class MakePermission.
 */
class MakePermission extends Command
{
    /**
     * Defender Permissions Repository.
     *
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * User which implements UserRepository.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defender:make:permission
                            {name : Name of the permission}
                            {readableName : A readable name of the permission}
                            {--user= : User id. Attach permission to user with the provided id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a permission';

    /**
     * Create a new command instance.
     *
     * @param PermissionRepository $permissionRepository
     * @param UserRepository       $userRepository
     */
    public function __construct(PermissionRepository $permissionRepository, UserRepository $userRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $readableName = $this->argument('readableName');
        $userId = $this->option('user');

        $permission = $this->createPermission($name, $readableName);

        if ($userId) {
            $this->attachPermissionToUser($permission, $userId);
        }
    }

    /**
     * Create permission.
     *
     * @param string $name
     * @param string $readableName
     *
     * @return \Artesaos\Defender\Permission
     */
    protected function createPermission($name, $readableName)
    {
        // No need to check is_null($permission) as create() throwsException
        $permission = $this->permissionRepository->create($name, $readableName);

        $this->info('Permission created successfully');

        return $permission;
    }

    /**
     * Attach Permission to user.
     *
     * @param \Artesaos\Defender\Permission $permission
     * @param int                           $userId
     */
    protected function attachPermissionToUser($permission, $userId)
    {
        // Check if user exists
        if ($user = $this->userRepository->findById($userId)) {
            $user->attachPermission($permission);
            $this->info('Permission attached successfully to user');
        } else {
            $this->error('Not possible to attach permission. User not found');
        }
    }
}
