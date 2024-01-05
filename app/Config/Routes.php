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
  $routes->get('championships/settings/(:num)', 'Admin\Championships::settings/$1');
  $routes->post('championships/saveSettings', 'Admin\Championships::saveSettings');
  $routes->get('championships/teams/(:num)', 'Admin\Championships::teams/$1');
  $routes->get('championships/division/(:num)', 'Admin\Championships::division/$1');
  $routes->post('championships/saveTeamsDivisions', 'Admin\Championships::saveTeamsDivisions');

  $routes->post('members/approve', 'Admin\Members::approve');
  $routes->post('members/deleteMTD/(:num)', 'Admin\Members::deleteMTD/$1');
  $routes->get('members/update/(:num)', 'Admin\Members::update/$1');
  $routes->post('members/save', 'Admin\Members::save');

  $routes->get('teams', 'Admin\Teams::index');
  $routes->get('teams/create', 'Admin\Teams::create');
  $routes->get('teams/update/(:num)', 'Admin\Teams::update/$1');
  $routes->post('teams/save', 'Admin\Teams::save');
});
