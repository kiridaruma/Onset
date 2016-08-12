<?php
$app->get('/',      '\App\Onset\Controller\OnsetController:index');
$app->get('/help',  '\App\Onset\Controller\OnsetController:help');
$app->get('/onset[/]',  '\App\Onset\Controller\OnsetController:chat');
$app->get('/status[/]', '\App\Onset\Controller\OnsetController:status');

// Room
$app->group('/room', function (){
    $this->get('/autoremove',   '\App\Onset\Controller\RoomController:autoremove');
    $this->post('/create',      '\App\Onset\Controller\RoomController:create');
    $this->post('/remove',      '\App\Onset\Controller\RoomController:remove');
    $this->post('/enter',       '\App\Onset\Controller\RoomController:enter');
    $this->post('/read',        '\App\Onset\Controller\RoomController:read');
    $this->post('/write',       '\App\Onset\Controller\RoomController:write');
    $this->get('/logout',       '\App\Onset\Controller\RoomController:logout');
    $this->get('/logput',       '\App\Onset\Controller\RoomController:logput');
    $this->post('/users',       '\App\Onset\Controller\RoomController:users');
});
