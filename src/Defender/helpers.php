<?php

if (! function_exists('defender')) {
    /**
     * Get a defender instance.
     *
     * @return \Artesaos\Defender\Defender
     */
    function defender()
    {
        return app('defender');
    }
}

if (! function_exists('hasPermission')) {
    /**
     * Check if the current user has some permissions.
     *
     * @param string|array $permissions
     *
     * @return bool
     */
    function hasPermission($permissions)
    {
        if (! is_array($permissions)) {
            $permissions = func_get_args();
        }

        $user = defender()->getUser();

        if (is_null($user)) {
            return false;
        }

        $userPermissions = $user->getPermissions();

        $matches = array_intersect($permissions, $userPermissions);

        return count($matches) > 0;
    }
}
