<?php

// Mail Controller Routes

$api->get('/mail/send', 'SendMail\SendMailController@sendMail');
