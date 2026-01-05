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
$router->post('/registerProvider', 'TransportProviderController@registerProvider');

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
