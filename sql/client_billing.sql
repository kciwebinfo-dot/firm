CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(150) NOT NULL,
  service_code VARCHAR(50) DEFAULT NULL,
  service_category VARCHAR(100) DEFAULT NULL,
  default_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  description TEXT DEFAULT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  trade_name VARCHAR(150) DEFAULT NULL,
  mobile VARCHAR(20) DEFAULT NULL,
  whatsapp_number VARCHAR(20) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  pan VARCHAR(20) DEFAULT NULL,
  gstin VARCHAR(30) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  city VARCHAR(100) DEFAULT NULL,
  state VARCHAR(100) DEFAULT NULL,
  pincode VARCHAR(12) DEFAULT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS client_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  service_id INT NOT NULL,
  assigned_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (client_id),
  INDEX (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bill_no VARCHAR(30) NOT NULL UNIQUE,
  client_id INT NOT NULL,
  bill_date DATE NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  taxable_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  grand_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  due_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_status ENUM('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  notes TEXT DEFAULT NULL,
  created_by INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bill_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bill_id INT NOT NULL,
  service_id INT DEFAULT NULL,
  service_name VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  qty DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  rate DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (bill_id),
  INDEX (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  receipt_no VARCHAR(30) NOT NULL UNIQUE,
  bill_id INT NOT NULL,
  client_id INT NOT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_mode VARCHAR(30) NOT NULL,
  reference_no VARCHAR(100) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  received_by INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (bill_id),
  INDEX (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO services (service_name, service_code, service_category, default_fee, description, status) VALUES
('ITR Filing','ITR','Income Tax',1500,'Income tax return filing',1),
('GST Return','GSTRET','GST',1200,'Monthly GST return work',1),
('GSTR-1 Filing','GSTR1','GST',700,'GSTR-1 filing',1),
('GSTR-3B Filing','GSTR3B','GST',700,'GSTR-3B filing',1),
('GST Registration','GSTREG','Registration',2500,'GST registration service',1),
('PAN Application','PAN','Registration',500,'PAN application support',1),
('MSME Registration','MSME','Registration',1200,'MSME/Udyam registration',1),
('Accounting Work','ACC','Accounting',3000,'Monthly accounting work',1);

INSERT INTO clients (name, trade_name, mobile, whatsapp_number, email, pan, gstin, address, city, state, pincode, status, created_by) VALUES
('Amit Sharma','Sharma Traders','9876543210','9876543210','amit@example.com','ABCDE1234F','27ABCDE1234F1Z5','Market Road','Mumbai','Maharashtra','400001',1,1),
('Priya Verma','Verma Consultancy','9876500001','9876500001','priya@example.com','BBCDE1234G','','Civil Lines','Delhi','Delhi','110001',1,1),
('Rahul Mehta','Mehta Stores','9876500002','9876500002','rahul@example.com','CBCDE1234H','24CBCDE1234H1Z7','Ring Road','Ahmedabad','Gujarat','380001',1,1);

INSERT INTO client_services (client_id, service_id, assigned_fee, status) VALUES
(1,1,1500,1),(1,2,1200,1),(2,5,2500,1),(3,3,700,1),(3,4,700,1);
