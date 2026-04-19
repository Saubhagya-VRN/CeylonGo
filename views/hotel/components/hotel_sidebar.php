<?php
// Hotel Sidebar Component
// Usage: include(__DIR__ . '/components/hotel_sidebar.php');
// Optional parameter: $active_page = 'dashboard' (to set active menu item)
?>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<aside class="sidebar">
  <div class="brand">
  </div>
  <ul>
    <?php
      $nav_items = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => '/CeylonGo/public/hotel/dashboard', 'icon' => 'fa-solid fa-table-columns'],
        ['key' => 'availability', 'label' => 'Availability', 'url' => '/CeylonGo/public/hotel/availability', 'icon' => 'fa-regular fa-calendar-check'],
        ['key' => 'bookings', 'label' => 'Bookings', 'url' => '/CeylonGo/public/hotel/bookings', 'icon' => 'fa-regular fa-calendar'],
        ['key' => 'booking-management', 'label' => 'Booking Management', 'url' => '/CeylonGo/public/hotel/add-room', 'icon' => 'fa-solid fa-door-open'],
        ['key' => 'room-management', 'label' => 'Room Management', 'url' => '/CeylonGo/public/hotel/rooms', 'icon' => 'fa-solid fa-bed'],
        ['key' => 'payments', 'label' => 'Payments', 'url' => '/CeylonGo/public/hotel/payments', 'icon' => 'fa-solid fa-credit-card'],
        ['key' => 'reviews', 'label' => 'Reviews', 'url' => '/CeylonGo/public/hotel/reviews', 'icon' => 'fa-regular fa-star'],
        ['key' => 'inquiries', 'label' => 'Inquiries', 'url' => '/CeylonGo/public/hotel/inquiries', 'icon' => 'fa-solid fa-circle-question'],
        ['key' => 'report-issue', 'label' => 'Report Issue', 'url' => '/CeylonGo/public/hotel/report-issue', 'icon' => 'fa-solid fa-triangle-exclamation'],
        ['key' => 'notifications', 'label' => 'Notifications', 'url' => '/CeylonGo/public/hotel/notifications', 'icon' => 'fa-solid fa-bell'],
      ];

      $is_active_page = isset($active_page) ? $active_page : 'dashboard';
      
      foreach ($nav_items as $item) {
        $is_active = ($is_active_page === $item['key']) ? ' active' : '';
        echo '<li class="' . $is_active . '"><a href="' . htmlspecialchars($item['url']) . '"><i class="' . htmlspecialchars($item['icon']) . '"></i> ' . htmlspecialchars($item['label']) . '</a></li>';
      }
    ?>
  </ul>
</aside>
