CREATE TABLE `Type_rental` (
  `id` integer PRIMARY KEY,
  `label` varchar(255)
);

CREATE TABLE `User` (
  `id` integer PRIMARY KEY,
  `firstname` varchar(255),
  `lastname` varchar(255),
  `email` varchar(255),
  `password` varchar(255),
  `date_of_birth` datetime,
  `role` varchar(255),
  `username` varchar(255),
  `phone` varchar(15),
  `address` varchar(255)
);

CREATE TABLE `Reservation` (
  `id` integer PRIMARY KEY,
  `rental_id` int NOT NULL,
  `renter_id` int NOT NULL,
  `date_start` datetime,
  `date_end` datetime,
  `nbr_adult` int,
  `nbr_minor` int,
  `status` varchar(255),
  `checked` int,
  `applied_price_total` int
);

CREATE TABLE `Price` (
  `id` integer PRIMARY KEY,
  `rental_id` int NOT NULL,
  `season_id` int NOT NULL,
  `price_per_night` int
);

CREATE TABLE `Season` (
  `id` integer PRIMARY KEY,
  `label` varchar(255),
  `season_start` datetime,
  `season_end` datetime
);

CREATE TABLE `Rental` (
  `id` integer PRIMARY KEY,
  `title` varchar(255),
  `description` text,
  `capacity` int,
  `nbr_localization` int,
  `type_rental_id` int NOT NULL,
  `isActive` int,
  `image` varchar(255)
);

CREATE TABLE `Equipment` (
  `id` integer PRIMARY KEY,
  `label` varchar(255)
);

CREATE TABLE `rental_equipment` (
  `rental_id` int NOT NULL,
  `equipment_id` int NOT NULL
);

ALTER TABLE `Rental` ADD FOREIGN KEY (`type_rental_id`) REFERENCES `Type_rental` (`id`);

ALTER TABLE `Rental` ADD FOREIGN KEY (`id`) REFERENCES `Reservation` (`rental_id`);

ALTER TABLE `User` ADD FOREIGN KEY (`id`) REFERENCES `Reservation` (`renter_id`);

ALTER TABLE `Rental` ADD FOREIGN KEY (`id`) REFERENCES `Price` (`rental_id`);

ALTER TABLE `Season` ADD FOREIGN KEY (`id`) REFERENCES `Price` (`season_id`);

ALTER TABLE `rental_equipment` ADD FOREIGN KEY (`equipment_id`) REFERENCES `Equipment` (`id`);

ALTER TABLE `rental_equipment` ADD FOREIGN KEY (`rental_id`) REFERENCES `Rental` (`id`);
