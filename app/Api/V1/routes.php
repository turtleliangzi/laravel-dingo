<?php

/*
 * Dingo Api Routes
 */

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers'], function($api) {
        /*
         * include controller routes
         */
        $dir = '/var/www/pms_api/app/Api/V1/Controllers/' ;
        
        //Auth Controller Routes

        include $dir.'Auth/routes.php';
        // Push Controller Routes
        include $dir.'Push/routes.php';
        include $dir.'SendMail/routes.php';
        include $dir.'Dingding/routes.php';
        $api->group(['middleware' => 'jwt.auth'], function($api) {
            $dir = '/var/www/pms_api/app/Api/V1/Controllers/';

            // User Controller Routes
            include $dir.'User/routes.php';

            // Company Controller Routes
            include $dir.'Company/routes.php';

            // Department Controller Routes
            include $dir.'Department/routes.php';

            // Role Controller Routes
            include $dir.'Role/routes.php';

            // Project Controller Routes
            include $dir.'Project/routes.php';

            // Demand Controller Routes
            include $dir.'Demand/routes.php';

            // Folder Controller Routes
            include $dir.'Folder/routes.php';

            // UserGroup Controller Routes
            include $dir.'UserGroup/routes.php';
        });    
    });
});
