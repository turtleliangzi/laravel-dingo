<?php

// Dingding Controller Routes

$api->get('/dingding/access_token', 'Dingding\ContactsController@getAccessToken');
$api->get('/dingding/department', 'Dingding\ContactsController@getDepartment');
$api->get('/dingding/departmentInfo', 'Dingding\ContactsController@getDepartmentInfo');
$api->get('/dingding/departmentMember', 'Dingding\ContactsController@getDepartmentMember');
$api->get('/dingding/memberInfo', 'Dingding\ContactsController@getMemberInfo');
$api->get('/dingding/sendMessage', 'Dingding\ContactsController@sendMessage');
