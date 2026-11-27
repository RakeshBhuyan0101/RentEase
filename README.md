# RentEase – Property Rental & Management Platform

A web application built with **PHP**, **MySQL**, **Bootstrap 5.3.3**, and **jQuery AJAX** supporting three distinct user roles: **Owner**, **Tenant**, and **Admin**.

---

## 🚀 Quick Setup & Access

1. **Start XAMPP**: Ensure **Apache** (Port `8080`) and **MySQL** (Port `3306`) are running.
2. **Database**: Import `rentease_db.sql` into phpMyAdmin (`http://localhost:8080/phpmyadmin/` → Database: `rentease_db`).
3. **Application URL**: Open **`http://localhost:8080/RentEase/pages/signin.php`** in your browser.

---

## 🔑 Recommended Test Credentials

Use these preferred accounts to test all features:

| Role | Preferred Account | Password | What You Can Test |
|---|---|---|---|
| **Owner** *(Primary)* | `rakeshbhuyan0101@gmail.com` | `Munna@1234` | Manage 5 properties, live search/filters, add/edit modal with image upload, approve/reject tenant bookings. |
| **Owner** *(Alternative)* | `owner1@rentease.in` | `owner123` | View separate listings and booking inquiries. |
| **Admin** | `admin@rentease.in` | `admin123` | Approve/reject pending owner properties, block/unblock users, inspect platform analytics. |
| **Tenant** *(Primary)* | `ananya.deshmukh@gmail.com` | `tenant123` | Explore approved listings, filter by city/BHK/budget, submit booking requests, track booking statuses. |
| **Tenant** *(Alternative)* | `tenant1@rentease.in` | `tenant123` | Has pre-approved booking with owner contact info unlocked. |

> *All passwords in the database are hashed with standard Bcrypt (`password_hash`, `PASSWORD_DEFAULT`).*

---

## 🧪 What & How to Test (Role-by-Role)

### 1. 🏢 Owner Dashboard (`/pages/owner/dashboard.php`)
* **Test Live Search & Filter Pills**:
  * Type in the search box (e.g. `Villa`, `Whitefield`, `Patia`) to filter table rows in real time.
  * Click the filter pills (**All**, **Approved**, **Pending**, **Rejected**) to view listings by status.
* **Test Adding a Property**:
  * Click **+ Add Property** to open the modal.
  * Fill in Title, Description, City, Locality, Full Address, BHK, Furnishing, Rent, Deposit, Amenities.
  * Upload a cover image (`.jpg`, `.png`, `.webp`, $\le$ 2MB).
  * Click **Save Property** $\rightarrow$ It appears in your table with a `PENDING` status badge.
* **Test Editing a Property**:
  * Click **Edit** on any property row.
  * Notice the form pre-fills with existing data, shows the current image name, and toggles to **Edit Property** mode.
  * Update any value (e.g. rent or title) and click **Update Property** $\rightarrow$ Listing updates dynamically via AJAX.
* **Test Booking Request Management**:
  * Switch to the **Booking Requests** tab.
  * Inspect incoming tenant requests (Tenant name, Move-in date, Rent, Message).
  * Click **Approve Booking** or **Reject Request** $\rightarrow$ Updates database via AJAX without reloading.

---

### 2. 🛡️ Admin Dashboard (`/pages/admin/dashboard.php`)
* **Test Property Approvals**:
  * View all listings submitted across owners.
  * Filter by **Pending** to locate new/updated listings awaiting review.
  * Click **Approve** on a pending property $\rightarrow$ The property immediately becomes visible to tenants!
  * Click **Reject** to decline an unverified listing.
* **Test User Management**:
  * Switch to the **User Management** tab.
  * Filter by **Tenants**, **Owners**, **Active**, or **Blocked**.
  * Click **Block** to restrict an abusive user (blocked accounts cannot log in).
  * Click **Unblock** to restore access.
* **Test Platform Overview**:
  * Switch to **Platform Overview** to inspect live metric cards: Total Registered Users, Total Properties, Pending Approvals, and Total Bookings.

---

### 3. 🏡 Tenant Dashboard (`/pages/tenant/dashboard.php`)
* **Test Exploring Properties**:
  * Filter by **City** (e.g., *Bengaluru*, *Bhubaneswar*, *Hyderabad*, *Mumbai*, *Gurugram*, *Pune*).
  * Filter by **BHK** (*1RK*, *1BHK*, *2BHK*, *3BHK*, *4BHK+*).
  * Filter by **Max Budget** (*Up to ₹15,000*, *₹25,000*, *₹50,000*, *₹1,00,000*).
  * Live search by locality, landmark, or amenity.
  * *Note: Tenants only see properties marked `approved` by the Admin.*
* **Test Requesting a Booking**:
  * Click **Request to Book** on any property card.
  * Choose an expected move-in date, lease duration (6, 11, or 24 months), and write an optional note to the owner.
  * Click **Submit Booking Request** $\rightarrow$ Request is submitted to the owner as `PENDING`.
* **Test My Bookings**:
  * Switch to the **My Bookings** tab.
  * Track submitted requests with status pills (**All**, **Pending**, **Approved**, **Rejected**, **Cancelled**).
  * For **Pending** requests: Click **Cancel Request** to withdraw.
  * For **Approved** requests: Owner contact number and email are unlocked and revealed!

---

### 🔄 End-to-End Integrated Test Flow
1. **Login as Owner** (`rakeshbhuyan0101@gmail.com`) $\rightarrow$ Add a new property $\rightarrow$ Listing is saved with `PENDING` status.
2. **Login as Admin** (`admin@rentease.in`) $\rightarrow$ Go to *Property Approvals* $\rightarrow$ Click **Approve** on the new listing.
3. **Login as Tenant** (`ananya.deshmukh@gmail.com`) $\rightarrow$ The newly approved listing now appears under *Explore Properties* $\rightarrow$ Click **Request to Book**.
4. **Login as Owner** $\rightarrow$ Under *Booking Requests*, see the tenant's inquiry $\rightarrow$ Click **Approve Booking**.
5. **Login as Tenant** $\rightarrow$ Under *My Bookings*, status changes to `APPROVED` with owner contact details visible!

---

## 📡 API Endpoints Reference

### Authentication (`/api/auth/`)
| Endpoint | Method | Parameters | Description |
|---|---|---|---|
| `/api/auth/signin.php` | `POST` | `email`, `password` | Authenticates user, starts session, returns role redirect URL. |
| `/api/auth/signup.php` | `POST` | `full_name`, `email`, `phone`, `password`, `role` | Registers new tenant or owner with Bcrypt hash. |
| `/api/auth/logout.php` | `GET` | *None* | Destroys session and cookies, redirects to signin. |

---

### Owner APIs (`/api/owner/` and root `/api/`)
| Endpoint | Method | Parameters | Description |
|---|---|---|---|
| `/api/properties.php` or `/api/owner/getProperties.php` | `GET` | *Session Owner* | Returns all properties owned by session owner with status counts. |
| `/api/properties.php` or `/api/owner/addProperty.php` | `POST` (FormData) | `title`, `description`, `city`, `locality`, `full_address`, `bhk_type`, `furnishing`, `monthly_rent`, `security_deposit`, `amenities`, `cover_image` (file) | Inserts new property with `approval_status = 'pending'`. |
| `/api/owner/updateProperty.php` | `POST` (FormData) | `property_id`, all property fields, optional `cover_image` | Updates owner's property and resets status to `pending`. |
| `/api/owner/deleteProperty.php` | `POST` / `DELETE` | `property_id` | Deletes property belonging to session owner. |
| `/api/bookings.php` or `/api/owner/getBookings.php` | `GET` | *Session Owner* | Fetches booking requests for owner's properties with status counts. |
| `/api/owner/updateBookingStatus.php` | `POST` | `booking_id`, `status` (`approved` \| `rejected`) | Updates booking status for owner's property. |

---

### Admin APIs (`/api/admin/`)
| Endpoint | Method | Parameters | Description |
|---|---|---|---|
| `/api/admin/getDashboardStats.php` | `GET` | *Session Admin* | Returns platform statistics (users, properties, bookings breakdown). |
| `/api/admin/getProperties.php` | `GET` | *Session Admin* | Returns all properties across all owners with owner info. |
| `/api/admin/updatePropertyStatus.php` | `POST` | `property_id`, `status` (`approved` \| `rejected` \| `pending`) | Approves or rejects an owner property. |
| `/api/admin/getUsers.php` | `GET` | *Session Admin* | Returns list of all registered users with role and status counts. |
| `/api/admin/updateUserStatus.php` | `POST` | `user_id`, `status` (`active` \| `blocked`) | Blocks or unblocks a tenant/owner account. |

---

### Tenant APIs (`/api/tenant/`)
| Endpoint | Method | Parameters | Description |
|---|---|---|---|
| `/api/tenant/searchProperty.php` | `GET` | `search`, `city`, `bhk_type`, `max_rent` | Searches and filters verified (`approved`) and `available` listings. |
| `/api/tenant/createBooking.php` | `POST` | `property_id`, `move_in_date`, `lease_months`, `tenant_message` | Submits booking request for session tenant. |
| `/api/tenant/getBookings.php` | `GET` | *Session Tenant* | Returns tenant's bookings, specs, and owner contacts (revealed when approved). |
| `/api/tenant/cancelBooking.php` | `POST` | `booking_id` | Cancels a pending booking request. |
