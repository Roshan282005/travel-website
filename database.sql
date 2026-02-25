CREATE DATABASE travel_db;

USE travel_db;

CREATE TABLE destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country VARCHAR(100),
    image VARCHAR(255),
    description TEXT
);

CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE firebase_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(100) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    photo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL
);
