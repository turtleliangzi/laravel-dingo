<?php

// Lesson Controller Routes

$api->get('/lessons/all', 'Lesson\LessonController@show');
$api->get('/lessons/one/{id}', 'Lesson\LessonController@one'); 
