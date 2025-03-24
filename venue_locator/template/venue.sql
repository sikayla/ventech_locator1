-- Create the first database for properties
CREATE DATABASE IF NOT EXISTS venue_db;

-- Use the database 'venue_db' for properties
USE venue_db;

-- Create the 'properties' table
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    property_type ENUM('Residential', 'Commercial', 'Industrial', 'Land'),
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    image_url VARCHAR(255) DEFAULT 'images/default.jpg',  -- Default image in case no image is provided
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert records into the 'properties' table
INSERT INTO properties (title, description, price, property_type, latitude, longitude, image_url)
VALUES 
('Venue Special', 'Prime retail space in a bustling location.', 500000.00, 'Commercial', 10.3167, 123.8874, '/venue_locator/images/venue1.jpg'),
('Wide Venue', 'A beautiful luxury house in a peaceful neighborhood.', 20000000.00, 'Residential', 10.3200, 123.8920, '/venue_locator/images/venue2.jpg');

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    username VARCHAR(100) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),  -- Ensure passwords are hashed (bcrypt, etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL;

CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(255) NOT NULL,
    category2 VARCHAR(100) NOT NULL,
    category3 VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO venues (name, description, price, category, category2, category3, image)
VALUES 
('Molino 3 Bacoor 3 Basketball Court', ' (10 person, 5 amenities, low price).', 500, 'Covered Court', 'high price', '5', '/venue_locator/images/court.png'),
('Bacoor City Hall COVERED COURT', ' (9 person, 6 amenities, high price).', 500, 'Covered Court', 'high price', '10', '/venue_locator/images/court.png'),
('San Lorenzo Ruiz Homes Covered Court', 'Sanggunian Kabataan Molino 7 Office', 300, 'Covered Court', 'low price', '6', '/venue_locator/images/court.png'),
('Molino 1 (Progressive 18) Covered Court', ' Molino 1(Progressive 18) Covered Court', 350, 'Covered Court', 'low price', '6', '/venue_locator/images/court.png'),
('Molino 3 Bacoor 3 Basketball Court', ' (10 person, 5 amenities, low price).', 500, 'Covered Court', 'high price', '5', '/venue_locator/images/court.png'),
('Bacoor City Hall COVERED COURT', ' (9 person, 6 amenities, high price).', 500, 'Covered Court', 'high price', '10', '/venue_locator/images/court.png'),
('San Lorenzo Ruiz Homes Covered Court', 'Sanggunian Kabataan Molino 7 Office', 300, 'Covered Court', 'low price', '6', '/venue_locator/images/court.png'),
('Molino 1 (Progressive 18) Covered Court', ' Molino 1(Progressive 18) Covered Court', 350, 'Covered Court', 'low price', '6', '/venue_locator/images/court.png');


ALTER TABLE venues ADD COLUMN image_path VARCHAR(255) DEFAULT '/venue_locator/images/court.png';
-- Ensure the table exists

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,  -- Change from venue_name to venue_id for correct foreign key reference
    event_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    num_attendees INT NOT NULL CHECK (num_attendees > 0),
    total_cost DECIMAL(10,2) NOT NULL CHECK (total_cost >= 0),
    payment_method ENUM('Cash', 'Credit/Debit', 'Online') NOT NULL,
    shared_booking BOOLEAN NOT NULL DEFAULT FALSE,
    id_photo VARCHAR(255) NULL,
    status ENUM('Pending', 'Canceled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
);

SELECT id, id_photo FROM bookings;
ALTER TABLE bookings MODIFY COLUMN status ENUM('Pending', 'Canceled', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending';



CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO admins (username, password) 
VALUES ('admin', '1234'),
       ('admin1', '$2y$10$u6/l7p9YgZ0z5mLhFYxJbujGZ5r1qU6/78nX7N.R/FG81W7GJevsK');


CREATE TABLE user_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    username VARCHAR(100) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    profile_image VARCHAR(255) NULL
);

SELECT COUNT(*) FROM users;

SELECT * FROM users;

SELECT COUNT(*) FROM users;



-- Insert sample admin user with hashed password ('admin' with password '1234')



-- Add status column separately

