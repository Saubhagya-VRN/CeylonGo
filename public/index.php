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
$router->get('transporter/report', 'TransportProviderController@report');
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
$router->post('transporter/accept-booking', 'TransportProviderController@acceptBooking');
$router->post('transporter/reject-booking', 'TransportProviderController@rejectBooking');
$router->get('tourist/transport-status', 'TransportProviderController@checkBookingStatus');

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
$router->get('guide/report', 'GuideController@report');
$router->post('guide/profile', 'GuideController@profile');
$router->get('guide/places', 'GuideController@places');
$router->get('guide/info', 'GuideController@info');
$router->get('guide/pending_info', 'GuideController@pendingInfo');
$router->get('guide/cancelled_info', 'GuideController@cancelledInfo');
$router->get('guide/payment', 'GuideController@payment');
$router->post('guide/payment', 'GuideController@payment');
$router->post('guide/accept-booking', 'GuideController@acceptBooking');
$router->post('guide/reject-booking', 'GuideController@rejectBooking');

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

// ========== ADMIN ROUTES ==========
$router->get('admin/dashboard', 'AdminController@dashboard');
$router->get('admin/profile', 'AdminController@profile');
$router->post('admin/profile', 'AdminController@updateProfile');
$router->post('admin/delete-profile', 'AdminController@deleteProfile');
$router->get('admin/users', 'AdminController@users');
$router->post('admin/users', 'AdminController@users');
$router->post('admin/user/status', 'AdminController@toggleUserStatus');
$router->get('admin/bookings', 'AdminController@bookings');
$router->get('admin/booking-details', 'AdminController@getBookingDetails');
$router->post('admin/flag-booking', 'AdminController@flagBooking');
$router->post('admin/package-booking/status', 'AdminController@updatePackageBookingStatus');
$router->get('admin/payments', 'AdminController@payments');
$router->post('admin/payment/verify', 'AdminController@verifyPayment');
$router->post('admin/payment/approve-slip',   'AdminController@approveSlipPayment');
$router->post('admin/payment/approve-refund', 'AdminController@approveRefund');
$router->get('admin/reviews', 'AdminController@reviews');
$router->post('admin/review/delete', 'AdminController@deleteReview');
$router->post('admin/review/reply', 'AdminController@replyToReview');
$router->post('admin/review/approve', 'AdminController@approveReview');
$router->get('admin/inquiries', 'AdminController@inquiries');
$router->post('admin/inquiry/delete', 'AdminController@deleteInquiry');
$router->post('admin/inquiry/reply',  'AdminController@replyToInquiry');
$router->get('admin/reports', 'AdminController@reports');
$router->get('admin/service', 'AdminController@service');
$router->post('admin/provider/status', 'AdminController@toggleProviderStatus');
$router->get('admin/settings', 'AdminController@settings');
$router->get('admin/forgot-password', 'AdminController@forgotPassword');
$router->get('admin/packages', 'AdminController@packages');
$router->get('admin/packages/new', 'AdminController@packageNew');
$router->post('admin/packages/create', 'AdminController@packageCreate');
$router->get('admin/packages/edit', 'AdminController@packageEdit');
$router->post('admin/packages/update', 'AdminController@packageUpdate');
$router->post('admin/packages/delete', 'AdminController@packageDelete');

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>
