<?php

// Push Controller Routes

$api->get('/push/task/undone', 'Push\PushController@getUndoneTask');
$api->get('/push/task/remind', 'Push\PushController@taskRemind');
