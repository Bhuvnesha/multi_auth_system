<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// --------------------------------------------------------------------
// Router Setup
// --------------------------------------------------------------------
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// --------------------------------------------------------------------
// Public Routes
// --------------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::loginForm');
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Auth::registerForm');
$routes->post('/register', 'Auth::register');
$routes->post('/logout', 'Auth::logout');
$routes->get('/forgot-password', 'Auth::forgotPasswordForm');
$routes->post('/forgot-password', 'Auth::forgotPassword');
$routes->get('/reset-password/(:segment)', 'Auth::resetPasswordForm/$1');
$routes->post('/reset-password', 'Auth::resetPassword');

// --------------------------------------------------------------------
// Protected Routes (Require Authentication)
// --------------------------------------------------------------------
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');

    // User Management Routes
    $routes->get('/users', 'UserManagement::index');
    $routes->get('/users/create', 'UserManagement::create');
    $routes->post('/users/store', 'UserManagement::store');
    $routes->get('/users/edit/(:num)', 'UserManagement::edit/$1');
    $routes->post('/users/update/(:num)', 'UserManagement::update/$1');
    $routes->post('/users/delete/(:num)', 'UserManagement::delete/$1');
    $routes->post('/users/toggle-status/(:num)', 'UserManagement::toggleStatus/$1');

    // Role Management Routes
    $routes->get('/roles', 'RoleManagement::index');
    $routes->get('/roles/create', 'RoleManagement::create');
    $routes->post('/roles/store', 'RoleManagement::store');
    $routes->get('/roles/edit/(:num)', 'RoleManagement::edit/$1');
    $routes->post('/roles/update/(:num)', 'RoleManagement::update/$1');
    $routes->post('/roles/delete/(:num)', 'RoleManagement::delete/$1');

    // Permission Management Routes
    $routes->get('/permissions', 'PermissionManagement::index');
    $routes->get('/permissions/create', 'PermissionManagement::create');
    $routes->post('/permissions/store', 'PermissionManagement::store');
    $routes->get('/permissions/edit/(:num)', 'PermissionManagement::edit/$1');
    $routes->post('/permissions/update/(:num)', 'PermissionManagement::update/$1');
    $routes->post('/permissions/delete/(:num)', 'PermissionManagement::delete/$1');

    // Profile Routes
    $routes->get('/profile', 'Profile::index');
    $routes->post('/profile/update', 'Profile::update');
    $routes->get('/profile/change-password', 'Profile::changePasswordForm');
    $routes->post('/profile/change-password', 'Profile::changePassword');
});

// --------------------------------------------------------------------
// Admin-only Routes
// --------------------------------------------------------------------
$routes->group('', ['filter' => 'rbac:manage_users'], function($routes) {
    // Routes that require admin privileges
    $routes->get('/admin/dashboard', 'Admin::dashboard');
    $routes->get('/admin/settings', 'Admin::settings');
    $routes->post('/admin/settings/update', 'Admin::updateSettings');
});
