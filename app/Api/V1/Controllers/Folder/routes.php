<?php

// Foler Controller Routes

$api->post('/folder/add', 'Folder\FolderController@add');
$api->post('/folder/upload/file', 'Folder\FolderController@uploadFile');
$api->get('/folder/get/top/{project_id}/{parent}', 'Folder\FolderController@getFolder');
$api->get('/folder/get/parent/{project_id}/{parent}', 'Folder\FolderController@getFolderParent');
