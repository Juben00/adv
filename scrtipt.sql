CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_start_date DATE,
    booking_end_date DATE,
    booking_start_time TIME,
    booking_end_time TIME,
    booking_duration INT,
    booking_status_id INT,
    booking_participants INT,
    booking_grand_total DECIMAL(10,2),
    booking_guest_id INT,
    booking_venue_id INT,
    booking_payment_method INT,
    booking_payment_reference VARCHAR(255),
    booking_payment_status_id INT,
    booking_cancellation_reason TEXT NULL,
    booking_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    booking_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_guest_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_venue_id) REFERENCES venues(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_status_id) REFERENCES bookings_status_sub(id) ON DELETE SET NULL,
    FOREIGN KEY (booking_payment_method) REFERENCES payment_method_sub(id),
    FOREIGN KEY (booking_payment_status_id) REFERENCES payment_status_sub(id)
);

CREATE TABLE payment_method_sub (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_method_name VARCHAR(50)
);

INSERT INTO payment_method_sub (payment_method_name) VALUES 
('G-cash'), 
('PayMaya');

CREATE TABLE bookings_status_sub (
    id INT PRIMARY KEY,
    booking_status_name VARCHAR(50)
);

INSERT INTO bookings_status_sub (id, booking_status_name) VALUES
(1, 'Pending'),
(2, 'Confirmed'),
(3, 'Cancelled'),
(4, 'Completed');

CREATE TABLE payment_status_sub (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_status_name VARCHAR(50)
);

INSERT INTO payment_status_sub (payment_status_name) VALUES 
('Pending'), 
('Paid'), 
('Failed');
