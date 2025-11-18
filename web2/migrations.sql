-- Complete migration SQL for Dorm Management System
CREATE DATABASE IF NOT EXISTS dorm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dorm_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100),
  role ENUM('resident','admin') NOT NULL DEFAULT 'resident',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE residents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  student_no VARCHAR(50),
  room_no VARCHAR(50),
  name VARCHAR(100),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE repairs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  resident_id INT NULL,
  location VARCHAR(200) NOT NULL,
  item VARCHAR(200) NOT NULL,
  description TEXT,
  status ENUM('待處理','處理中','已完成') DEFAULT '待處理',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL
);

CREATE TABLE activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  activity_date DATE NOT NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE activity_signups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  activity_id INT NOT NULL,
  resident_id INT NULL,
  student_or_room VARCHAR(100),
  signup_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
  FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL
);

CREATE TABLE beverage_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  resident_id INT NULL,
  description VARCHAR(255),
  amount DECIMAL(8,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL
);
