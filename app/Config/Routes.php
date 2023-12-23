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
  $routes->get('members/find', 'Manager\Members::find');
});

$routes->get('/admin/login', 'Admin\Login::index', ['filter' => 'admin-already-auth']);
$routes->post('/admin/login/attempt', 'Admin\Login::attempt', ['filter' => 'admin-already-auth']);

$routes->group('admin', ['filter' => 'admin-auth'], static function ($routes) {
  $routes->get('login/logout', 'Admin\Login::logout');

  $routes->get('/', 'Admin\Home::index');
  $routes->get('home', 'Admin\Home::index');

  $routes->get('championships/create', 'Admin\Championships::create');
  $routes->post('championships/save', 'Admin\Championships::save');
  
  $routes->get('championships/(:num)', 'Admin\Championships::divisions/$1');
  $routes->get('championships/division/(:num)', 'Admin\Championships::division/$1');
});
