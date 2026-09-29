# Waste Food Management System (FoodRescue) - v1.0 MVP

A full-stack, responsive web application connecting surplus-food donors (restaurants, caterers, bakeries) with food recipients (shelters, community charities, volunteers) to reduce food waste and support communities in need.

Built strictly according to the **SRS v1.0**, **PRD v1.0**, and **TRD v1.0** specifications with a clean, beginner-friendly architecture using **HTML, CSS, PHP, and MySQL**.

---

## 🎨 Figma Design System Implementation

Designed with the exact MVP specifications:
- **Primary Color:** `#4CAF50` (Fresh leafy green for CTAs, active highlights)
- **Secondary Color:** `#FFB74D` (Warm amber accent for attention, notices)
- **Background Color:** `#FFFDF8` (Warm soft cream canvas)
- **Text Color:** `#2D2D2D` (High contrast accessible charcoal)
- **Error Color:** `#D32F2F` (Clear validation feedback)
- **Typography:** Inter and Poppins font pairing (`'Poppins', 'Inter', sans-serif`)
- **Status Badges:**
  - ⏳ `Pending`
  - ✔ `Approved`
  - ✖ `Rejected`
  - ⛔ `Cancelled`
  - 🔒 `Reserved`
  - 🌿 `Available` / ⌛ `Expired`
- **Component System:** Reusable buttons (`.btn-primary`, `.btn-secondary`, `.btn-outline`), cards (`.card`, `.food-card`, `.metric-card`), accessible forms with `:user-invalid` styling, responsive data tables with mobile-stacked card conversions, search/filter bars, and confirmation dialogs.

---

## 📱 9 Responsive Screens Implemented

1. **Sign In Screen (`/login`):**
   - Email, password, "Forgot Password" link, "Sign In" button.
   - Separate links to register as donor or recipient.
   - Quick one-click demo account auto-fill buttons.
2. **Registration Screen (`/register`):**
   - Full name, email, password, organization, phone.
   - Interactive role choice cards (Receive Food vs Donate Food).
   - Public admin signup strictly blocked (admin accounts provisioned privately).
3. **Donor Dashboard Screen (`/donor/dashboard`):**
   - Real-time stat cards: **Active Listings: 8**, **Pending Requests: 3**, Total Listings.
   - "Add Food Listing" primary CTA.
   - Responsive listings table: *Vegetable Curry / 10 Meals / Available*; *Bread Packets / 15 Packs / Reserved*.
   - Friendly empty state when no listings exist: *"No listings yet. Add your first food listing."*
4. **Create / Edit Listing Screen (`/donor/listings/new`, `/donor/listings/{id}/edit`):**
   - Food title, detailed description, quantity, unit (Meals, Packs, Boxes, Portions, Kg, Liters).
   - Available-until date/time (with future date restriction).
   - Pickup location and instructions text.
   - Save / Cancel actions with inline validation error states.
5. **Recipient Browse Screen (`/listings`):**
   - Search bar & unit filtering.
   - Clean card grid displaying only active, unexpired food offers.
   - Badges showing available status, quantity, area, and pickup deadline.
6. **Listing Detail Screen (`/listings/{id}`):**
   - Detailed showcase: *Vegetable Curry, 10 Meals, available until 7 PM, Main Street Community Hall, Green Kitchen*.
   - Pickup instructions from chef/donor.
   - Integrated "Request Food" form with optional note for authenticated recipients.
7. **Request Status Screen (`/recipient/requests`):**
   - Flash banner: *"Request submitted successfully. Status: Pending."*
   - Status history table: *Vegetable Curry / Today / Pending*; *Rice Packets / Yesterday / Approved*.
   - Cancel button for Pending requests.
   - Direct pickup contact details displayed upon approval.
8. **Donor Requests Screen (`/donor/requests`):**
   - Recipient details, listing title, requested date, notes.
   - Direct **Approve** and **Reject** actions.
   - **Atomic Reservation:** Approving a request reserves the listing immediately and disables further approvals.
9. **Admin Command Center (`/admin/dashboard`, `/admin/users`, `/admin/listings`, `/admin/requests`, `/admin/reports`):**
   - Role-protected administrative interface.
   - Platform metrics: **Users: 120**, **Listings: 45**, **Requests: 89**.
   - User directory with confirmation-prompted deactivation/reactivation.
   - Listing moderation with confirmation removal of invalid items.
   - Detailed activity breakdown reports.

---

## 🗄️ Database Architecture

The MySQL database `waste_food_db` consists of 3 relational tables:
1. **`users`:** `id`, `name`, `email` (unique), `password_hash`, `role` (`donor`, `recipient`, `admin`), `organization`, `phone`, `is_active`, timestamps.
2. **`food_listings`:** `id`, `donor_id` (FK), `title`, `description`, `quantity` (> 0), `unit`, `available_until`, `pickup_location`, `pickup_instructions`, `organization_name`, `status` (`available`, `reserved`, `unavailable`, `expired`), timestamps.
3. **`food_requests`:** `id`, `listing_id` (FK), `recipient_id` (FK), `message`, `status` (`pending`, `approved`, `rejected`, `cancelled`), `decided_at`, timestamps.

---

## 🚀 Getting Started & Setup Guide

### 1. Requirements
- PHP 8.1+ (tested on PHP 8.4) with PDO and pdo_mysql extensions.
- MySQL or MariaDB running locally on port 3306 (default XAMPP setup).

### 2. Database Setup & Seeding
From the project folder `d:\Raj-alisha`, run:
```bash
php database/seed.php
```
This automatically resets the tables and provisions **only the initial Administrator account**:
- **Email:** `admin@wastefood.org`
- **Password:** `password123`
- **Role:** `admin`

No mock food listings, fake users, or dummy requests are pre-seeded. All data is real and dynamically rendered from what you add through the application!

### 3. Start the Web Server
Launch the PHP built-in server:
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```
Now open your web browser and navigate to:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Initial Administrator Account

| Role | Email | Password | Access & Responsibilities |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@wastefood.org` | `password123` | Full oversight: View real-time registered users, listings, requests, reports, deactivate abusive users, and remove invalid listings. |

---

## 👥 How to Add Real Data for Demonstration

1. **Register a Donor Account:**
   - Go to [http://127.0.0.1:8000/register?role=donor](http://127.0.0.1:8000/register?role=donor)
   - Enter your name, email, password, and kitchen/organization name (e.g., *Green Kitchen*).
   - Click **Create Account**.

2. **Add Food Listings as Donor:**
   - From your Donor Dashboard, click **➕ Add Food Listing**.
   - Fill in:
     - Title: *Vegetable Curry*
     - Description: *Freshly prepared mixed vegetable curry with aromatic spices and herbs.*
     - Quantity & Unit: *10 Meals*
     - Available Until: Select tomorrow 7:00 PM.
     - Pickup Location: *Main Street Community Hall*
     - Pickup Instructions: *Ask for the kitchen manager at the side door.*
   - Click **Create Listing**. It now appears immediately in the database and on the browse page!

3. **Register a Recipient Account:**
   - Sign out, or open an incognito/private browser window.
   - Go to [http://127.0.0.1:8000/register?role=recipient](http://127.0.0.1:8000/register?role=recipient)
   - Enter details (e.g. *Main Street Shelter*).

4. **Browse & Request Food:**
   - Go to [http://127.0.0.1:8000/listings](http://127.0.0.1:8000/listings).
   - Click **View Details & Request** on the Vegetable Curry card.
   - Enter a collection note and click **🍲 Request Food**.
   - Your request status will show: `⏳ Pending`.

5. **Approve Request as Donor:**
   - Sign back in as the Donor and go to [http://127.0.0.1:8000/donor/requests](http://127.0.0.1:8000/donor/requests).
   - Click **✔ Approve**.
   - The listing will be automatically reserved, blocking competing requests!

6. **Check Admin HQ:**
   - Sign in as `admin@wastefood.org` / `password123`.
   - View your real counts on the admin dashboard, inspect the user list, review requests, and check dynamic reports.
