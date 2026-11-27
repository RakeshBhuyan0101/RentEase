-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: rentease_db
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `move_in_date` date NOT NULL,
  `lease_months` int(11) DEFAULT 11,
  `tenant_message` text DEFAULT NULL,
  `booking_status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`booking_id`),
  KEY `fk_booking_property` (`property_id`),
  KEY `fk_booking_tenant` (`tenant_id`),
  CONSTRAINT `fk_booking_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (5,1,12,'2026-12-15',11,'Looking to move with spouse. Can pay 6 months advance deposit upfront.','pending','2026-11-27 09:21:37','2026-11-27 09:21:37'),(6,2,13,'2026-12-01',12,'Need a quiet place to work from home.','pending','2026-11-27 09:21:37','2026-11-27 09:21:37'),(7,3,15,'2027-01-01',11,'Transferring to Bengaluru office near Indiranagar metro station.','approved','2026-11-27 09:21:37','2026-11-27 09:21:37'),(8,4,14,'2026-12-10',24,'Senior software engineer at Google moving with family.','pending','2026-11-27 09:21:37','2026-11-27 09:21:37'),(9,5,3,'2026-11-20',11,'Shifting closer to corporate park for easier commute.','approved','2026-11-27 09:21:37','2026-11-27 09:21:37'),(10,9,3,'2026-11-28',11,'','pending','2026-11-27 14:56:10','2026-11-27 14:56:10');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `city` varchar(80) NOT NULL,
  `locality` varchar(100) NOT NULL,
  `full_address` text NOT NULL,
  `bhk_type` enum('1RK','1BHK','2BHK','3BHK','4BHK+') NOT NULL,
  `furnishing` enum('Unfurnished','Semi-Furnished','Fully-Furnished') DEFAULT 'Semi-Furnished',
  `monthly_rent` decimal(10,2) NOT NULL,
  `security_deposit` decimal(10,2) NOT NULL,
  `amenities` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT 'default-apt.jpg',
  `availability_status` enum('available','booked') NOT NULL DEFAULT 'available',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`property_id`),
  KEY `fk_property_owner` (`owner_id`),
  CONSTRAINT `fk_property_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
INSERT INTO `properties` VALUES (1,9,'Palm Meadows Villa 4BHK','Well-ventilated apartment with 24/7 water and balcony.','Bengaluru','Whitefield','Villa 42, Palm Meadows Phase 1, Varthur Main Rd, Whitefield, Bengaluru','4BHK+','Fully-Furnished',95000.00,500000.00,'WiFi, Swimming Pool, Parking, Private Garden, AC','default-apt.jpg','available','approved','2026-11-16 14:19:05'),(2,9,'Salarpuria Sattva 2BHK','Ideal for working professionals and students.','Bengaluru','Bellandur','Tower 4, Flat 802, Salarpuria Sattva, Outer Ring Rd, Bellandur, Bengaluru','2BHK','Semi-Furnished',42000.00,200000.00,'WiFi, Gym, Parking, Lift, 24/7 Security','default-apt.jpg','available','approved','2026-11-16 14:19:05'),(3,9,'Indiranagar Loft Studio','Gated society with park and gym access.','Bengaluru','Indiranagar','12th Main Road, 100ft Road Cross, Indiranagar, Bengaluru','1RK','Unfurnished',27000.00,100000.00,'Balcony, Water Filter, Lift','default-apt.jpg','available','pending','2026-11-16 14:19:05'),(4,10,'Prestige High Fields 3BHK','Luxury high-rise apartment with panoramic skyline views and modern clubhouse.','Hyderabad','Gachibowli','Tower 2, Flat 1404, Prestige High Fields, Financial District, Gachibowli, Hyderabad','3BHK','Fully-Furnished',55000.00,150000.00,'Clubhouse, Gym, Tennis Court, Power Backup, Covered Parking','default-apt.jpg','available','approved','2026-11-27 09:21:25'),(5,10,'Hiranandani Estate 2BHK','Serene township apartment surrounded by lush greenery and premium lifestyle amenities.','Mumbai','Thane West','Rodas Enclave, Hiranandani Estate, Ghodbunder Rd, Thane West, Mumbai','2BHK','Semi-Furnished',38000.00,120000.00,'Modular Kitchen, 24/7 Security, Piped Gas, Garden, Children Play Area','default-apt.jpg','available','approved','2026-11-27 09:21:25'),(6,11,'DLF Phase 5 Sky Villa','Ultra-luxury penthouse facing golf course with private elevator and personal jacuzzi.','Gurugram','DLF Phase 5','The Camellias, Golf Course Road, DLF Phase 5, Gurugram','4BHK+','Fully-Furnished',150000.00,600000.00,'Jacuzzi, Golf Course View, Private Elevator, Concierge, Heated Pool','default-apt.jpg','available','approved','2026-11-27 09:21:25'),(7,11,'Koregaon Park Garden Flat','Cozy ground floor flat with private courtyard in prime cafe district.','Pune','Koregaon Park','Lane 7, Near German Bakery, Koregaon Park, Pune','2BHK','Semi-Furnished',32000.00,90000.00,'Pet Friendly, Parking, Solar Water, CCTV','default-apt.jpg','available','pending','2026-11-27 09:21:25'),(8,2,'Bandra West Sea View Flat','Spacious apartment with sweeping views of the Arabian Sea.','Mumbai','Bandra West','Carter Road Promenade, Bandra West, Mumbai','3BHK','Fully-Furnished',110000.00,400000.00,'Sea View, Covered Parking, Modern Interiors, AC','default-apt.jpg','available','rejected','2026-11-27 09:21:25'),(9,9,'patia house updated','full villa with balcony view and luxury garden','bhubaneswar','Patia','Near DLF Cybercity, Patia, Bhubaneswar','3BHK','Fully-Furnished',11978.00,250000.00,'WiFi, Parking, AC, Power Backup','prop_6b099538e93b6.png','available','approved','2026-11-27 14:35:36'),(10,9,'Greenwood High Residency 3BHK','Sunlit modern apartment next to international schools, premium clubhouse and jogging track.','Bengaluru','Sarjapur Road','Flat 604, Tower C, Greenwood High Residency, Sarjapur Road, Bengaluru','3BHK','Fully-Furnished',48000.00,150000.00,'Gym, Swimming Pool, 24/7 Security, Covered Parking, Power Backup','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(11,9,'Khandagiri Sunrise Apartment 2BHK','Peaceful family home with wide balconies, continuous water supply and temple view.','Bhubaneswar','Khandagiri','Plot 112, Near Khandagiri Caves, NH16, Bhubaneswar','2BHK','Semi-Furnished',18000.00,40000.00,'Lift, Power Backup, 24/7 Water, Covered Parking','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(12,10,'Banjara Hills Royal Suite 4BHK','Opulent executive residence with private elevator foyer, Italian marble and home theatre.','Hyderabad','Banjara Hills','Road No. 12, Near MLA Colony, Banjara Hills, Hyderabad','4BHK+','Fully-Furnished',120000.00,400000.00,'Private Elevator, Home Theatre, Terrace Garden, Valet, Heated Pool','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(13,10,'Jubilee Hills Terrace 1BHK','Chic boutique studio apartment ideal for media and corporate professionals.','Hyderabad','Jubilee Hills','Road No. 36, Near Metro Station, Jubilee Hills, Hyderabad','1BHK','Fully-Furnished',24000.00,60000.00,'WiFi, AC, Modular Kitchen, Rooftop Lounge, Security','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(14,11,'Viman Nagar Executive Flat 2BHK','Contemporary high-rise flat walking distance from Phoenix Marketcity and airport.','Pune','Viman Nagar','Clover Park, Road 4, Viman Nagar, Pune','2BHK','Semi-Furnished',35000.00,100000.00,'Gym, Parking, CCTV, Gas Pipeline, Clubhouse','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(15,11,'Cyber Hub City Studio 1RK','Smart plug-and-play studio for young professionals, 5 min walk from DLF Cyber Hub.','Gurugram','Cyber City','Tower 8A, DLF Phase 2, Near Cyber City, Gurugram','1RK','Fully-Furnished',22000.00,50000.00,'WiFi, Power Backup, Housekeeping, AC, Security','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(16,11,'Golf Course Extn Luxury 3BHK','Premium condominium facing the Aravalli hills with world-class sporting amenities.','Gurugram','Sector 65','M3M Golfestate, Golf Course Extension Road, Sector 65, Gurugram','3BHK','Semi-Furnished',62000.00,180000.00,'Clubhouse, Tennis Court, Park, Covered Parking, Gym','default-apt.jpg','available','pending','2026-11-27 15:01:59'),(17,2,'Powai Lake View Apartment 2BHK','Stunning direct views of Powai Lake with European fittings and Olympic swimming pool.','Mumbai','Powai','Hiranandani Gardens, Central Avenue, Powai, Mumbai','2BHK','Fully-Furnished',65000.00,200000.00,'Lake View, Gym, Swimming Pool, 24/7 Security, Lift','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(18,2,'South Ex Designer Studio 1BHK','Centrally located South Delhi apartment close to Ring Road and AIIMS.','Delhi','South Extension','Part 2, Block G, South Extension, New Delhi','1BHK','Fully-Furnished',29000.00,70000.00,'Metro Proximity, AC, Lift, Modular Kitchen, Water Storage','default-apt.jpg','available','approved','2026-11-27 15:01:59'),(19,2,'Vasant Kunj Forest Villa 4BHK','Secluded independent villa adjoining the green ridge forest with private lawn.','Delhi','Vasant Kunj','Sector C, Pocket 8, Vasant Kunj, New Delhi','4BHK+','Unfurnished',85000.00,250000.00,'Garden, Servant Quarter, Gated Society, 3 Parking Slots','default-apt.jpg','available','pending','2026-11-27 15:01:59');
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `COMMENT` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  KEY `fk_review_property` (`property_id`),
  KEY `fk_review_tenant` (`tenant_id`),
  CONSTRAINT `fk_review_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('tenant','owner','admin') NOT NULL DEFAULT 'tenant',
  `STATUS` enum('active','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','admin@rentease.in','$2y$10$nqgY5gr3EnDv89z2iWVokugOXtGa7M8TzJknqS.4YrXd5gauU3J9e','9800000001','admin','active','2026-11-16 14:19:05'),(2,'Rajesh Sharma','owner1@rentease.in','$2y$10$bwGKwv4Bzzl9lHYcgy0EX.6ysxqF85kgQ.4EfaOC0qBynvtinSLT6','9811111111','owner','active','2026-11-16 14:19:05'),(3,'Priya Verma','tenant1@rentease.in','$2y$10$V.shyNtiTsXhyahcNLY9gexlM7oLf.PHmUSfnKHywtPKnV4nFrBhO','9822222222','tenant','active','2026-11-16 14:19:05'),(6,'Test User','test1795195401@example.com','$2y$10$C/9QbQEHMrBnnGTwRqQDreMefndtwN3aXK2TOneRsgz.HPFAhPUB.','9800000000','tenant','active','2026-11-20 17:23:21'),(8,'Test User','check1795200126@gmail.com','$2y$10$C/9QbQEHMrBnnGTwRqQDreMefndtwN3aXK2TOneRsgz.HPFAhPUB.','9800000000','tenant','active','2026-11-20 18:42:07'),(9,'Rakesh Bhuyan','rakeshbhuyan0101@gmail.com','$2y$10$ZhbwPJZyMs/PLTv2cm8.8.6lNSustkR73dx5tfLA6j.H/GF/ONuxa','8249322344','owner','active','2026-11-22 17:21:56'),(10,'Kavita Mehta','kavita.mehta@gmail.com','$2y$10$khSHX4l4j3vIcRP9vaiMiOu2yaT6trwaQlmPWBcs0QF.de3f7PXzm','+91 98334 45566','owner','active','2026-11-27 09:21:25'),(11,'Arun Patel','arun.patel@gmail.com','$2y$10$khSHX4l4j3vIcRP9vaiMiOu2yaT6trwaQlmPWBcs0QF.de3f7PXzm','+91 98445 56677','owner','active','2026-11-27 09:21:25'),(12,'Ananya Deshmukh','ananya.deshmukh@gmail.com','$2y$10$J2O6PlYjJWqYSRyAMJ6xNunUalVDS8roT3tBxBaIRfquPPLkV7TOG','+91 99120 44512','tenant','active','2026-11-27 09:21:25'),(13,'Vikramaditya Kulkarni','vikramaditya.k@gmail.com','$2y$10$J2O6PlYjJWqYSRyAMJ6xNunUalVDS8roT3tBxBaIRfquPPLkV7TOG','+91 98450 11873','tenant','active','2026-11-27 09:21:25'),(14,'Rohit Sharma','rohit.sharma@gmail.com','$2y$10$J2O6PlYjJWqYSRyAMJ6xNunUalVDS8roT3tBxBaIRfquPPLkV7TOG','+91 98765 43210','tenant','active','2026-11-27 09:21:25'),(15,'Sneha Reddy','sneha.reddy@gmail.com','$2y$10$J2O6PlYjJWqYSRyAMJ6xNunUalVDS8roT3tBxBaIRfquPPLkV7TOG','+91 91234 56789','tenant','active','2026-11-27 09:21:25');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-27 20:34:41
