<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->addRedirect('/', 'manager/home');

$routes->get('/manager/login', 'Manager\Login::index', ['filter' => 'already-auth']);
$routes->post('/manager/login', 'Manager\Login::attempt', ['filter' => 'already-auth']);

$routes->group('manager', ['filter' => 'auth'], static function ($routes) {
  $routes->get('login/logout', 'Manager\Login::logout');

  $routes->get('/', 'Manager\Home::index');
  $routes->get('home', 'Manager\Home::index');

  $routes->get('members/create', 'Manager\Members::create');
  $routes->post('members/save', 'Manager\Members::save');
});
