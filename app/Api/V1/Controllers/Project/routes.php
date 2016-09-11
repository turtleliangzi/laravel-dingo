<?php

// Project Controller Routes

$api->post('/project/add', 'Project\ProjectController@add');
$api->get('/project/get/{kind}', 'Project\ProjectController@unaudited');
$api->post('/project/audit', 'Project\ProjectController@audit');
$api->post('/project/distribute', 'Project\ProjectController@distribute');
$api->get('/project/myproject', 'Project\ProjectController@getMyProject');
$api->post('/project/add_goal', 'Project\ProjectController@addGoal');
$api->post('/project/add_task', 'Project\ProjectController@addTask');
$api->get('/project/get/one/{project_id}', 'Project\ProjectController@getOne');
$api->get('/project/get/goal/all/{project_id}', 'Project\ProjectController@getGoalAll');
$api->post('/project/add_member', 'Project\ProjectController@addMember');
$api->get('/project/get_member/{project_id}', 'Project\ProjectController@getMember');
$api->get('/project/get_task/{project_id}/{goal_id}/{status}', 'Project\ProjectController@getTask');
$api->get('/project/get/task/one/{goal_id}/{task_id}', 'Project\ProjectController@getTaskOne');
$api->post('/project/receive/task', 'Project\ProjectController@receiveTask');
$api->post('/project/complete/task', 'Project\ProjectController@completeTask');
$api->post('/project/deal', 'Project\ProjectController@dealTask');
$api->get('/project/get_goal/one/{project_id}/{goal_id}', 'Project\ProjectController@getOneGoal');
$api->post('/project/deal/goal', 'Project\ProjectController@dealGoal');
$api->get('/project/all', 'Project\ProjectController@getAllProject');
$api->get('/project/project_manager/{project_id}', 'Project\ProjectController@isProjectManager');
$api->get('/project/getAllTaskInfo/{task_status}','Project\ProjectController@getAllTaskInfo');
$api->get('/project/checkStatus/{status}', 'Project\ProjectController@checkStatus');
$api->get('/project/task/doing', 'Project\ProjectController@getDoingTask');
