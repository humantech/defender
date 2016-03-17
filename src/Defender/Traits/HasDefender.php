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
     * @param int    $domainId
     * @param int    $moduleId
     *
     * @return bool
     */
    public function hasPermission($permission, $domainId, $moduleId)
    {
        $permissions = app('defender.permission')->getActivesByUserInModule($this, $domainId, $moduleId)->lists('name')->toArray();

        return in_array($permission, $permissions);
    }

    /**
     * Returns if the current user has the given permission.
     *
     * @param string $permission
     * @param int    $domainId
     * @param int    $moduleId
     *
     * @return bool
     */
    public function canDo($permission, $domainId, $moduleId)
    {
        return $this->hasPermission($permission, $moduleId, $domainId);
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
                unset(
                    $permission->pivot->user_id,
                    $permission->pivot->permission_id,
                    $permission->pivot->value,
                    $permission->pivot->expires,
                    $permission->created_at,
                    $permission->updated_at);

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
