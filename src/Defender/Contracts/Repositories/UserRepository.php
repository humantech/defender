<?php

namespace Artesaos\Defender\Contracts\Repositories;

interface UserRepository extends AbstractRepository
{
    public function attachPermission($permissionName, array $options = []);
}
