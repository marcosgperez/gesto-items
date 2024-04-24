CREATE TABLE floors (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE sectors (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE items (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `qr` VARCHAR(255),
    `photos` VARCHAR(255),
    `brand` VARCHAR(255),
    `code` VARCHAR(255),
    `serial` VARCHAR(255),
    `model` VARCHAR(255),
    `chasis` VARCHAR(255),
    `description` TEXT,
    `manual` VARCHAR(255)
);

CREATE TABLE histories (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP
);

CREATE TABLE events (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP,
    `event_type` VARCHAR(255),
    `start_date` TIMESTAMP,
    `end_date` TIMESTAMP,
    `description` TEXT,
    `photos` VARCHAR(255),
    `observations` TEXT
);

CREATE TABLE event_types (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP,
    `name` VARCHAR(255)
);

CREATE TABLE parts (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `photos` VARCHAR(255),
    `brand` VARCHAR(255),
    `code` VARCHAR(255),
    `serial` VARCHAR(255),
    `model` VARCHAR(255),
    `description` TEXT
);

ALTER TABLE sectors
ADD COLUMN floor_id int UNSIGNED,
ADD FOREIGN KEY (floor_id) REFERENCES floors(id);

ALTER TABLE histories
ADD COLUMN item_id int,
ADD FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE events
ADD COLUMN history_id int,
ADD FOREIGN KEY (history_id) REFERENCES histories(id);

ALTER TABLE events
ADD COLUMN item_id int,
ADD FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE parts
ADD COLUMN item_id int,
ADD FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE items
ADD COLUMN sector_id int UNSIGNED,
ADD FOREIGN KEY (sector_id) REFERENCES sectors(id);

ALTER TABLE items
ADD COLUMN floor_id int UNSIGNED,
ADD FOREIGN KEY (floor_id) REFERENCES floors(id);

INSERT INTO event_types (name) VALUES
('CARGA INICIAL'),
('MODIFICACION'),
('MEDICION'),
('MANTENIMIENTO'),
('ROTURA'),
('REPARACION');

CREATE TABLE users (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `surname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `user_type_id` int UNSIGNED NOT NULL,
    `profile_initials` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `profile_color` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE user_types (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE clients (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `instance` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE conversations ( 
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `chat_open_timestamp` TIMESTAMP,
    `chat_close_timestamp` TIMESTAMP,
    `bot_mode` int UNSIGNED NOT NULL, 
    `client_id` int UNSIGNED NOT NULL
);

ALTER TABLE conversations
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE items ADD COLUMN
status int UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE items ADD COLUMN
scheduled_maintenance int UNSIGNED DEFAULT null;

CREATE TABLE fifo_messages (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `message` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `instance` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `send_timestamp` TIMESTAMP,
);

ALTER TABLE fifo_messages
ADD FOREIGN KEY (client_id) REFERENCES clients(id);


ALTER TABLE fifo_messages 
ADD COLUMN errors longtext;

ALTER TABLE items 
ADD COLUMN phones_to_remind longtext
ADD COLUMN text_to_send longtext
ADD COLUMN last_reminder TIMESTAMP
ADD COLUMN reminder_interval int UNSIGNED;

ALTER TABLE event_types 
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE event_types
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE floors
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE floors
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE items
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE items
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE sectors
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE sectors
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE user_types
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE user_types
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE users
ADD COLUMN client_id int UNSIGNED;

ALTER TABLE users
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

CREATE TABLE locations (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `address` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `client_id` int UNSIGNED
);

ALTER TABLE locations
ADD FOREIGN KEY (client_id) REFERENCES clients(id);

ALTER TABLE items
ADD COLUMN location_id int UNSIGNED DEFAULT null;

ALTER TABLE items
ADD FOREIGN KEY (location_id) REFERENCES locations(id);

ALTER TABLE events 
DROP COLUMN end_date;

ALTER TABLE items 
ADD COLUMN image_url varchar(255);

CREATE TABLE checks (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `image_url` varchar(255),
    `amount` int UNSIGNED,
    `payment_date` TIMESTAMP,
    `from` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `to` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `status` int UNSIGNED NOT NULL DEFAULT 1,
    `instance` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
);

CREATE TABLE whatsapp_phones (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `phone_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `client_id` int UNSIGNED
);

ALTER TABLE whatsapp_phones
ADD FOREIGN KEY (client_id) REFERENCES clients(id);