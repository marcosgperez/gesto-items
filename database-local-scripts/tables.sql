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