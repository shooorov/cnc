<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class RolePermission
{
    public static function options()
    {
        $user = Auth::guard('web')->user();
        $options = [];

        if ($user?->is_admin || $user?->status == 'active') {
            $permissions = $user->role ? $user->role->permissions : [];

            foreach (config('permission') as $name => $items) {
                foreach ($items as $item => $route) {
                    if (
                        $user->is_admin
                        || (isset($permissions[$name])
                            && isset($permissions[$name][$item])
                            && $permissions[$name][$item]['is_checked']
                        )
                    ) {
                        $options["$name.$item"] = $route;
                    }
                }
            }
        }

        return $options;
    }

    public static function routes()
    {
        $valid_routes = [];
        foreach (self::options() as $route) {
            if (is_array($route)) {
                $routes = array_values($route);
                $valid_routes = array_merge($valid_routes, $routes);
            } elseif (is_string($route)) {
                $valid_routes = array_merge($valid_routes, [$route]);
            }
        }

        $valid_routes = array_merge($valid_routes, [
            'dashboard',
            'home',
            'profile.update',
            'profile.password',
            'profile.password.update',
            'use',
        ]);

        return $valid_routes;
    }

    public static function isRouteValid($route_ref = null)
    {
        if (empty($route_ref)) {
            $route_ref = request()->route()->getName();
        }

        $valid_routes = self::routes();

        return in_array($route_ref, $valid_routes);
    }

    public static function isEnabled($key)
    {
        $values = array_keys(self::options());

        return in_array($key, $values);
    }
}
