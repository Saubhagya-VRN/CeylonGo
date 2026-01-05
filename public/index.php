<?php
session_start();

require_once '../config/config.php';
require_once '../core/autoload.php';
require_once '../core/helpers.php';
require_once '../core/Router.php';

$router = new Router();

// Auth Routes
$router->get('login', 'AuthController@loginView');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@registerView');
$router->get('logout', 'AuthController@logout');

// Routes
$router->get('transporter/register', 'TransportProviderController@loginView');
$router->get('transporter/dashboard', 'TransportProviderController@dashboard');
$router->get('transporter/upcoming', 'TransportProviderController@upcoming');
$router->get('transporter/pending', 'TransportProviderController@pending');
$router->get('transporter/cancelled', 'TransportProviderController@cancelled');
$router->get('transporter/review', 'TransportProviderController@review');
$router->get('transporter/profile', 'TransportProviderController@profile');
$router->post('transporter/profile', 'TransportProviderController@profile');
$router->get('transporter/info', 'TransportProviderController@info');
$router->get('transporter/pending_info', 'TransportProviderController@pendingInfo');
$router->get('transporter/vehicle', 'TransportProviderController@vehicle');
$router->post('transporter/vehicle', 'TransportProviderController@addVehicle');
$router->post('transporter/update-vehicle', 'TransportProviderController@updateVehicle');
$router->get('transporter/payment', 'TransportProviderController@payment');
$router->post('registerProvider', 'TransportProviderController@registerProvider');

// ========== ADMIN ROUTES ==========
$router->get('admin/dashboard', 'AdminController@dashboard');
$router->get('admin/profile', 'AdminController@profile');
$router->post('admin/profile', 'AdminController@updateProfile');
$router->post('admin/delete-profile', 'AdminController@deleteProfile');
$router->get('admin/users', 'AdminController@users');
$router->get('admin/bookings', 'AdminController@bookings');
$router->get('admin/payments', 'AdminController@payments');
$router->get('admin/reviews', 'AdminController@reviews');
$router->get('admin/inquiries', 'AdminController@inquiries');
$router->get('admin/promotions', 'AdminController@promotions');
$router->get('admin/reports', 'AdminController@reports');
$router->get('admin/service', 'AdminController@service');
$router->get('admin/settings', 'AdminController@settings');
$router->get('admin/forgot-password', 'AdminController@forgotPassword');

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>
