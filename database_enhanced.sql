-- Enhanced TravelGo Database Schema
-- Run this file to add all new tables and enhancements

USE travel_db;

-- Enhanced destinations table (add new columns)
ALTER TABLE destinations 
ADD COLUMN IF NOT EXISTS featured TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS lat DECIMAL(10, 8) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS lon DECIMAL(11, 8) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS price_range VARCHAR(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    booking_date DATE NOT NULL,
    return_date DATE,
    travelers INT DEFAULT 1,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_method VARCHAR(50),
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    images TEXT,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, destination_id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error', 'booking', 'review') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User preferences table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    currency VARCHAR(10) DEFAULT 'USD',
    language VARCHAR(10) DEFAULT 'en',
    theme VARCHAR(20) DEFAULT 'light',
    email_notifications TINYINT(1) DEFAULT 1,
    push_notifications TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment transactions table
CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255) UNIQUE,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Destination categories/tags
CREATE TABLE IF NOT EXISTS destination_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Many-to-many relationship for destination tags
CREATE TABLE IF NOT EXISTS destination_tag_map (
    destination_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (destination_id, tag_id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES destination_tags(id) ON DELETE CASCADE
);

-- Sample data for destination tags
INSERT IGNORE INTO destination_tags (name, slug, description, icon) VALUES
('Beach', 'beach', 'Coastal and beach destinations', 'fa-umbrella-beach'),
('Mountain', 'mountain', 'Mountain and hiking destinations', 'fa-mountain'),
('City', 'city', 'Urban and city destinations', 'fa-city'),
('Adventure', 'adventure', 'Adventure and outdoor activities', 'fa-hiking'),
('Cultural', 'cultural', 'Cultural and heritage sites', 'fa-landmark'),
('Relaxation', 'relaxation', 'Spa and relaxation destinations', 'fa-spa'),
('Wildlife', 'wildlife', 'Wildlife and nature destinations', 'fa-paw'),
('Luxury', 'luxury', 'Luxury and premium destinations', 'fa-gem'),
('Budget', 'budget', 'Budget-friendly destinations', 'fa-dollar-sign'),
('Family', 'family', 'Family-friendly destinations', 'fa-users');

-- Sample destinations data (if table is empty)
INSERT IGNORE INTO destinations (id, country, image, description, featured, lat, lon, price_range, rating) VALUES
(1, 'Paris, France', 'paris.jpg', 'The City of Light offers iconic landmarks like the Eiffel Tower, world-class museums, and exquisite cuisine.', 1, 48.8566, 2.3522, '$$$', 4.7),
(2, 'Tokyo, Japan', 'tokyo.jpg', 'A fascinating blend of traditional culture and cutting-edge technology in Asia\'s most vibrant metropolis.', 1, 35.6762, 139.6503, '$$', 4.8),
(3, 'New York, USA', 'newyork.jpg', 'The city that never sleeps - experience Broadway, Central Park, and the Statue of Liberty.', 1, 40.7128, -74.0060, '$$$', 4.6),
(4, 'Bali, Indonesia', 'bali.jpg', 'Tropical paradise with stunning beaches, ancient temples, and lush rice terraces.', 1, -8.3405, 115.0920, '$$', 4.7),
(5, 'London, UK', 'london.jpg', 'Historic city with royal palaces, museums, and a thriving cultural scene.', 0, 51.5074, -0.1278, '$$$', 4.5),
(6, 'Dubai, UAE', 'dubai.jpg', 'Futuristic city with luxury shopping, ultramodern architecture, and desert adventures.', 1, 25.2048, 55.2708, '$$$$', 4.6),
(7, 'Rome, Italy', 'rome.jpg', 'Ancient city with remarkable ruins, Renaissance art, and delicious Italian cuisine.', 0, 41.9028, 12.4964, '$$', 4.8),
(8, 'Sydney, Australia', 'sydney.jpg', 'Harbor city with iconic Opera House, beautiful beaches, and outdoor lifestyle.', 0, -33.8688, 151.2093, '$$$', 4.7),
(9, 'Barcelona, Spain', 'barcelona.jpg', 'Mediterranean city famous for Gaudí architecture, beaches, and vibrant nightlife.', 0, 41.3851, 2.1734, '$$', 4.6),
(10, 'Iceland', 'iceland.jpg', 'Land of fire and ice with stunning natural wonders, Northern Lights, and geothermal spas.', 1, 64.9631, -19.0208, '$$$', 4.9);

-- Update existing destinations with sample ratings
UPDATE destinations SET rating = 4.5 + (RAND() * 0.5), review_count = FLOOR(50 + (RAND() * 200)) WHERE rating = 0.00;

-- Create indexes for better performance
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_destination ON bookings(destination_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_reviews_destination ON reviews(destination_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_wishlist_user ON wishlist(user_id);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
CREATE INDEX idx_activity_user ON activity_log(user_id);
CREATE INDEX idx_destinations_featured ON destinations(featured);
CREATE INDEX idx_destinations_rating ON destinations(rating);