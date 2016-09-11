<?php

/*
 * User Controller Routes
 *
 */

$api->get('/users/all', 'User\UserController@show');
$api->get('/users/one', 'User\UserController@getAuthenticatedUser');
$api->get('/user/permission', 'User\UserController@getUserPermission');
