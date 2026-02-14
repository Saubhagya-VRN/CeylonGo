# Transport Requests Feature - Setup Instructions

## Database Setup

A new table `transport_requests` has been created to store all transport booking information.

### To Create the Table:

1. **Option 1: Run the PHP script**
   - Open your browser and navigate to:
   ```
   http://localhost/CeylonGo/database/create_transport_requests.php
   ```

2. **Option 2: Run SQL directly**
   - Open phpMyAdmin
   - Select the `ceylon_go` database
   - Go to SQL tab
   - Copy and paste the contents of `database/create_transport_requests_table.sql`
   - Click "Go"

### Table Structure:

The `transport_requests` table includes the following fields:
- `id` - Primary key (auto-increment)
- `user_id` - Foreign key to tourist_users table
- `customer_name` - Customer's full name
- `contact_number` - Phone number
- `date` - Travel date
- `num_people` - Number of passengers
- `vehicle_type` - Type of vehicle (Tuk, Car, SUV, Minivan, Bus)
- `pickup_location` - Starting location
- `pickup_time` - Time of pickup
- `dropoff_location` - Destination
- `notes` - Optional notes/requirements
- `estimated_fare` - Calculated fare amount
- `distance` - Distance in kilometers
- `status` - Request status (pending, confirmed, cancelled, completed)
- `created_at` - Timestamp when request was created
- `updated_at` - Timestamp when request was last updated

### Features Implemented:

1. **Transport Request Form** (in tourist_dashboard.php)
   - Auto-populates customer name and contact number from user profile
   - Collects all booking information including date, time, locations, etc.
   - Calculates estimated fare based on distance and vehicle type
   - Saves all data to the database

2. **Backend Processing**
   - New controller: `controllers/tourist/save_transport_request.php`
   - Handles AJAX form submission
   - Validates data and saves to database
   - Returns success/error response with request ID

3. **View Requests Page**
   - New page: `views/tourist/my_transport_requests.php`
   - Displays all transport requests made by the logged-in tourist
   - Shows request status, details, route info, and estimated fare
   - Organized by most recent first

4. **Model Class**
   - New model: `models/TransportRequestModel.php`
   - Provides methods for CRUD operations
   - Handles database interactions for transport requests

### How It Works:

1. Tourist opens the transport request modal on the dashboard
2. Form auto-fills with their name and contact number
3. They enter travel details and calculate the fare
4. Upon confirmation, data is sent via AJAX to the backend
5. Backend validates and saves to `transport_requests` table
6. User receives confirmation with their request ID
7. They can view all their requests at `/views/tourist/my_transport_requests.php`

### Next Steps:

1. Run the table creation script
2. Test the transport request form
3. Verify data is being saved correctly
4. Optionally: Add admin panel to manage requests
5. Optionally: Add email notifications for new requests
