# 🌄 TravelKH — Modern Cambodian Tour & Travel Booking Platform

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%20%7C%208.x-8892BF?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql" alt="MySQL Database">
  <img src="https://img.shields.io/badge/Payments-Stripe-008F81?style=for-the-badge&logo=stripe" alt="Stripe Integration">
  <img src="https://img.shields.io/badge/Frontend-Bootstrap%205-7952B3?style=for-the-badge&logo=bootstrap" alt="Bootstrap CSS">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

---

## 🌍 About TravelKH

**TravelKH** is a premium, lightweight, and modern travel package search and booking application tailored for exploring Cambodia. It connects travellers directly with custom travel packages, providing detailed itineraries, duration information, pricing, and a secure checkout pipeline powered by the Stripe API. The application is built using a clean PHP & MySQL database architecture with dynamic modern styling.

---

## ✨ Core Features

### 👤 Customer Experience
*   **Interactive Search & Discovery:** Instantly search and filter tours by locations, keywords, or durations.
*   **Premium Tour Details Page:** Features high-resolution cover views, structured itineraries, and a sticky pricing sidebar highlighting booking confirmation guarantees.
*   **Stripe checkout pipeline:** Secure, integrated booking process using Stripe JS SDK card validation and backend cURL API charges.
*   **Personalised Dashboard:** Dynamic profile cards with direct file upload functionality for user avatars, password configuration, and travel history logs.

### 🛡️ Admin Dashboard
*   **Manage Tours:** Create, update, or disable travel packages with local file upload support for tour covers.
*   **Manage Bookings:** Change booking statuses (approve, reject, delete), track Stripe tokens, and update customer package totals.
*   **Manage Users:** Access control panels to modify statuses (active vs. inactive), change user passwords, and manage administrative roles.

---

## 🛠️ Technology Stack

| Category | Technologies Used |
| :--- | :--- |
| **Backend** | PHP (Procedural, Prepared Statements, MySQLi, API cURL) |
| **Frontend** | HTML5, Vanilla CSS3, Javascript (ES6), Bootstrap 5.3.3, AOS (Animate on Scroll) |
| **Fonts** | Google Fonts (Outfit, Playfair Display) |
| **Database** | MySQL / MariaDB |
| **Payment Gateway** | Stripe API Gateway (JS SDK & Backend Charges API) |

---

## 📂 Project Architecture

```text
TravelKH/
├── admin/                     # Admin control dashboard pages
│   ├── bookings.php           # Admin bookings controller (CRUD)
│   ├── index.php              # Dashboard home statistics
│   ├── profile.php            # Admin profile security
│   ├── tours.php              # Admin tours manager (with file uploads)
│   └── users.php              # Admin user account control (roles & status)
├── assets/                    # Static UI & User Upload assets
│   ├── css/                   # Stylesheets (style.css, profile.css, tours.css)
│   └── images/                # Tour assets & uploaded user avatars
├── auth/                      # Session & credentials controls
│   ├── login.php / register.php # Signin & registration logic
│   └── logout.php             # Session destruction
├── database/
│   └── travel_tour_db.sql     # Database schema & seed values
├── includes/                  # Common code libraries
│   ├── config.php             # Core configuration & image resolution helpers
│   ├── auth.php               # Login checks & authorization functions
│   └── header.php / footer.php / navbar.php # Global templates
├── index.php                  # Homepage & featured packages list
├── tours.php                  # Interactive search page listing active packages
└── tour-details.php           # Details page with premium booking sidebar
```

---

## 🚀 Installation & Local Setup

### 📋 Prerequisites
1.  Install **XAMPP** (or WampServer/MAMP) with PHP 7.4+ and MySQL.
2.  Install a code editor (e.g., VS Code).

### 🛠️ Configuration Steps

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/your-username/TourKH.git
    ```
    *Place the project inside the server root directory (e.g. `C:/xampp/htdocs/TourKH`).*

2.  **Import the Database:**
    *   Start Apache and MySQL in the XAMPP Control Panel.
    *   Open browser and navigate to `http://localhost/phpmyadmin`.
    *   Create a new database named `travel_tour_db`.
    *   Import [database/travel_tour_db.sql](file:///C:/xampp/htdocs/TourKH---Php--main/database/travel_tour_db.sql) into the database.

3.  **Local Credentials Setup:**
    Create a new file named `config.local.php` inside the `includes/` folder to securely set up your database and payment credentials. This file is ignored by Git.
    ```php
    <?php
    $host = "localhost";
    $user = "root";
    $pass = "your_mysql_password"; // Add your MySQL root password here
    $db   = "travel_tour_db";

    $stripe_secret = "sk_test_xxxxxx";      // Add your Stripe Secret Key
    $stripe_publishable = "pk_test_xxxxxx"; // Add your Stripe Publishable Key
    ```

4.  **Run the Project:**
    Open your browser and navigate to:
    `http://localhost/TourKH`

---

## 🔮 Future Roadmap

*   **KHQR Payment Integration:** Support scanning Bakong KHQR codes to process direct bank transfers locally.
*   **Decoupled Frontend API:** Migrate to a Laravel/Node REST API backend combined with a Next.js/React frontend for enhanced performance.
*   **Real-time Customer Support:** Integrate WebSockets for live chat channels between customers and agents.
*   **Geospatial Maps Integration:** Integrate map layouts (Google Maps/Mapbox) to show travel packages geographically.

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.
