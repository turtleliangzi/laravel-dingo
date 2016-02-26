<?php

/*
 * Dingo Api Routes
 */



$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers'], function($api) {
        /*
         * include controller routes
         */

        $dir = '/var/www/dingo/app/Api/V1/Controllers/';

        // Auth Controller Rotues
        require_once $dir.'Auth/routes.php';
        $api->group(['middleware' => 'jwt.auth'], function($api) {
            $dir = '/var/www/dingo/app/Api/V1/Controllers/';
            // User Controller Routes
            require_once $dir.'User/routes.php';

            //Lesson Controller Routes
            require_once $dir.'Lesson/routes.php';

        });
    });
});

