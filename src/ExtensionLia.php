<?php

namespace Lia\Filemanager;

use Lia\Admin;
use Lia\Auth\Database\Menu;
use Lia\Extension;

class ExtensionLia extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */

    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('filemanager', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */

            $router->group([
                'namespace'     => 'Lia\Filemanager\Controllers',
                'as'            => 'lia-filemanager.'
            ], function($router){

                $router->match(['GET', 'POST'], 'filemanager/dialog', 'DialogController@index')->name('dialog');

                $router->get('filemanager/ajax_calls', 'AjaxCallsController@index')->name('ajax_calls');
                $router->get('filemanager/upload', 'UploadController@index')->name('upload');
                $router->get('filemanager/execute', 'ExecuteController@index')->name('execute');
                $router->get('filemanager/force_download', 'ForceDownloadController@index')->name('force_download');
            });
        });
    }

    public static function import()
    {
        $lastOrder = Menu::max('order');
        Menu::create([
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'Filemanager',
            'icon'      => 'fa-folder',
            'uri'       => 'filemanager/dialog',
        ]);

        parent::createPermission('Admin filemanager', 'ext.filemanager', 'filemanager/*');
    }

}