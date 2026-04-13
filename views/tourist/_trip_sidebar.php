<?php
/**
 * Tourist app sidebar (Dashboard, Customise trip, Bookings, etc.).
 * Expects: $asset_base, $user_name, $user_email_sidebar, $avatar_initial, $trip_sidebar_active (optional key).
 */
if (!isset($asset_base) || $asset_base === '') {
    $asset_base = '/CeylonGo/public';
}
$sa = isset($trip_sidebar_active) ? (string) $trip_sidebar_active : '';
$b = function ($path) use ($asset_base) {
    return htmlspecialchars($asset_base . $path, ENT_QUOTES, 'UTF-8');
};
?>
    <aside class="trip-sidebar" id="tripSidebar">
      <div class="trip-sidebar-nav">
        <ul>
          <li<?php echo $sa === 'dashboard' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/dashboard-side'); ?>"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard <span class="sidebar-sub">Overview & Stats</span></span></a></li>
          <li<?php echo $sa === 'customize' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/customize-trip'); ?>"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
          <li id="tripSidebarNavStatusBookings"<?php echo $sa === 'status' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/booking-status'); ?>"><i class="fa-solid fa-clipboard-list"></i> <span class="sidebar-link-text">Status of Bookings <span class="sidebar-sub">Trip review &amp; submit</span></span></a></li>
          <li<?php echo $sa === 'budget' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/customize-trip?step=10'); ?>"><i class="fa-solid fa-wallet"></i> <span class="sidebar-link-text">Budget Overview <span class="sidebar-sub">Costs &amp; itinerary</span></span></a></li>
          <li<?php echo $sa === 'overview' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/customize-trip?step=14'); ?>"><i class="fa-solid fa-clipboard-check"></i> <span class="sidebar-link-text">Trip Overview <span class="sidebar-sub">Final confirmation</span></span></a></li>
          <li<?php echo $sa === 'bookings' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/my-bookings?view=custom'); ?>"><i class="fa-regular fa-calendar-check"></i> <span class="sidebar-link-text">Bookings <span class="sidebar-sub">Customised trips</span></span></a></li>
          <li<?php echo $sa === 'payment' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/payment'); ?>"><i class="fa-solid fa-credit-card"></i> <span class="sidebar-link-text">Payments <span class="sidebar-sub">Invoices & Wallet</span></span></a></li>
          <li<?php echo $sa === 'profile' ? ' class="active"' : ''; ?>><a href="<?php echo $b('/tourist/profile'); ?>"><i class="fa-regular fa-user"></i> <span class="sidebar-link-text">Profile <span class="sidebar-sub">Account Settings</span></span></a></li>
        </ul>
      </div>
      <div class="trip-sidebar-footer">
        <div class="trip-sidebar-user">
          <div class="trip-sidebar-user-avatar"><?php echo htmlspecialchars($avatar_initial ?? 'T', ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="trip-sidebar-user-info">
            <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ?? 'Tourist', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="trip-sidebar-user-email"><?php
              $em = isset($user_email_sidebar) ? (string) $user_email_sidebar : '';
              echo htmlspecialchars($em !== '' ? (substr($em, 0, 20) . (strlen($em) > 20 ? '...' : '')) : '', ENT_QUOTES, 'UTF-8');
            ?></div>
          </div>
        </div>
        <a href="<?php echo $b('/logout'); ?>" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </aside>
