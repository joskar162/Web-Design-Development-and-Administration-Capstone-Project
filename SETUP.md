# Setup Instructions

## Prerequisites

Before you begin, ensure you have:
- XAMPP, WAMP, or LAMP installed
- PHP 7.4 or higher
- MySQL 5.7 or higher
- A code editor (VS Code, Sublime Text, etc.)
- A web browser

## Installation Steps

### 1. Download the Starter Template

Download or clone this repository to your local machine.

### 2. Move to Web Server Directory

Copy the `dcma-starter` folder to your web server's root directory:

**For XAMPP:**
```
C:/xampp/htdocs/dcma-starter
```

**For WAMP:**
```
C:/wamp64/www/dcma-starter
```

**For LAMP (Linux):**
```
/var/www/html/dcma-starter
```

### 3. Start Your Web Server

**XAMPP/WAMP:**
- Open the control panel
- Start Apache
- Start MySQL

**LAMP:**
```bash
sudo service apache2 start
sudo service mysql start
```

### 4. Create the Database

**Option A: Using phpMyAdmin**
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy the contents of `database/dcma_template.sql`
4. Paste and click "Go"

**Option B: Using Command Line**
```bash
mysql -u root -p < database/dcma_template.sql
```

### 5. Configure Database Connection

1. Open `config/config.php`
2. Update the database credentials if needed:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';  // Your MySQL password (usually empty for XAMPP)
   $db = 'dcma';
   ```

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost/dcma-starter
```

## Verify Installation

You should see the login page. If you see errors:

### Common Issues

**"Connection failed"**
- Check if MySQL is running
- Verify database credentials in `config/config.php`
- Ensure the `dcma` database exists

**"Page not found"**
- Verify the folder is in the correct location
- Check if Apache is running
- Try accessing `http://localhost/dcma-starter/login.php` directly

**"Blank page"**
- Enable error reporting in PHP
- Check Apache error logs
- Verify PHP is installed correctly

## Development Workflow

1. **Plan your database schema** - Design tables on paper first
2. **Implement database** - Complete `dcma_template.sql`
3. **Build authentication** - Start with login/logout
4. **Create dashboards** - Build lecturer and student views
5. **Add features incrementally** - One feature at a time
6. **Test thoroughly** - Test each feature before moving on
7. **Style your application** - Make it look professional

## Testing Your Work

Create test accounts in your database:
```sql
-- Lecturer account (password: password123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('john_lecturer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Dr. John Smith', 'john@university.edu', 'lecturer');

-- Student account (password: password123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('alice_student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Alice Johnson', 'alice@student.edu', 'student');
```

## Getting Help

If you encounter issues:
1. Check the error messages carefully
2. Review your code for syntax errors
3. Verify database queries in phpMyAdmin
4. Consult PHP/MySQL documentation
5. Ask your instructor or peers

## Next Steps

1. Read through all the TODO comments in the code
2. Review the README.md for feature requirements
3. Start with the database schema
4. Build features incrementally
5. Test frequently

Good luck with your project! 🚀
