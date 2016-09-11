<?php

// Company Controller Routes

$api->post('/company/add', 'Company\CompanyController@add');
$api->post('/company/join', 'Company\CompanyController@join');
$api->get('/company/info', 'Company\CompanyController@info');
$api->get('/company/member/count', 'Company\CompanyController@memberCount');
$api->get('/company/member/info/{search}', 'Company\CompanyController@memberInfo');
