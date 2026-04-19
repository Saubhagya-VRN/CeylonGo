# Expanded Individual Contribution Report: Driver & Guide Modules
**Project:** CeylonGo - Integrated Tourism Platform
**Roles:** Transport Provider (Driver) & Tour Guide
**Contributor:** [Your Name]

---

## 1. Transport Provider (Driver) Module: Advanced Features

### 1.1 Robust Authentication & Secure Onboarding
- **Transactional Registration**: Implemented multi-table registration using PDO transactions (`beginTransaction`, `commit`, `rollBack`) to ensure data consistency across `transport_users`, `users`, `license`, and `vehicle` tables.
- **Short-Code ID System**: Engineered a unique ID generator that truncates UUIDs to fit `varchar(12)` database constraints (e.g., `TP65b12abc`), ensuring unique indexing for lookup optimization.
- **Path-Safe Uploads**: Configured distinct upload directories for profile, license, and vehicle images with `uniqid()` based filename obfuscation to prevent directory traversal and file name collisions.

### 1.2 Intelligent Request Management
- **Smart Assignment Engine**: 
    - **Direct Logic**: Prioritizes requests assigned specifically to the driver's ID.
    - **Pool Logic**: Automatically surfaces unassigned requests that match the driver's vehicle type and availability.
- **Asynchronous Status Updates**: Integrated AJAX endpoints for accepting/rejecting bookings, allowing the dashboard to update without full page refreshes, which reduces server load and improves mobile responsiveness.
- **Automated Communication**: Integrated native PHP `mail()` functionality to send structured trip itineraries and driver details to tourists immediately upon booking confirmation.

### 1.3 Fleet & Financial Management
- **Multi-Vehicle Scaling**: Developed logic for drivers to maintain multiple vehicles with specific passenger capacities and vehicle type IDs.
- **KPI-Driven Analytics**: 
    - **Revenue Tracking**: Aggregates fares from both `confirmed` and `completed` statuses.
    - **Distance Metrics**: Dynamically sums distance from confirmed tours to track fleet wear and fuel efficiency.

---

## 2. Tour Guide Module: Specialized Features

### 2.1 Expert Profiling System
- **Profile Granularity**: Supports multi-categorical specializations (Nature, Adventure, Culture, History) and multi-language string parsing.
- **Experience Verification**: Stores the number of years of experience and matches this during the search algorithm (optional enhancement).

### 2.2 Reassignment & Fallback Logic
- **Chain Reassignment**: If a guide rejects a request, the system executes a `findAvailableGuide` query excluding the rejecting guides (`id NOT IN (...)`) and matches based on `language LIKE %...%`.
- **Conflict Prevention**: Logical checks ensure guides cannot accept overlapping tours for the same date and time (SQL `BETWEEN` or `CONCAT` checks).

### 2.3 Comprehensive Reporting
- **Visual Analytics**: Utilizes Chart.js (or similar) to plot "Monthly Revenue Trend" and "Request Success Ratio" based on data fetched from `getFilteredReportData`.
- **Date-Range Precision**: Users can filter performance data down to the specific day using SQL `BETWEEN` queries, handling `NULL` values gracefully using `IFNULL()`.

---

## 3. Comprehensive Test Cases (Expanded)

### 3.1 Authentication & Registration Tests
| ID | Category | Test Case | Action | Expected Result |
|:---|:---|:---|:---|:---|
| **TC-SEC-01** | Security | Role-Based Access | Try to access `/transporter/dashboard` as a 'tourist'. | Redirect to login or show 'Access Denied'. |
| **TC-VAL-01** | Validation | Invalid NIC Format | Enter `12345` in NIC field. | Error message: "Invalid NIC number format." |
| **TC-VAL-02** | Validation | Image Type Check | Upload a `.txt` file for license image. | Server-side rejection or file extension validation error. |
| **TC-VAL-03** | Validation | Password Mismatch | Enter different passwords in 'Password' and 'Confirm Password'. | Error message: "Passwords do not match." |

### 3.2 Transport & Guide Request Tests
| ID | Category | Test Case | Action | Expected Result |
|:---|:---|:---|:---|:---|
| **TC-LOG-01** | Logic | Smart Reassignment | Driver A rejects booking #101. | Booking #101 status remains 'pending' but is now visible only to Driver B (with matching vehicle). |
| **TC-LOG-02** | Logic | Language Matching | Tourist requests "French" guide. | Only guides with "French" in their `languages` field see the request in their 'Pending' list. |
| **TC-BND-01** | Boundary | Passenger Capacity | Request a Van for 10 people when max is 8. | System should prevent booking or alert the tourist of capacity limits. |
| **TC-BND-02** | Boundary | Past Date Check | Attempt to accept a booking for a date that has already passed. | System should hide the 'Accept' button or show "Expired". |

### 3.3 Reporting & Data Integrity
| ID | Category | Test Case | Action | Expected Result |
|:---|:---|:---|:---|:---|
| **TC-REP-01** | Data | Revenue Calculation | Complete a tour with a 5000 LKR fare. | 'Total Revenue' KPI in the report increases by exactly 5000. |
| **TC-REP-02** | Data | Empty State | Filter report for a month with zero bookings. | Display "No data found" or "0" instead of breaking UI or showing SQL errors. |
| **TC-INT-01** | Integrity | Duplicate Assignment | Two drivers click 'Accept' at the same millisecond. | Database lock or status check ensures only the first driver gets the booking. |

---

## 4. Database Schema Details (Individual Implementation)
- **`transport_requests`**: Stores `id`, `user_id`, `customer_name`, `vehicle_type`, `pickup_location`, `dropoff_location`, `status` (ENUM), `assigned_driver_id`.
- **`guide_requests`**: Stores `id`, `tourist_id`, `location`, `language`, `fee` (Fixed 2500.00), `status`, `guide_id`.
- **`transport_vehicle`**: Links vehicles to providers via `user_id` with `vehicle_no` as primary key.
- **`guide_users`**: Extends central user identity with `license_number`, `specialization`, and `languages`.
