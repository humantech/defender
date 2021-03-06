<?php

namespace Artesaos\Defender\Contracts\Repositories;

/**
 * Interface PermissionRepository.
 */
interface PermissionRepository extends AbstractRepository
{
    /**
     * Create a new permission using the given name.
     *
     * @param string $permissionName
     * @param string $readableName
     *
     * @throws \Artesaos\Defender\Exceptions\PermissionExistsException
     *
     * @return \Artesaos\Defender\Permission;
     */
    public function create($permissionName, $readableName = null, $moduleId = 0);
}
