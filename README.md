# SkillSwap - Learn, Teach, and Connect

SkillSwap is a web platform that connects people who want to learn and teach different skills. Users can exchange their expertise, join events, and grow together through mutual learning.

## Features

- User registration and authentication
- Skill matching system
- Contact form
- Newsletter subscription
- Event listings
- Premium and free membership tiers

## Tech Stack

- Frontend:
  - HTML5
  - Tailwind CSS
  - JavaScript
  - Font Awesome
  - Google Fonts

- Backend:
  - PHP
  - MySQL
  - Apache Server

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/skillswap.git
```

2. Set up your XAMPP/Apache environment:
   - Copy all files to your web root directory
   - Ensure Apache and MySQL services are running

3. Create the database:
   - Open phpMyAdmin
   - Create a new database named 'skillswap'
   - Import the database schema from `database/skillswap.sql`

4. Configure the database connection:
   - Open `config/db.php`
   - Update the database credentials if needed:
     ```php
     $host = 'localhost';
     $dbname = 'skillswap';
     $username = 'root';
     $password = '';
     ```

5. Configure your virtual host (optional):
   ```apache
   <VirtualHost *:80>
       ServerName skillswap.local
       DocumentRoot "/path/to/skillswap"
       <Directory "/path/to/skillswap">
           Options Indexes FollowSymLinks MultiViews
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

## Project Structure

```
skillswap/
├── api/                    # PHP API endpoints
│   ├── auth.php           # Authentication utilities
│   ├── contact.php        # Contact form handler
│   ├── login.php          # Login endpoint
│   ├── newsletter.php     # Newsletter subscription
│   └── signup.php         # User registration
├── config/                # Configuration files
│   └── db.php            # Database configuration
├── css/                   # Stylesheets
│   └── style.css         # Custom styles
├── database/              # Database files
│   └── skillswap.sql     # Database schema
├── js/                    # JavaScript files
│   ├── api.js            # API integration
│   └── main.js           # Main JavaScript
├── .htaccess             # Apache configuration
├── error.php             # Error handling
├── index.html            # Landing page
├── sign-up.html          # Registration page
└── README.md             # Project documentation
```

## Usage

1. Visit the homepage at `http://localhost/skillswap` (or your configured domain)
2. Create an account using the "Join Now" button
3. Select your role (Learn/Teach)
4. Choose your skills/interests
5. Start connecting with other users

## Security Features

- Password hashing
- Session management
- CORS configuration
- Security headers
- Input validation
- Error handling
