<?php

namespace Artesaos\Defender\Traits\Modules;

use Illuminate\Database\Eloquent\Model;
use Artesaos\Defender\Pivots\PermissionUserPivot;
use Artesaos\Defender\Traits\Permissions\InteractsWithPermissions;

/**
 * Trait HasPermissions.
 */
trait HasPermissions
{
    use InteractsWithPermissions;

    /**
     * Many-to-many permission-user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(
            config('defender.module_model'),
            config('defender.permission_user_table'),
            config('defender.module_key'),
            config('defender.permission_key')
        )->withPivot('value', 'expires', 'domain_id', 'user_id');
    }

    /**
     * @param Model  $parent
     * @param array  $attributes
     * @param string $table
     * @param bool   $exists
     *
     * @return PermissionUserPivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        $permissionModel = app()['config']->get('defender.permission_model');

        if ($parent instanceof $permissionModel) {
            return new PermissionUserPivot($parent, $attributes, $table, $exists);
        }

        return parent::newPivot($parent, $attributes, $table, $exists);
    }
}
