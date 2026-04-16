
CREATE DATABASE IF NOT EXISTS bike_shop;
USE bike_shop;


CREATE TABLE users (
    uid int(11) NOT NULL AUTO_INCREMENT,
    username varchar(50) NOT NULL,
    password varchar(50) NOT NULL,
    fullname varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    phone varchar(20) DEFAULT NULL,
    role varchar(20) DEFAULT 'user',
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (uid),
    UNIQUE KEY username (username)
);


CREATE TABLE bicycles (
    bike_id int(11) NOT NULL AUTO_INCREMENT,  
    name varchar(100) NOT NULL,
    category varchar(50) NOT NULL,
    serial_number varchar(100) DEFAULT NULL,
    price_per_day decimal(10,2) DEFAULT '10.00',
    quantity int(11) DEFAULT '1',
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (bike_id),  -- Updated to match
    UNIQUE KEY serial_number (serial_number)
);


CREATE TABLE rentals (
    rental_id int(11) NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    bike_id int(11) NOT NULL,
    rent_start_date date NOT NULL,
    expected_return_date date NOT NULL,
    actual_return_date date DEFAULT NULL,
    quantity_rented int(11) DEFAULT '1',
    total_amount decimal(10,2) DEFAULT NULL,
    late_fee decimal(10,2) DEFAULT '0.00',
    rental_status varchar(20) DEFAULT 'active',
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rental_id),
    KEY user_id (user_id),
    KEY bike_id (bike_id),
    FOREIGN KEY (user_id) REFERENCES users (uid),
    FOREIGN KEY (bike_id) REFERENCES bicycles (bike_id)  
);


CREATE TABLE charity_donations (
    donation_id int(11) NOT NULL AUTO_INCREMENT,
    rental_id int(11) NOT NULL,
    user_id int(11) NOT NULL,
    days_late int(11) DEFAULT NULL,
    amount decimal(10,2) DEFAULT NULL,
    charity_name varchar(255) DEFAULT NULL,
    donation_date timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (donation_id),
    KEY rental_id (rental_id),
    KEY user_id (user_id),
    FOREIGN KEY (rental_id) REFERENCES rentals (rental_id),
    FOREIGN KEY (user_id) REFERENCES users (uid)
);


INSERT INTO users (username, password, fullname, email, role) VALUES
('adminc', MD5('admingc'), 'Administrator', 'calvingodspower@gmail.com', 'admin'),
('calvin', MD5('calv'), 'Calvin E', 'calvingodspower@gmail.com', 'user'),
('godsp0wer', MD5('gp'), 'Godspwer E', 'calvingodspower@gmail.com', 'user');

INSERT INTO bicycles (name, category, serial_number, price_per_day, quantity) VALUES
('Mountain Explorer Pro', 'Mountain', 'TK-MRLN-001', 5.00, 3),
('City Commuter Deluxe', 'City', 'GT-ESC-002', 3.00, 5),
('Road Speedster', 'Road', 'SP-ALZ-003', 8.00, 2),
('Eco Electric Plus', 'Electric', 'RD-RC5-004', 11.00, 2),
('Hybrid Adventure', 'Hybrid', 'CN-QK4-005', 6.00, 4),
('Kids Fun Bike', 'Kids', 'SW-KL-006', 2.00, 6),
('Beach Cruiser', 'Cruiser', 'EL-TWN-007', 4.00, 3);


SELECT 'Users:' as 'Table', COUNT(*) as 'Count' FROM users
UNION ALL
SELECT 'Bicycles:', COUNT(*) FROM bicycles;