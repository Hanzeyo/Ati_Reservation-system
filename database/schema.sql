CREATE DATABASE IF NOT EXISTS ati_reservation;
USE ati_reservation;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20),
    office_agency VARCHAR(150),
    category ENUM('ATI Personnel', 'Gov Agency', 'Private / Guest') NOT NULL,
    role ENUM('user', 'admin', 'super_admin') DEFAULT 'user',
    status ENUM('pending', 'active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('halls', 'dormitories') NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    rate VARCHAR(50) NOT NULL,
    capacity VARCHAR(50),
    location VARCHAR(100),
    description TEXT,
    status ENUM('available', 'maintenance', 'inactive') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    floor VARCHAR(50),
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_no VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    room_id INT,
    event_title VARCHAR(255),
    pax_count INT,
    start_date DATE NOT NULL,
    end_date DATE,
    time_slot VARCHAR(100),
    special_notes TEXT,
    status ENUM('Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance', 'Approved', 'Checked In', 'No-Show', 'Rejected', 'Cancelled', 'Completed') DEFAULT 'Pending',
    admin_remarks TEXT,
    check_in_at TIMESTAMP NULL DEFAULT NULL,
    check_in_by INT NULL DEFAULT NULL,
    check_out_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value LONGTEXT,
    updated_by INT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
