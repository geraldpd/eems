<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('routes')) {
    function routes($section, $parent = null, $end = 'admin')
    {
        return collect([
            'index' => $parent ? [$parent] : null,
            'create' => $parent ? [$parent] : null,
            'show' => $parent ? [$parent, 'resource_id'] : ['resource_id'],
            'edit' => $parent ? [$parent, 'resource_id'] : ['resource_id'],
            'store' => $parent ? [$parent] : null,
            'update' => $parent ? [$parent,'resource_id'] : ['resource_id'],
            'destroy' => $parent ? [$parent,'resource_id'] : ['resource_id'],
            'datatable' => $parent ? [$parent] : null, //optional
        ])
        ->map(function($params, $resource) use ($section, $end){
            return Route::has("$end.$section.$resource") ? route("$end.$section.$resource", $params) : "$resource"."_route_not_found";
        });
    }
}
