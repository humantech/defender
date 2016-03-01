<?php

namespace Artesaos\Defender;

use Illuminate\Database\Eloquent\Model;
use Artesaos\Defender\Pivots\PermissionUserPivot;

/**
 * Class Permission.
 */
class Permission extends Model
{
    /**
     * @var
     */
    protected $table;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'readable_name',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('defender.permission_table', 'permissions');
    }

    /**
     * Many-to-many permission-user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            config('auth.model'),
            config('defender.permission_user_table'),
            config('defender.permission_key'),
            'user_id'
        )->withPivot('value', 'expires');
    }

    /**
     * @param Model  $parent
     * @param array  $attributes
     * @param string $table
     * @param bool   $exists
     *
     * @return PermissionUserPivot|\Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        $userModel = app()['config']->get('auth.model');

        if ($parent instanceof $userModel) {
            return new PermissionUserPivot($parent, $attributes, $table, $exists);
        }

        return parent::newPivot($parent, $attributes, $table, $exists);
    }
}
