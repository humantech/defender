<?php

namespace Artesaos\Defender\Contracts;

/**
 * Interface Defender.
 */
interface Defender
{
    /**
     * Get the current authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUser();

    /**
     * Check if the authenticated user has the given permission.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function hasPermission($permission, $force = false);

    /**
     * Check if the authenticated user has the given permission.
     *
     * @param string $permission
     * @param bool   $force
     *
     * @return bool
     */
    public function canDo($permission, $force = false);

    /**
     * Check if a permission with the given name exists.
     *
     * @param string $permissionName
     *
     * @return bool
     */
    public function permissionExists($permissionName);

    /**
     * Get the permission with the given name.
     *
     * @param string $permissionName
     *
     * @return \Artesaos\Defender\Permission|null
     */
    public function findPermission($permissionName);

    /**
     * Find a permission by its id.
     *
     * @param int $permissionId
     *
     * @return \Artesaos\Defender\Permission|null
     */
    public function findPermissionById($permissionId);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissionsList();

    /**
     * @param string $permissionName
     * @param string $readableName
     *
     * @return \Artesaos\Defender\Permission
     */
    public function createPermission($permissionName, $readableName = null);

    /**
     * @return \Artesaos\Defender\Javascript
     */
    public function javascript();
}
