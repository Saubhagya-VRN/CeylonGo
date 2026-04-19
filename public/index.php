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
$router->get('about', 'PagesController@about');
$router->get('contact', 'PagesController@contact');

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
$router->get('tourist/dashboard-side', 'TouristController@dashboardSide');
$router->get('tourist/customize-trip', 'TouristController@trip');
$router->get('tourist/booking-status', 'TouristController@bookingStatusHub');
$router->get('tourist/custom-trip-summary', 'TouristController@customTripSummary');
$router->post('tourist/trip-submit', 'TouristController@tripSubmit');
$router->post('tourist/trip-payment-checkout', 'TouristController@tripPaymentCheckout');
$router->get('tourist/trip-payment-status/{id}', 'TouristController@tripPaymentStatus');
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
$router->post('tourist/payment/checkout', 'TouristController@paymentCheckout');
$router->get('tourist/payment/return', 'TouristController@paymentReturn');
$router->post('tourist/payment/return', 'TouristController@paymentReturn');
$router->post('tourist/payment/notify', 'TouristController@paymentNotify');
$router->get('tourist/payment', 'TouristController@payment');
$router->get('tourist/trip-summary', 'TouristController@tripSummary');
$router->get('tourist/booking/trip-summary', 'TouristController@packageBookingTripSummary');
$router->get('tourist/booking/trip-summary-json', 'TouristController@packageBookingTripSummaryJson');
$router->post('tourist/booking/refund-request', 'TouristController@packageBookingRefundRequest');
$router->post('tourist/trip/refund-request', 'TouristController@customTripRefundRequest');
$router->get('tourist/packages', 'TouristController@packages');
$router->get('tourist/package-details/{id}', 'TouristController@packageDetails');
$router->get('tourist/package_details', 'TouristController@packageDetailsQuery');
$router->get('tourist/add-review', 'TouristController@addReview');
$router->post('tourist/add-review', 'TouristController@addReview');
$router->get('tourist/transport-providers', 'TouristController@transportProviders');
$router->get('tourist/transport-edit/{id}', 'TouristController@transportEdit');
$router->get('tourist/transport-delete/{id}', 'TouristController@transportDelete');
$router->post('tourist/tour-guide-submit', 'TouristController@tourGuideRequestSubmit');
$router->get('tourist/tour-guide-report', 'TouristController@tourGuideRequestReport');
$router->get('tourist/contact', 'TouristController@contact');
$router->post('tourist/hotel-request', 'TouristController@hotelRequestSubmit');
$router->post('tourist/inquiries', 'TouristController@inquirySubmit');
$router->get('tourist/profile', 'TouristController@profile');
$router->post('tourist/profile', 'TouristController@profile');

// ========== API ROUTES ==========
$router->get('api/geocode', 'GeocodeController@geocode');
$router->get('api/calculate-fare', 'GeocodeController@calculateFare');
$router->get('api/places-autocomplete', 'GeocodeController@placesAutocomplete');
$router->get('api/locations', 'GeocodeController@placesAutocomplete'); // Alias for autocomplete

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
$router->post('hotel/rooms', 'HotelController@rooms');
$router->get('hotel/add-room', 'HotelController@addRoomView');
$router->post('hotel/add-room', 'HotelController@addRoom');
$router->get('hotel/edit-room/{id}', 'HotelController@editRoomView');
$router->post('hotel/update-room', 'HotelController@updateRoom');
$router->get('hotel/delete-room/{id}', 'HotelController@deleteRoom');
$router->get('hotel/bookings', 'HotelController@bookings');
$router->get('hotel/bookings-calendar', 'HotelController@getBookingsCalendar');
$router->get('hotel/dashboard-stats', 'HotelController@getDashboardStats');
$router->get('hotel/revenue-data', 'HotelController@getRevenueData');
$router->get('hotel/availability-data', 'HotelController@getAvailabilityData');
$router->get('hotel/recent-bookings', 'HotelController@getRecentBookings');
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
$router->post('admin/payment/reject-slip',    'AdminController@rejectSlipPayment');
$router->post('admin/payment/approve-refund', 'AdminController@approveRefund');
$router->post('admin/payment/reject-refund',      'AdminController@rejectRefund');
$router->post('admin/trip-payment/approve-slip', 'AdminController@approveTripSlipPayment');
$router->post('admin/trip-payment/reject-slip',  'AdminController@rejectTripSlipPayment');
$router->post('admin/trip-payment/approve-refund', 'AdminController@approveTripRefund');
$router->post('admin/trip-payment/reject-refund',  'AdminController@rejectTripRefund');
$router->get('admin/reviews', 'AdminController@reviews');
$router->post('admin/review/delete', 'AdminController@deleteReview');
$router->post('admin/review/reply', 'AdminController@replyToReview');
$router->post('admin/review/approve', 'AdminController@approveReview');
$router->post('admin/package-review/delete', 'AdminController@deletePackageReview');
$router->post('admin/package-review/reply', 'AdminController@replyToPackageReview');
$router->post('admin/package-review/approve', 'AdminController@approvePackageReview');
$router->get('admin/inquiries', 'AdminController@inquiries');
$router->get('admin/inquiries/export', 'AdminController@exportInquiriesPdf');
$router->get('admin/reviews/export', 'AdminController@exportReviewsPdf');
$router->get('admin/reviews/export-package', 'AdminController@exportPackageReviewsPdf');
$router->post('admin/inquiry/delete', 'AdminController@deleteInquiry');
$router->post('admin/inquiry/reply',  'AdminController@replyToInquiry');
$router->get('admin/reports', 'AdminReportController@index');
$router->get('admin/reports/export-pdf', 'AdminReportController@exportPdf');
$router->get('admin/service', 'AdminController@service');
$router->post('admin/provider/status', 'AdminController@toggleProviderStatus');
$router->get('admin/settings', 'AdminController@settings');
$router->get('admin/forgot-password', 'AdminController@forgotPassword');
$router->get('admin/packages', 'AdminController@packages');
$router->get('admin/packages/export', 'AdminController@exportPackagesPdf');
$router->get('admin/packages/new', 'AdminController@packageNew');
$router->post('admin/packages/create', 'AdminController@packageCreate');
$router->get('admin/packages/edit', 'AdminController@packageEdit');
$router->post('admin/packages/update', 'AdminController@packageUpdate');
$router->post('admin/packages/delete', 'AdminController@packageDelete');

// Dispatch the request
try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Throwable $e) {
    http_response_code(500);
    $msg = $e->getMessage();
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Error: ' . $msg;
    } else {
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title></head><body>';
        echo '<h1>Something went wrong</h1>';
        echo '<p>' . htmlspecialchars($msg) . '</p>';
        if (function_exists('ini_get') && ini_get('display_errors')) {
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
        echo '</body></html>';
    }
}
?>
