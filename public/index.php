<?php
/**
 * Web Application Entry Point
 */

// Load bootstrap
require_once '../core/bootstrap.php';

// Load router
require_once '../core/Router.php';

$router = new Router();

// Auth Routes
$router->get('login', 'AuthController@loginView');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@registerView');
$router->get('logout', 'AuthController@logout');

// Routes
$router->get('transporter/register', 'TransportProviderController@registerView');
$router->post('transporter/register', 'TransportProviderController@registerProvider');
$router->get('transporter/dashboard', 'TransportProviderController@dashboard');
$router->get('transporter/upcoming', 'TransportProviderController@upcoming');
$router->get('transporter/pending', 'TransportProviderController@pending');
$router->get('transporter/cancelled', 'TransportProviderController@cancelled');
$router->get('transporter/review', 'TransportProviderController@review');
$router->get('transporter/profile', 'TransportProviderController@profile');
$router->post('transporter/profile', 'TransportProviderController@profile');
$router->get('transporter/info', 'TransportProviderController@info');
$router->get('transporter/pending_info', 'TransportProviderController@pendingInfo');
$router->get('transporter/cancelled_info', 'TransportProviderController@cancelledInfo');
$router->get('transporter/vehicle', 'TransportProviderController@vehicle');
$router->post('transporter/vehicle', 'TransportProviderController@addVehicle');
$router->post('transporter/update-vehicle', 'TransportProviderController@updateVehicle');
$router->get('transporter/payment', 'TransportProviderController@payment');
$router->post('transporter/payment', 'TransportProviderController@saveBankDetails');
$router->post('registerProvider', 'TransportProviderController@registerProvider');

// ========== ADMIN ROUTES ==========
$router->get('admin/dashboard', 'AdminController@dashboard');
$router->get('admin/profile', 'AdminController@profile');
$router->post('admin/profile', 'AdminController@updateProfile');
$router->post('admin/delete-profile', 'AdminController@deleteProfile');
$router->get('admin/users', 'AdminController@users');
$router->get('admin/bookings', 'AdminController@bookings');
$router->post('admin/approve-booking', 'AdminController@approveBooking');
$router->get('admin/payments', 'AdminController@payments');
$router->get('admin/reviews', 'AdminController@reviews');
$router->get('admin/inquiries', 'AdminController@inquiries');
$router->get('admin/promotions', 'AdminController@promotions');
$router->get('admin/reports', 'AdminController@reports');
$router->get('admin/service', 'AdminController@service');
$router->get('admin/settings', 'AdminController@settings');
$router->get('admin/forgot-password', 'AdminController@forgotPassword');

// ========== TOURIST ROUTES ==========
$router->get('tourist/register', 'TouristController@registerView');
$router->post('tourist/register', 'TouristController@register');
$router->get('tourist/dashboard', 'TouristController@dashboardNew');
$router->get('tourist/old-dashboard', 'TouristController@oldDashboard');
$router->get('tourist/customize-trip', 'TouristController@trip');
$router->get('tourist/transport-services', 'TouristController@transportRequestView');
$router->post('tourist/transport-services', 'TouristController@transportRequest');
$router->get('tourist/transport-report', 'TouristController@transportReport');
$router->get('tourist/tour-guides', 'TouristController@tourGuides');
$router->get('tourist/choose-hotel', 'TouristController@chooseHotel');
$router->get('tourist/hotel-details/{id}', 'TouristController@hotelDetails');
$router->get('tourist/booking-form', 'TouristController@bookingForm');
$router->post('tourist/booking-form', 'TouristController@bookingFormSubmit');
$router->get('tourist/my-bookings', 'TouristController@myBookings');
$router->get('tourist/booking-approve', 'TouristController@bookingApprove');
$router->get('tourist/payment', 'TouristController@payment');
$router->get('tourist/trip-summary', 'TouristController@tripSummary');
$router->get('tourist/recommended-packages', 'TouristController@recommendedPackages');
$router->get('tourist/packages', 'TouristController@packages');
$router->get('tourist/package-details/{id}', 'TouristController@packageDetails');
$router->get('tourist/package_details', 'TouristController@packageDetailsQuery');
$router->get('tourist/add-review', 'TouristController@addReview');
$router->get('tourist/transport-providers', 'TouristController@transportProviders');
$router->get('tourist/transport-edit/{id}', 'TouristController@transportEdit');
$router->get('tourist/transport-delete/{id}', 'TouristController@transportDelete');
$router->post('tourist/tour-guide-submit', 'TouristController@tourGuideRequestSubmit');
$router->get('tourist/tour-guide-report', 'TouristController@tourGuideRequestReport');
$router->get('tourist/contact', 'TouristController@contact');
$router->get('tourist/public-diaries', 'TouristController@publicDiaries');

// ========== API ROUTES ==========
$router->get('api/geocode', 'GeocodeController@geocode');
$router->get('api/calculate-fare', 'GeocodeController@calculateFare');
$router->get('api/places-autocomplete', 'GeocodeController@placesAutocomplete');

// ========== GUIDE ROUTES ==========
$router->get('guide/register', 'GuideController@registerView');
$router->post('guide/register', 'GuideController@register');
$router->get('guide/dashboard', 'GuideController@dashboard');
$router->get('guide/upcoming', 'GuideController@upcoming');
$router->get('guide/pending', 'GuideController@pending');
$router->get('guide/cancelled', 'GuideController@cancelled');
$router->get('guide/review', 'GuideController@review');
$router->get('guide/profile', 'GuideController@profile');
$router->post('guide/profile', 'GuideController@profile');
$router->get('guide/places', 'GuideController@places');
$router->get('guide/info', 'GuideController@info');
$router->get('guide/pending_info', 'GuideController@pendingInfo');
$router->get('guide/cancelled_info', 'GuideController@cancelledInfo');
$router->get('guide/payment', 'GuideController@payment');
$router->post('guide/payment', 'GuideController@payment');

// ========== HOTEL ROUTES ==========
$router->get('hotel/register', 'HotelController@registerView');
$router->post('hotel/register', 'HotelController@register');
$router->get('hotel/dashboard', 'HotelController@dashboard');
$router->get('hotel/rooms', 'HotelController@rooms');
$router->get('hotel/add-room', 'HotelController@addRoomView');
$router->post('hotel/add-room', 'HotelController@addRoom');
$router->get('hotel/edit-room/{id}', 'HotelController@editRoomView');
$router->post('hotel/update-room', 'HotelController@updateRoom');
$router->get('hotel/delete-room/{id}', 'HotelController@deleteRoom');
$router->get('hotel/bookings', 'HotelController@bookings');
$router->get('hotel/availability', 'HotelController@availability');
$router->get('hotel/inquiries', 'HotelController@inquiries');
$router->get('hotel/notifications', 'HotelController@notifications');
$router->get('hotel/payments', 'HotelController@payments');
$router->get('hotel/reviews', 'HotelController@reviews');
$router->get('hotel/report-issue', 'HotelController@reportIssue');

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>
