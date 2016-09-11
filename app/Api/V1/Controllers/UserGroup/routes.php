<?php

// UserGroup Controller Routes

$api->post('/group/add', 'UserGroup\UserGroupController@add');
$api->get('/group/all', 'UserGroup\UserGroupController@getAllGroup');
$api->get('/group/member', 'UserGroup\UserGroupController@getMemberGroup');
$api->get('/user/group/info/{uid}', 'UserGroup\UserGroupController@getUserGroup');
$api->post('/group/change_group', 'UserGroup\UserGroupController@changeUserGroup');
$api->get('/group/one/{group_id}', 'UserGroup\UserGroupController@getOneGroup');
