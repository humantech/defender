<?php

namespace Artesaos\Defender\Repositories\Eloquent;

use Artesaos\Defender\Permission;
use Illuminate\Contracts\Foundation\Application;
use Artesaos\Defender\Exceptions\PermissionExistsException;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;
use Carbon\Carbon;

/**
 * Class EloquentPermissionRepository.
 */
class EloquentPermissionRepository extends AbstractEloquentRepository implements PermissionRepository
{
    /**
     * @param Application $app
     * @param Permission  $model
     */
    public function __construct(Application $app, Permission $model)
    {
        parent::__construct($app, $model);
    }

    /**
     * Create a new permission using the given name.
     *
     * @param string $permissionName
     * @param string $readableName
     *
     * @throws PermissionExistsException
     *
     * @return Permission
     */
    public function create($permissionName, $readableName = null, $moduleId = 0)
    {
        if (! is_null($this->model->where('name', '=', $permissionName)->where('module_id', '=', $moduleId)->first())) {
            throw new PermissionExistsException('The permission '.$permissionName.' already exists'); // TODO: add translation support
        }

        // Do we have a display_name set?
        $readableName = is_null($readableName) ? $permissionName : $readableName;

        return $permission = $this->model->create([
            'name'          => $permissionName,
            'readable_name' => $readableName,
            'module_id'     => $moduleId,
        ]);
    }

    /**
     * @param $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivesByUser($user)
    {
        $table = $user->permissions()->getTable();

        return $user->permissions()
            ->where($table.'.value', true)
            ->where(function ($q) use ($table) {
                $q->where($table.'.expires', '>=', Carbon::now());
                $q->orWhereNull($table.'.expires');
            })
            ->get();
    }

    /**
     * @param $user
     * @param $domainId
     * @param $moduleId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivesByUserInModule($user, $domainId, $moduleId)
    {
        $table = $user->permissions()->getTable();
        $permissionsTable = config('defender.permission_table', 'permissions');

        return $user->permissions()
            ->where($permissionsTable.'.module_id', $moduleId)
            ->where($table.'.value', true)
            ->where($table.'.domain_id', $domainId)
            ->where(function ($q) use ($table) {
                $q->where($table.'.expires', '>=', Carbon::now());
                $q->orWhereNull($table.'.expires');
            })
            ->get();
    }
}
