# E-Pharma 

E-Pharma is a full-stack web-based pharmacy management system developed using PHP, MySQL, JavaScript, and integrated third-party services.  
The platform simulates a real-world online pharmacy with authentication, shopping cart, payment integration, and admin management.

---

##  Key Features

###  Authentication System
- User registration
- Secure login & logout
- Email verification
- Forgot & reset password
- "Remember me" functionality
- Session management

### Shopping System
- Product listing by category
- Add to cart (AJAX)
- Update quantity
- Remove items
- Checkout system
- Invoice generation

###  Payment Integration
- PayPal API integration
- Order creation & capture
- Payment validation

###  User Dashboard
- Profile management
- Edit profile
- Upload profile picture
- Order history

### Admin Panel
- Admin authentication
- Dashboard overview
- Add / Edit / Delete products
- Add / Edit / Delete users
- Product image upload
- AJAX admin operations

###  Email System
- PHPMailer integration
- Email verification
- Password reset emails

---

## Technologies Used

- PHP (Core PHP)
- MySQL
- JavaScript (AJAX)
- HTML5
- CSS3
- PayPal API
- PHPMailer

---

##  Project Architecture

The project is structured into:

- `public/` → Frontend pages & AJAX endpoints
- `includes/` → Authentication, sessions, database logic
- `admin/` → Admin panel
- `assets/` → CSS, JS, images
- `uploads/` → Product & user uploads
- `PHPMailer` → Email handling
- PayPal client integration

The application follows a modular structure separating logic, authentication, and UI components.

---

## ▶️ How to Run the Project

1. Install XAMPP / WAMP
2. Move the project folder into `htdocs`
3. Start Apache & MySQL
4. Import the database into phpMyAdmin
5. Configure database credentials in `includes/db.php`
6. Open in browser:

   http://localhost/e-pharma/public/

---

##  Security Features

- Password hashing
- Session validation
- Token-based password reset
- Protected admin routes
- Server-side validation

---

Developed by Elona Sharra 