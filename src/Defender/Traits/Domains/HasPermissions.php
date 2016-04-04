<?php

namespace Artesaos\Defender\Traits\Domains;

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
            config('defender.domain_model'),
            config('defender.permission_user_table'),
            config('defender.domain_key'),
            config('defender.permission_key')
        )->withPivot('value', 'expires', 'module_id', 'user_id');
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
        $domainModel = app()['config']->get('defender.domain_model');

        if ($parent instanceof $domainModel) {
            return new PermissionUserPivot($parent, $attributes, $table, $exists);
        }

        return parent::newPivot($parent, $attributes, $table, $exists);
    }
}
