<?php

// Demand Controller Routes

$api->post('/demand/add', 'Demand\DemandController@add');
$api->get('/demand/all/{project}/{kind}', 'Demand\DemandController@demandAll');
