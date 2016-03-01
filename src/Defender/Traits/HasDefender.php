<?php

namespace Artesaos\Defender\Traits;

use Artesaos\Defender\Traits\Users\HasPermissions;

/**
 * Trait HasDefender.
 */
trait HasDefender
{
    use HasPermissions;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $cachedPermissions;

    /**
     * Returns if the current user has the given permission.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function hasPermission($permission, $force = false)
    {
        $permissions = $this->getAllPermissions($force)->lists('name')->toArray();

        return in_array($permission, $permissions);
    }

    /**
     * Checks for permission
     * If has superuser group automatically passes.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function canDo($permission, $force = false)
    {
        return $this->hasPermission($permission, $force);
    }

    /**
     * Retrieve all user permissions.
     *
     * @param bool $force
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions($force = false)
    {
        if (empty($this->cachedPermissions) or $force) {
            $this->cachedPermissions = $this->getFreshAllPermissions();
        }

        return $this->cachedPermissions;
    }

    /**
     * Get fresh permissions from database.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getFreshAllPermissions()
    {
        $permissions = app('defender.permission')->getActivesByUser($this);

        $permissions = $permissions
            ->map(function ($permission) {
                unset($permission->pivot, $permission->created_at, $permission->updated_at);

                return $permission;
            });

        return $permissions->toBase();
    }

    /**
     * Find a user by its id.
     *
     * @param int $id
     *
     * @return \Artesaos\Defender\Contracts\User
     */
    public function findById($id)
    {
        return $this->find($id);
    }
}
