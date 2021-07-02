<?php

if (! function_exists('tableActions')) {
    function tableActions($resource, $base_route)
    {
        $edit_button = "<a href='".route($base_route.'.edit', [$resource->id])."' class='btn btn-primary'>Edit</a>";
        $delete_button = "<button data-id='".$resource->id."' data-destroy_route='".route($base_route.'.destroy', [$resource->id])."' class='destroy-button btn btn-secondary'>Delete</button>";

        return "$edit_button $delete_button";
    }
}
