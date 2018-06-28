<?php

if (!function_exists('lia_fm_vendor')) {

    /**
     * @param $path
     *
     * @return string
     */
    function lia_fm_vendor($path)
    {
        return asset('vendor/lia-filemanager/'.trim($path, '/'), config('lia.secure'));
    }
}