<?php

/*
 * Auth Controller Routes
 *
 */

$api->post('/auth/login', 'Auth\AuthController@authenticate');
$api->post('/auth/register', 'Auth\AuthController@register');
$api->post('/auth/resetPassword', 'Auth\AuthController@resetPassword');
