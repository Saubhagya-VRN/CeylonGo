# Package Bookings System - Setup Instructions

## Overview
This system allows customers to submit package booking requests that are stored in the database and can be approved/rejected by admin.

## Database Setup

### Step 1: Create the Database Table

**Option 1: Run the PHP script (Recommended)**
1. Open your browser and navigate to:
   ```
   http://localhost/CeylonGo/database/create_package_bookings.php
   ```

**Option 2: Run SQL directly**
1. Open phpMyAdmin
2. Select your database (e.g., `ceylon_go`)
3. Go to SQL tab
4. Copy and paste the contents of `database/create_package_bookings_table.sql`
5. Click "Go"

### Step 2: Verify Table Creation
The table `package_bookings` should be created with the following structure:
- `id` - Primary key
- `user_id` - Tourist user ID
- `package_id` - Package ID
- `package_name` - Package name
- `travelers`, `adults`, `children`, `infants` - Traveler counts
- `travel_date` - Preferred travel date
- `fullname`, `email`, `phone` - Customer contact info
- `special_requests` - Optional notes
- `total_amount` - Booking total
- `status` - pending/approved/rejected/cancelled
- `admin_notes` - Admin comments
- `approved_at`, `approved_by` - Approval tracking
- `created_at`, `updated_at` - Timestamps

## How It Works

### For Customers (Tourists):
1. Customer submits booking form → Saved to `package_bookings` table with status='pending'
2. Customer sees booking in "My Bookings" page with "Pending" status
3. After admin approval, status changes to 'approved'
4. Customer can then proceed to payment

### For Admin:
1. Go to `/CeylonGo/public/admin/bookings`
2. View all booking requests with statistics
3. Click ✓ to approve or ✕ to reject pending bookings
4. View booking details by clicking the eye icon

## Files Modified/Created:

1. **Database:**
   - `database/create_package_bookings_table.sql` - Table structure
   - `database/create_package_bookings.php` - Setup script

2. **Controllers:**
   - `controllers/TouristController.php` - Updated `bookingFormSubmit()` to save to database
   - `controllers/TouristController.php` - Updated `myBookings()` to read from database
   - `controllers/TouristController.php` - Updated `payment()` to read from database
   - `controllers/AdminController.php` - Added `bookings()` method to fetch bookings
   - `controllers/AdminController.php` - Added `approveBooking()` method for approval/rejection

3. **Views:**
   - `views/admin/admin_bookings.php` - Updated to show real booking data with approve/reject buttons

4. **Routes:**
   - `public/index.php` - Added route for `admin/approve-booking`

## Testing:

1. **Submit a booking:**
   - Login as tourist
   - Go to packages page
   - Click "Book Now" on any package
   - Fill form and submit
   - Should redirect to "My Bookings" showing pending status

2. **Admin approval:**
   - Login as admin
   - Go to `/CeylonGo/public/admin/bookings`
   - See the pending booking
   - Click ✓ to approve or ✕ to reject
   - Booking status should update

3. **Customer view:**
   - After admin approval, customer's "My Bookings" should show "Approved" status
   - Customer can click "Proceed to Payment"

## Notes:
- All bookings are now stored in the database (no longer in session)
- Admin can see all bookings from all customers
- Customers only see their own bookings
- Booking status can be: pending, approved, rejected, or cancelled



