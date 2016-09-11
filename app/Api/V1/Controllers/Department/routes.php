<?php

// Department Controller Routes

$api->post('/department/add', 'Department\DepartmentController@add');
$api->get('/department/one/info/{department_id}', 'Department\DepartmentController@oneInfo');
$api->post('/department/add/member', 'Department\DepartmentController@addMember');
$api->get('/department/all/info', 'Department\DepartmentController@allInfo');
$api->get('/department/member/department', 'Department\DepartmentController@memberDepartment');
$api->get('/department/member/info/{department_id}/{search}', 'Department\DepartmentController@memberInfo');
$api->get('/department/count', 'Department\DepartmentController@departmentCount');

$api->get('/department/getlist','Department\DepartmentController@getDepList');
$api->get('/department/getMemberlist/{department_id}','Department\DepartmentController@getDingMemberList');
