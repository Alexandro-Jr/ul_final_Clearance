CREATE DATABASE IF NOT EXISTS clearance_db;
USE clearance_db;
CREATE TABLE receipts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(50),
  student_name VARCHAR(100),
  amount DECIMAL(10,2),
  date DATE,
  category VARCHAR(50),
  payment_method VARCHAR(50),
  description TEXT,
  file_path VARCHAR(255)
);