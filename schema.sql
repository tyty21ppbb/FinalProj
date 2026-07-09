CREATE DATABASE IF NOT EXISTS chairhive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chairhive;

CREATE TABLE users (
    id             INT(11)       NOT NULL AUTO_INCREMENT,
    full_name      VARCHAR(150)  NOT NULL,
    email          VARCHAR(150)  NOT NULL UNIQUE,
    password_hash  VARCHAR(255)  NOT NULL,
    address        VARCHAR(255)  DEFAULT NULL,
    contact_number VARCHAR(50)   DEFAULT NULL,
    role           ENUM('buyer','admin') NOT NULL DEFAULT 'buyer',
    is_verified    TINYINT(1)    NOT NULL DEFAULT 0,
    verify_token   VARCHAR(64)   DEFAULT NULL,
    is_active      TINYINT(1)    NOT NULL DEFAULT 1,
    date_created   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id           INT(11)       NOT NULL AUTO_INCREMENT,
    name         VARCHAR(150)  NOT NULL,
    category     VARCHAR(100)  NOT NULL,
    description  VARCHAR(500)  DEFAULT NULL,
    price        DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_qty    INT(11)       NOT NULL DEFAULT 0,
    is_active    TINYINT(1)    NOT NULL DEFAULT 1,
    date_created TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    id                INT(11)       NOT NULL AUTO_INCREMENT,
    user_id           INT(11)       NOT NULL,
    shipping_address  VARCHAR(255)  NOT NULL,
    contact_number    VARCHAR(50)   NOT NULL,
    payment_method    VARCHAR(50)   NOT NULL,
    payment_reference VARCHAR(100)  DEFAULT NULL,
    total_amount      DECIMAL(10,2) NOT NULL,
    status            VARCHAR(30)   NOT NULL DEFAULT 'Paid (Simulated)',
    date_created      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
    id           INT(11)       NOT NULL AUTO_INCREMENT,
    order_id     INT(11)       NOT NULL,
    product_id   INT(11)       NOT NULL,
    product_name VARCHAR(150)  NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    quantity     INT(11)       NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (order_id)  REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_log (
    id           INT(11)      NOT NULL AUTO_INCREMENT,
    user_id      INT(11)      DEFAULT NULL,
    actor_name   VARCHAR(150) NOT NULL,
    actor_role   VARCHAR(20)  NOT NULL,
    action       VARCHAR(100) NOT NULL,
    description  VARCHAR(255) NOT NULL,
    date_created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO users (full_name, email, password_hash, address, contact_number, role, is_verified, is_active)
VALUES ('System Administrator','admin@chairhive.test',
        '$2b$04$WXt7lZsc8hCdpftGnTiz3uahe1xLgn3JwYVKMr9G/JIhOatP.iLRa',
        'ChairHive Head Office, Quezon City','09170000000','admin',1,1);


INSERT INTO products (name, category, description, price, stock_qty) VALUES
('Ergonomic Mesh Chair',        'Ergonomic Chairs',         'Breathable mesh back with adjustable lumbar support and armrests.',            4250.00, 25),
('ErgoFlex Pro Chair',          'Ergonomic Chairs',         'Synchro-tilt mechanism with adjustable headrest and seat depth.',              6800.00, 14),
('Posture-Support Mesh Chair',  'Ergonomic Chairs',         'Designed for all-day comfort with breathable mesh and contoured seat.',        3950.00, 20),
('Executive Leather Chair',     'Executive Chairs',         'Premium PU leather chair with padded headrest and recline function.',          7990.00, 12),
('Boardroom High-Back Chair',   'Executive Chairs',         'High-back leather chair built for conference rooms and private offices.',       9500.00,  8),
('Classic Manager Chair',       'Executive Chairs',         'Bonded leather chair with chrome base and tilt-lock mechanism.',               5600.00, 15),
('RGB Gaming Chair',            'Gaming Chairs',            'Racing-style chair with adjustable armrests and built-in lumbar pillow.',       6200.00, 18),
('ProGamer Racing Chair',       'Gaming Chairs',            'Reinforced steel frame with 180-degree recline and footrest.',                  7400.00, 10),
('Compact Gaming Chair',        'Gaming Chairs',            'Space-saving gaming chair with PU leather finish and headrest pillow.',         4800.00, 22),
('Visitor Chair',               'Visitor & Guest Chairs',   'Stackable guest chair with a powder-coated steel frame.',                      1850.00, 40),
('Padded Reception Chair',      'Visitor & Guest Chairs',   'Cushioned waiting-area chair with armrests, sold individually.',               2300.00, 30),
('Link Bench Chair (3-Seater)', 'Visitor & Guest Chairs',   'Connected 3-seat bench for waiting rooms and lobbies.',                        5400.00,  6),
('Adjustable Task Stool',       'Stools & Drafting Chairs', 'Height-adjustable stool with swivel base, great for standing desks.',          2300.00, 18),
('Drafting Chair with Footring','Stools & Drafting Chairs', 'Tall gas-lift stool with footring, built for standing-height desks.',           3300.00, 11),
('Backless Lab Stool',          'Stools & Drafting Chairs', 'Simple round-seat stool for labs, counters, and workshops.',                   1450.00, 26);
