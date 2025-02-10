CREATE TABLE `Type_rental` (
  `id` integer PRIMARY KEY,
  `label` varchar(255)
);

CREATE TABLE `user` (
  `id` integer PRIMARY KEY,
  `fistname` varchar(255),
  `lastname` varchar(255),
  `email` varchar(255),
  `password` varchar(255),
  `age` datetime,
  `role` varchar(255),
  `username` varchar(255),
  `phone` int(11),
  `address` varchar(255)
);

CREATE TABLE `Reservation` (
  `id` integer PRIMARY KEY,
  `id_rental` int,
  `id_renter` int,
  `date_start` datetime,
  `date_end` datetime,
  `nbr_adult` int,
  `nbr_minor` int,
  `status` varchar(255),
  `checked` int
);

CREATE TABLE `Rate` (
  `id` integer PRIMARY KEY,
  `id_rental` int,
  `label` varchar(255),
  `price` int
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
  `description` textarea,
  `capacity` int,
  `nbr_location` int,
  `id_type_rental` int,
  `isActive` int,
  `equipment` int
);

CREATE TABLE `Equipment` (
  `id` integer PRIMARY KEY,
  `label` varchar(255)
);

ALTER TABLE `Rental` ADD FOREIGN KEY (`id_type_rental`) REFERENCES `Type_rental` (`id`);

ALTER TABLE `Rental` ADD FOREIGN KEY (`id`) REFERENCES `Reservation` (`id_rental`);

CREATE TABLE `Rental_Equipment` (
  `Rental_equipment` int,
  `Equipment_id` integer,
  PRIMARY KEY (`Rental_equipment`, `Equipment_id`)
);

ALTER TABLE `Rental_Equipment` ADD FOREIGN KEY (`Rental_equipment`) REFERENCES `Rental` (`equipment`);

ALTER TABLE `Rental_Equipment` ADD FOREIGN KEY (`Equipment_id`) REFERENCES `Equipment` (`id`);


ALTER TABLE `user` ADD FOREIGN KEY (`id`) REFERENCES `Reservation` (`id_renter`);

ALTER TABLE `Rental` ADD FOREIGN KEY (`id`) REFERENCES `Rate` (`id_rental`);
