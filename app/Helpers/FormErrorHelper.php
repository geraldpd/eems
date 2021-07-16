<?php

if (! function_exists('hasError')) {
    function hasError($errors, $input, $message = null)
    {
        $error = $message ?? $errors->first($input);

        if($errors->has($input)) {
            return "<small class='help-block text-danger'><strong>". $error ."</strong></small>";
        }
    }
}