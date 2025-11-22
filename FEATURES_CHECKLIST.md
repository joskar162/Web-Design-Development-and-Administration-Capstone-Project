# Features Implementation Checklist

Use this checklist to track your progress as you build the Dynamic Class Management Application.

## Database Schema ✓

- [ ] Create `users` table with all required fields
- [ ] Create `classes` table with all required fields
- [ ] Create `enrollments` table with all required fields
- [ ] Create `attendance` table with all required fields
- [ ] Add appropriate primary keys
- [ ] Add foreign key constraints
- [ ] Add appropriate indexes
- [ ] Insert sample data (2+ lecturers, 3+ students, 3+ classes)
- [ ] Test database queries in phpMyAdmin

## Configuration & Setup ✓

- [ ] Complete database connection in `config.php`
- [ ] Implement `isLoggedIn()` helper function
- [ ] Implement `hasRole()` helper function
- [ ] Implement `requireLogin()` helper function
- [ ] Implement `requireRole()` helper function
- [ ] Implement `sanitize()` helper function
- [ ] Test database connection

## Authentication System ✓

### Login (login.php)
- [ ] Create login form (username, password fields)
- [ ] Handle form submission
- [ ] Query database for user
- [ ] Verify password using `password_verify()`
- [ ] Set session variables (user_id, username, full_name, email, role)
- [ ] Redirect to appropriate dashboard based on role
- [ ] Display error messages for invalid credentials
- [ ] Redirect if already logged in
- [ ] Add demo credentials display

### Logout (logout.php)
- [ ] Destroy session
- [ ] Redirect to login page

### Index (index.php)
- [ ] Check if user is logged in
- [ ] Redirect to appropriate dashboard or login page

## Lecturer Features ✓

### Dashboard (dashboard_lecturer.php)
- [ ] Require lecturer role
- [ ] Display welcome message with lecturer name
- [ ] Show "Create New Class" button
- [ ] Create class creation form with fields:
  - [ ] Class code
  - [ ] Class name
  - [ ] Description
  - [ ] Max students
  - [ ] Schedule day
  - [ ] Schedule time
  - [ ] Room
- [ ] Handle class creation form submission
- [ ] Validate form input
- [ ] Insert new class into database
- [ ] Display success/error messages
- [ ] Query and display lecturer's classes
- [ ] Show enrolled count for each class
- [ ] Add "View Details" button for each class
- [ ] Add "Delete" button for each class
- [ ] Handle class deletion
- [ ] Add JavaScript to toggle form visibility

### Class Details (class_details.php)
- [ ] Require lecturer role
- [ ] Get class_id from URL
- [ ] Verify lecturer owns the class
- [ ] Display class information (code, name, description, schedule, room)
- [ ] Query enrolled students
- [ ] Display students in a table with:
  - [ ] Student name
  - [ ] Email
  - [ ] Attendance rate
  - [ ] Current grade
- [ ] Create grade update form for each student
- [ ] Handle grade update submission
- [ ] Create "Mark Attendance" button for each student
- [ ] Create attendance modal with:
  - [ ] Date field
  - [ ] Status dropdown (present, absent, late)
- [ ] Handle attendance marking submission
- [ ] Add JavaScript for modal functionality
- [ ] Display success/error messages

## Student Features ✓

### Dashboard (dashboard_student.php)
- [ ] Require student role
- [ ] Display welcome message with student name
- [ ] Query student's enrolled classes
- [ ] Display enrolled classes with:
  - [ ] Class code and name
  - [ ] Description
  - [ ] Lecturer name
  - [ ] Schedule and room
  - [ ] Grade (if assigned)
  - [ ] "View Details" button
  - [ ] "Drop Class" button
- [ ] Query available classes (not enrolled, not full)
- [ ] Display available classes with:
  - [ ] Class code and name
  - [ ] Description
  - [ ] Lecturer name
  - [ ] Schedule and room
  - [ ] Enrolled count / max students
  - [ ] "Enroll" button
- [ ] Handle enrollment submission
- [ ] Check if already enrolled
- [ ] Check if class is full
- [ ] Insert enrollment record
- [ ] Handle drop class action
- [ ] Update enrollment status to 'dropped'
- [ ] Display success/error messages

### Class View (class_view.php)
- [ ] Require student role
- [ ] Get enrollment_id from URL
- [ ] Verify student owns the enrollment
- [ ] Display class information:
  - [ ] Class code and name
  - [ ] Description
  - [ ] Lecturer name and email
  - [ ] Schedule and room
- [ ] Display grade prominently if assigned
- [ ] Query attendance records
- [ ] Calculate attendance statistics:
  - [ ] Total sessions
  - [ ] Present count and percentage
  - [ ] Absent count
  - [ ] Late count
- [ ] Display attendance history in table with:
  - [ ] Date
  - [ ] Status (with color coding)
  - [ ] Notes
- [ ] Display "No records" message if empty

## UI/UX Styling ✓

### Login Page
- [ ] Center login box on page
- [ ] Style login form
- [ ] Add background gradient or color
- [ ] Style input fields
- [ ] Style login button
- [ ] Make it responsive

### Header
- [ ] Style main header
- [ ] Display app name/logo
- [ ] Show user info (name and role)
- [ ] Style logout button
- [ ] Make it responsive

### Dashboards
- [ ] Style dashboard container
- [ ] Create grid layout for class cards
- [ ] Style class cards with:
  - [ ] Hover effects
  - [ ] Proper spacing
  - [ ] Color scheme
- [ ] Style buttons (primary, secondary, danger)
- [ ] Style forms and inputs
- [ ] Add proper spacing and margins

### Tables
- [ ] Style data tables
- [ ] Add header styling
- [ ] Add row hover effects
- [ ] Make tables responsive

### Messages
- [ ] Style success messages (green)
- [ ] Style error messages (red)
- [ ] Add icons if desired

### Responsive Design
- [ ] Test on mobile devices
- [ ] Adjust grid layouts for small screens
- [ ] Make tables scrollable on mobile
- [ ] Adjust header for mobile

## Security Implementation ✓

- [ ] Use `password_hash()` for storing passwords
- [ ] Use `password_verify()` for checking passwords
- [ ] Sanitize all user inputs with `mysqli_real_escape_string()` or prepared statements
- [ ] Use `htmlspecialchars()` for all output
- [ ] Validate user roles before showing sensitive data
- [ ] Prevent direct access to pages without login
- [ ] Verify ownership before allowing actions (e.g., only lecturer can edit their classes)
- [ ] Add CSRF protection (bonus)

## Testing ✓

### Authentication Testing
- [ ] Test login with valid credentials
- [ ] Test login with invalid credentials
- [ ] Test logout functionality
- [ ] Test accessing protected pages without login
- [ ] Test role-based access (student can't access lecturer pages)

### Lecturer Testing
- [ ] Test creating a new class
- [ ] Test viewing class list
- [ ] Test viewing class details
- [ ] Test updating student grades
- [ ] Test marking attendance
- [ ] Test deleting a class
- [ ] Test with multiple lecturers (can't see each other's classes)

### Student Testing
- [ ] Test viewing available classes
- [ ] Test enrolling in a class
- [ ] Test viewing enrolled classes
- [ ] Test viewing class details
- [ ] Test viewing attendance records
- [ ] Test viewing grades
- [ ] Test dropping a class
- [ ] Test enrollment in full class (should fail)
- [ ] Test duplicate enrollment (should fail)

### Edge Cases
- [ ] Test with empty database
- [ ] Test with special characters in input
- [ ] Test with very long input
- [ ] Test SQL injection attempts
- [ ] Test XSS attempts
- [ ] Test concurrent enrollments
- [ ] Test deleting class with enrollments

## Documentation ✓

- [ ] Update README with setup instructions
- [ ] Document any additional features added
- [ ] Add code comments for complex logic
- [ ] Document database schema
- [ ] Create user guide (optional)
- [ ] List known issues/limitations (if any)

## Bonus Features (Optional) ⭐

- [ ] Assignment creation and submission
- [ ] File upload for assignments
- [ ] Email notifications
- [ ] Search/filter classes
- [ ] Export grades to CSV
- [ ] Class announcements
- [ ] Calendar view
- [ ] Student profile pictures
- [ ] Forgot password functionality
- [ ] Admin dashboard
- [ ] Grade analytics/charts
- [ ] Attendance reports
- [ ] Class capacity warnings
- [ ] Enrollment waitlist

## Final Checks ✓

- [ ] All features work as expected
- [ ] No PHP errors or warnings
- [ ] No JavaScript console errors
- [ ] Code is clean and well-organized
- [ ] Code is properly commented
- [ ] Database is properly normalized
- [ ] Security measures are in place
- [ ] UI is professional and user-friendly
- [ ] Application is responsive
- [ ] README is complete
- [ ] Ready for demo presentation

---

**Progress Tracking:**
- Total Tasks: ~150
- Completed: ___
- Remaining: ___
- Completion: ___%

**Target Completion Date:** ___________

**Notes:**
_Use this space to track issues, questions, or ideas_
