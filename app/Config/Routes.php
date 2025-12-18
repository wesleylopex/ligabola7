<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->addRedirect('/', 'manager/home');
$routes->get('/migrate', 'Migrate::index');

$routes->get('/manager/login', 'Manager\Login::index', ['filter' => 'already-auth']);
$routes->post('/manager/login', 'Manager\Login::attempt', ['filter' => 'already-auth']);

$routes->get('/manager/forgot-password', 'Manager\ForgotPassword::index', ['filter' => 'already-auth']);
$routes->post('/manager/forgot-password/handle', 'Manager\ForgotPassword::handle', ['filter' => 'already-auth']);
$routes->get('/manager/forgot-password/reset', 'Manager\ForgotPassword::reset', ['filter' => 'already-auth']);
$routes->post('/manager/forgot-password/save', 'Manager\ForgotPassword::save', ['filter' => 'already-auth']);

$routes->group('manager', ['filter' => 'auth'], static function ($routes) {
  $routes->get('login/logout', 'Manager\Login::logout');

  $routes->get('/', 'Manager\Home::index');
  $routes->get('home', 'Manager\Home::index');

  $routes->get('members/create', 'Manager\Members::create');
  $routes->get('members/update/(:num)', 'Manager\Members::update/$1');
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
  $routes->post('championships/save-settings', 'Admin\Championships::saveSettings');
  $routes->get('championships/teams/(:num)', 'Admin\Championships::teams/$1');
  $routes->get('championships/division/(:num)', 'Admin\Championships::division/$1');
  $routes->post('championships/save-teams-divisions', 'Admin\Championships::saveTeamsDivisions');
  $routes->get('championships/division/(:num)/settings', 'Admin\Championships::divisionSettings/$1');
  $routes->post('championships/save-divisions-settings', 'Admin\Championships::saveDivisionsSettings');

  $routes->post('members-teams-divisions/approve', 'Admin\MembersTeamsDivisions::approve');
  $routes->post('members-teams-divisions/delete/(:num)', 'Admin\MembersTeamsDivisions::delete/$1');
  $routes->get('members-teams-divisions/update/(:num)', 'Admin\MembersTeamsDivisions::update/$1');
  $routes->post('members-teams-divisions/save', 'Admin\MembersTeamsDivisions::save');

  $routes->get('members', 'Admin\Members::index');
  $routes->get('members/create', 'Admin\Members::create');
  $routes->get('members/update/(:num)', 'Admin\Members::update/$1');
  $routes->post('members/delete/(:num)', 'Admin\Members::delete/$1');
  $routes->post('members/save', 'Admin\Members::save');

  $routes->get('teams', 'Admin\Teams::index');
  $routes->get('teams/create', 'Admin\Teams::create');
  $routes->get('teams/update/(:num)', 'Admin\Teams::update/$1');
  $routes->post('teams/delete/(:num)', 'Admin\Teams::delete/$1');
  $routes->post('teams/save', 'Admin\Teams::save');
});
