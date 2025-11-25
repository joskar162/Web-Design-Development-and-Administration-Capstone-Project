-- Dynamic Class Management Application Database
-- TODO: Complete this database schema

-- Create the database

CREATE DATABASE IF NOT EXISTS dcma;
USE dcma;

--TODO: Create users table
-- Hint: Include fields for username, password, full name, email, role (student/lecturer)

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('student', 'lecturer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



--TODO: Create classes table
-- Hint: Include fields for class code, name, description, lecturer, schedule, room, capacity


CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    lecturer_id INT,
    schedule VARCHAR(100),
    room VARCHAR(50),
    capacity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE SET NULL
);

--TODO: Create enrollments table
-- Hint: Link students to classes, track enrollment status and grades

CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    grade DECIMAL(5, 2),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, class_id)
);



-- TODO: Create attendance table
-- Hint: Track attendance records with date, status (present/absent/late)

CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (enrollment_id, attendance_date)
);



--TODO: Insert sample data for testing
-- Create at least 2 lecturers and 3 students
-- Create at least 3 classes
-- Add some sample enrollments

INSERT INTO users (username, password, full_name, email, role) VALUES
('peter murithi', 'pk1234.', 'Dr. peter murithi', 'peter.murithi@university.edu', 'lecturer'),
('joshua karani', 'jk1234.', 'Prof.joshua karani', 'joshua.karani@university.edu', 'lecturer'),
('Charlie Brown', 'Cb1234.', 'Charlie Brown', 'charlie.brown@student.edu', 'student'),
('Diana Prince', 'Dp1234.', 'Diana Prince', 'diana.prince@student.edu', 'student'),
('Ethan Hunt', 'Eh1234.', 'Ethan Hunt', 'ethan.hunt@student.edu', 'student');

INSERT INTO classes (class_code, name, description, lecturer_id, schedule, room, capacity) VALUES
('CS101', 'Introduction to Computer Science', 'Basic concepts of computer science', 1, 'Mon/Wed 10:00-11:30', 'Room 101', 30),
('MATH201', 'Calculus II', 'Advanced calculus topics', 2, 'Tue/Thu 14:00-15:30', 'Room 202', 25),
('ENG101', 'English Composition', 'Writing and composition skills', 1, 'Mon/Wed/Fri 09:00-10:00', 'Room 103', 40);

INSERT INTO enrollments (student_id, class_id, enrollment_date, status, grade) VALUES
(3, 1, '2023-09-01', 'active', NULL),
(3, 2, '2023-09-01', 'active', NULL),
(4, 1, '2023-09-01', 'active', NULL),
(4, 3, '2023-09-01', 'active', NULL),
(5, 2, '2023-09-01', 'active', NULL),
(5, 3, '2023-09-01', 'active', NULL);