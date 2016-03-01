<?php

/**
 * Defender - Laravel 5 ACL Package
 * Author: PHP Artesãos.
 */
return [

    /*
     * Default Permission model used by Defender.
     */
    'permission_model' => Artesaos\Defender\Permission::class,

    /*
     * Permissions table name
     */
    'permission_table' => 'permissions',

    /*
     *
     */
    'permission_key' => 'permission_id',

    /*
     * Pivot table for permissions and users
     */
    'permission_user_table' => 'permission_user',

    /*
     * Forbidden callback
     */
    'forbidden_callback' => Artesaos\Defender\Handlers\ForbiddenHandler::class,

    /*
     * Use blade template helpers
     */
    'template_helpers' => true,

    /*
     * Use helper functions
     */
    'helpers' => true,

    /*
     * js var name
     */
    'js_var_name' => 'defender',

];
