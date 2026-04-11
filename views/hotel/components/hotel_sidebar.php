<?php
// Hotel Sidebar Component
// Usage: include(__DIR__ . '/../components/hotel_sidebar.php');
// Optional parameter: $active_page = 'dashboard' (to set active menu item)
?>

<aside class="sidebar">
  <div class="brand">
    <div class="brand-text">Ceylon Go</div>
  </div>
  <nav class="nav">
    <?php
    $nav_items = [
      ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => '/CeylonGo/public/hotel/dashboard'],
      ['key' => 'availability', 'label' => 'Availability', 'url' => '/CeylonGo/public/hotel/availability'],
      ['key' => 'bookings', 'label' => 'Bookings', 'url' => '/CeylonGo/public/hotel/bookings'],
      ['key' => 'booking-management', 'label' => 'Booking Management', 'url' => '/CeylonGo/public/hotel/add-room'],
      ['key' => 'payments', 'label' => 'Payments', 'url' => '/CeylonGo/public/hotel/payments'],
      ['key' => 'reviews', 'label' => 'Reviews', 'url' => '/CeylonGo/public/hotel/reviews'],
      ['key' => 'inquiries', 'label' => 'Inquiries', 'url' => '/CeylonGo/public/hotel/inquiries'],
      ['key' => 'report-issue', 'label' => 'Report Issue', 'url' => '/CeylonGo/public/hotel/report-issue'],
      ['key' => 'notifications', 'label' => 'Notifications', 'url' => '/CeylonGo/public/hotel/notifications'],
    ];

    $is_active_page = isset($active_page) ? $active_page : 'dashboard';
    
    foreach ($nav_items as $item) {
      $is_active = ($is_active_page === $item['key']) ? ' active' : '';
      echo '<a class="nav-link' . $is_active . '" href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a>';
    }
    ?>
  </nav>
</aside>
