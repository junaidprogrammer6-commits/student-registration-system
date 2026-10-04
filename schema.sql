CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registration_number VARCHAR(20) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  father_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  dob DATE NULL,
  gender ENUM('Male','Female','Other') NOT NULL,
  cnic VARCHAR(30) DEFAULT '',
  address VARCHAR(255) DEFAULT '',
  city VARCHAR(50) NOT NULL,
  province VARCHAR(50) DEFAULT '',
  postal_code VARCHAR(20) DEFAULT '',
  qualification VARCHAR(100) DEFAULT '',
  course VARCHAR(100) NOT NULL,
  institute VARCHAR(150) DEFAULT '',
  admission_year VARCHAR(10) DEFAULT '',
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_reg (registration_number),
  UNIQUE KEY uniq_email (email),
  UNIQUE KEY uniq_username (username)
);

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_admin_username (username)
);
