# Defender
----------

#### This is a variant from [artesaos/defender](https://github.com/artesaos/defender) without all role features.

Defender is a Access Control List (ACL) Solution for Laravel 5.1. **(Not compatible with Laravel 5.2)**  
With security and usability in mind, this project aims to provide you a safe way to control your application access without losing the fun of coding.

## Installation

### 1. Dependency

Using <a href="https://getcomposer.org/" target="_blank">composer</a>, execute the following command to automatically update your `composer.json`:

```shell
composer require humantech/defender
```

or manually update your `composer.json` file

```json
{
    "require": {
        "humantech/defender": "~0.1"
    }
}
```

### 2. Provider

You need to update your application configuration in order to register the package, so it can be loaded by Laravel. Just update your `config/app.php` file adding the following code at the end of your `'providers'` section:

```php
// file START ommited
    'providers' => [
        // other providers ommited
        \Artesaos\Defender\Providers\DefenderServiceProvider::class,
    ],
// file END ommited
```

### 3. User Class

On your User class, add the trait `Artesaos\Defender\Traits\HasDefender` to enable the creation of permissions:

```php
<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Artesaos\Defender\Traits\HasDefender;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, HasDefender;
...
```

#### 4. Publishing configuration file and migrations

To publish the default configuration file and database migrations, execute the following command:

```shell
php artisan vendor:publish
```

Execute the migrations, so that the tables on you database are created:

```shell
php artisan migrate
```

You can also publish only the configuration file or the migrations:

```shell
php artisan vendor:publish --tag=config
```
Or
```shell
php artisan vendor:publish --tag=migrations
```

If you already published defender files, but for some reason you want to override previous published files, add the `--force` flag.

### 5. Facade (optional)
In order to use the `Defender` facade, you need to register it on the `config/app.php` file, you can do that the following way:

```php
// config.php file
// file START ommited
    'aliases' => [
        // other Facades ommited
        'Defender' => \Artesaos\Defender\Facades\Defender::class,
    ],
// file END ommited
```

### 6. Defender Middlewares (optional)
If you have to control the access Defender provides middlewares to protect your routes.
If you have to control the access through the Laravel routes, Defender has some built-in middlewares for the trivial tasks. To use them, just put it in your `app/Http/Kernel.php` file.

```php
protected $routeMiddleware = [
    'auth'            => \App\Http\Middleware\Authenticate::class,
    'auth.basic'      => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,

    // Access control using permissions
    'needsPermission' => \Artesaos\Defender\Middlewares\NeedsPermissionMiddleware::class,
];
```

You'll see how to use the middlewares below.

#### 6.1 - Create your own middleware

If the built-in middlewares doesn't fit your needs, you can make your own by using [Defender's API](#using-the-facade) to control the access.

## Usage

Defender handles only access control. The authentication is still made by Laravel's `Auth`.

### Creating permissions

#### With commands

You can use these commands to create permissions for you application.

```shell
php artisan defender:make:permission users.index "List all the users" # creates the permission
php artisan defender:make:permission users.create "Create user" --user=1 # creates the permission and attaches it to user where id=1
php artisan defender:make:permission users.destroy "Delete user" # creates the permission
```

#### With the seeder or artisan tinker

You can also use the Defender's API. You can create a Laravel Seeder or use `php artisan tinker`.

```php
use App\User;

// The first parameter is the permission name
// The second is the "friendly" version of the name. (usually for you to show it in your application).
$permission =  Defender::createPermission('user.create', 'Create Users');

// You can assign permission directly to a user.
$user = User::find(1);
$user->attachPermission($permission);
```

### Using the middleware

To protect your routes, you can use the built-in middlewares.

> Defender requires Laravel's Auth, so, use the `auth` middleware before the Defender's middleware that you intend to use.

#### Checking Permissions: needsPermissionMiddleware

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission'], 'shield' => 'user.create', function()
{
    return 'Yes I can!';
}]);
```

If you're using Laravel 5.1 it's possible to use Middleware Parameters.

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission:user.index'], function() {
    return 'Yes I can!';
}]);
```

With this syntax it's also possible to use the middlewaren within your controllers.

```php
$this->middleware('needsPermission:user.index');
```

You can pass an array of permissions to check on.

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission'], 'shield' => ['user.index', 'user.create'], function()
{
    return 'Yes I can!';
}]);
```

When using middleware parameters, use a `|` to separate multiple permissions.

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission:user.index|user.create'], function() {
    return 'Yes I can!';
}]);
```

Or within controllers:

```php
$this->middleware('needsPermission:user.index|user.create');
```

When you pass an array of permissions, the route will be fired only if the user has all the permissions. However, if you want to allow the access to the route when the user has at least one of the permissions, just add `'any' => true`.

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission'], 'shield' => ['user.index', 'user.create'], 'any' => true, function()
{
    return 'Yes I can!';
}]);
```

Or, with middleware parameters, pass it as the 2nd parameter

```php
Route::get('foo', ['middleware' => ['auth', 'needsPermission:user.index|user.create,true'], function() {
    return 'Yes I can!';
}]);
```

Or within controllers:

```php
$this->middleware('needsPermission:user.index|user.create,true');
```

### Using in Views

Laravel's Blade extension for using Defender.

#### @shield

```
@shield('user.index')
    shows your protected stuff
@endshield
```

```
@shield('user.index')
    shows your protected stuff
@else
    shows the data for those who doesn't have the user.index permission
@endshield
```

#### Using javascript helper

The stand provides helper for when you need to interact with the user permissions on the front-end.

```php
echo Defender::javascript()->render();
// or
echo app('defender')->javascript()->render();
// or
echo app('defender.javascript')->render();
```

This helper injects a javascript code with all permissions of the current user.

----------

### Using the Facade

With the Defender's Facade you can access the API and use it at any part of your application.

##### `Defender::hasPermission($permission)`:

Check if the logged user has the `$permission`.

##### `Defender::canDo($permission)`:

Check if the logged user has the `$permission`.

##### `Defender::permissionExists($permissionName)`:

Check if the permission `$permissionName` exists in the database.

##### `Defender::findPermission($permissionName)`:

Find the permission in the database by the name `$permissionName`.

##### `Defender::findPermissionById($permissionId)`:

Find the permission in the database by the ID `$permissionId`.

##### `Defender::createPermission($permissionName)`:

Create a new permission in the database.

##### `Defender::javascript()->render()`:

Returns a javascript script with a list of all permissions of the current user.
The variable name can be modified.

----------

### Using the trait

To add the Defender's features, you need to add the trait `HasDefender` in you User model (usually `App\User`).

```php
<?php namespace App;

// Declaration of other omitted namespaces
use Artesaos\Defender\Traits\HasDefender;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword, HasDefender;

    // Rest of the class
}
```

This trait, beyond configuring the relationships, will add the following methods to your object `App\User`:

##### `public function hasPermission($permission)`:

This method checks if the logged user has the permission `$permission`

```php
public function foo(Authenticable $user)
{
    if ($user->hasPermission('user.create'));
}
```

----------

##### `public function attachPermission($permission, array $options = array())`:

Attach the user to the permission `$permission`. The `$permission` variable is an instance of the `Artesaos\Defender\Permission` class.

```php
public function foo(Authenticable $user)
{
    $permission = Defender::findPermission('user.create');

    $user->attachPermission($permission, [
        'value' => true // true = has the permission, false = doesn't have the permission,
    ]);
}
```

----------

##### `public function detachPermission($permission)`:

Remove the permission `$permission` from the user. The `$permission` variable might be an instance of the `Artesaos\Defender\Permission` class or an array of `ids` with the ids of the permissions to be removed.

```php
public function foo(Authenticable $user)
{
    $permission = Defender::findPermission('user.create');
    $user->detachPermission($permission);

    // or

    $permissions = [1, 3];
    $user->detachPermission($permissions);
}
```

----------

##### `public function syncPermissions(array $permissions)`:

This is like the method `attachPermission`, but only the permissions in the array `$permissions` be on the relationship after the method runs.

```php
public function foo(Authenticable $user)
{
    $permissions = [
        1 => ['value' => false],
        2 => ['value' => true,
        3 => ['value' => true]
    ];

    $user->syncPermissions($permissions);
}
```

----------

##### `public function revokePermissions()`:

Remove all the user permissions.

```php
public function foo(Authenticable $user)
{
    $user->revokePermissions();
}
```

----------

##### `public function revokeExpiredPermissions()`:

Remove all the temporary expired pemissions from the user. More about temporary permissions below.

```php
public function foo(Authenticable $user)
{
    $user->revokeExpiredPermissions();
}
```

----------

### Temporary permissions

One of the coolest Defender's features it's to add temporary permissions to a group or an user.

#### For example

Below we revoke the permission `user.create` for the user during 7 days.

```php
public function foo()
{
    $userX = App\User::find(3);
    $permission = Defender::findPermission('user.create');

    $userX->attachPermission($permission, [
        'value' => false, // false means that he will not have the permission,
        'expires' => \Carbon\Carbon::now()->addDays(7) // Daqui a quanto tempo essa permissão irá expirar
    ]);

}
```

After 7 days, the user will take the permission again.

To allow that a user have temporary access to perform a given action, just set the `expires` key. The `value` key will be `true` by default.

```php
public function foo()
{
    $user = App\User::find(1);
    $permission = Defender::findPermission('user.create');

    $user->attachPermission($permission, [
        'expires' => \Carbon\Carbon::now()->addDays(7)
    ];
}
```

It's also possible to extend an existing temporary:
Just use the `$user->extendPermission($permissionName, array $options)` method.
