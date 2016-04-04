<?php

namespace Artesaos\Defender;

use Illuminate\Contracts\Foundation\Application;
use Artesaos\Defender\Contracts\Defender as DefenderContract;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;

/**
 * Class Defender.
 */
class Defender implements DefenderContract
{
    /**
     * The Laravel Application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The PermissionRepository implementation.
     *
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var Javascript
     */
    protected $javascript;

    /**
     * Class constructor.
     *
     * @param Application          $app
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(Application $app, PermissionRepository $permissionRepository)
    {
        $this->app = $app;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get the current authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUser()
    {
        return $this->app['defender.auth']->user();
    }

    /**
     * Check if the authenticated user has the given permission.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function hasPermission($permission, $domainId, $moduleId)
    {
        if (! is_null($this->getUser())) {
            return $this->getUser()->hasPermission($permission, $domainId, $moduleId);
        }

        return false;
    }

    /**
     * Check if the authenticated user has the given permission.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function canDo($permission, $force = false)
    {
        if (! is_null($this->getUser())) {
            return $this->getUser()->canDo($permission, $force);
        }

        return false;
    }

    /**
     * Check if a permission with the given name exists.
     *
     * @param string $permissionName
     *
     * @return bool
     */
    public function permissionExists($permissionName)
    {
        return $this->permissionRepository->findByName($permissionName) !== null;
    }

    /**
     * Get the permission with the given name.
     *
     * @param string $permissionName
     *
     * @return \Artesaos\Defender\Permission|null
     */
    public function findPermission($permissionName)
    {
        return $this->permissionRepository->findByName($permissionName);
    }

    /**
     * Find a permission by its id.
     *
     * @param int $permissionId
     *
     * @return \Artesaos\Defender\Permission|null
     */
    public function findPermissionById($permissionId)
    {
        return $this->permissionRepository->findById($permissionId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissionsList()
    {
        return $this->permissionRepository->getList('name', 'id');
    }

    /**
     * @param string $permissionName
     * @param string $readableName
     *
     * @return Permission
     */
    public function createPermission($permissionName, $readableName = null, $moduleId = 0)
    {
        return $this->permissionRepository->create($permissionName, $readableName, $moduleId);
    }

    /**
     * @return Javascript
     */
    public function javascript()
    {
        if (! $this->javascript) {
            $this->javascript = new Javascript($this);
        }

        return $this->javascript;
    }
}
