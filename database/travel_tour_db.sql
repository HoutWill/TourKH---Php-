-- Travel Tour Database Dump
-- Generated on: 2026-06-08 09:29:39

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default_avatar.jpg',
  `nationality` varchar(50) DEFAULT NULL,
  `bio` text,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`user_id`, `username`, `full_name`, `email`, `phone`, `profile_image`, `nationality`, `bio`, `password`, `role`, `status`, `created_at`) VALUES
('1', 'admin', 'John Doe', 'john@example.com', '+855 12 345 678', 'default_avatar.jpg', 'Cambodian', 'Adventure seeker and culture lover. Excited to explore new places!', '$2y$10$pTCD.1m3h.M8H5Qo2RJaq.GNsCVNesgRIvfDk5EKA6UZ1Llmfiw3q', 'admin', 'active', '2026-06-02 19:56:26'),
('2', 'hout', 'serhout', 'chhivserhout@gmail.com', '+855 1669 977 4', 'serhout.jpg', '', '', '$2y$10$zIxSZESuPIzSND2UM/NrfOLu7sZNClRcJESVgdISADrizMfMOXCb2', 'user', 'active', '2026-06-02 19:56:26');

-- --------------------------------------------------------
-- Table structure for table `tours`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tours`;
CREATE TABLE `tours` (
  `tour_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(100) DEFAULT 'Cambodia',
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `short_description` text,
  `full_description` longtext,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tour_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `tours`
INSERT INTO `tours` (`tour_id`, `title`, `location`, `price`, `duration`, `image`, `short_description`, `full_description`, `status`, `created_at`) VALUES
('1', 'Angkor Wat Sunrise Experience', 'Siem Reap', '99.00', '2 Days 1 Night', 'angkor.jpg', 'Watch the sunrise over Angkor Wat and explore ancient temples.', 'OVERVIEW:\r\nExperience Cambodia’s most iconic sunrise at Angkor Wat.\r\n\r\nHIGHLIGHTS:\r\n- Angkor Wat Sunrise\r\n- Bayon Temple\r\n- Ta Prohm Jungle Temple\r\n\r\nINCLUDED:\r\n- Transportation\r\n- Local Guide\r\n- Drinking Water\r\n\r\nEXCLUDED:\r\n- Personal expenses\r\n- Travel insurance\r\n\r\nITINERARY:\r\nDay 1: Arrival & sunset visit\r\nDay 2: Sunrise tour & temple exploration', 'active', '2026-06-02 19:56:26'),
('2', 'Koh Rong Island Escape', 'Sihanoukville', '149.00', '3 Days 2 Nights', 'kohrong.jpg', 'Relax on white sand beaches and crystal clear water.', 'OVERVIEW:\r\nEscape to Cambodia’s paradise island.\r\n\r\nHIGHLIGHTS:\r\n- Beach relaxation\r\n- Snorkeling\r\n- Island hopping\r\n\r\nINCLUDED:\r\n- Boat transfer\r\n- Hotel stay\r\n- Breakfast\r\n\r\nEXCLUDED:\r\n- Drinks\r\n- Personal expenses\r\n\r\nITINERARY:\r\nDay 1: Travel & check-in\r\nDay 2: Island activities\r\nDay 3: Return', 'active', '2026-06-02 19:56:26'),
('3', 'Phnom Penh City Discovery', 'Phnom Penh', '59.00', '1 Day', 'phnompenh.jpg', 'Explore Cambodia’s capital city highlights.', 'OVERVIEW:\r\nDiscover culture and history in Phnom Penh.\r\n\r\nHIGHLIGHTS:\r\n- Royal Palace\r\n- National Museum\r\n- Riverfront walk\r\n\r\nINCLUDED:\r\n- Transport\r\n- Guide\r\n\r\nEXCLUDED:\r\n- Entry tickets\r\n- Food\r\n\r\nITINERARY:\r\nMorning: Royal Palace\r\nAfternoon: Museum & riverside', 'active', '2026-06-02 19:56:26'),
('4', 'Bokor Mountain Misty Escape', 'Kampot', '89.00', '1 Day', 'bokor.jpg', 'Mysterious foggy mountain with French colonial ruins and scenic views.', 'OVERVIEW:\r\nExplore the famous Bokor Mountain known for its mist, cool weather, and abandoned French buildings.\r\n\r\nHIGHLIGHTS:\r\n- Bokor Hill Station\r\n- Old Casino ruins\r\n- Giant Buddha statue\r\n- Ocean viewpoint\r\n\r\nINCLUDED:\r\n- Transport from Kampot/Phnom Penh\r\n- Local guide\r\n\r\nEXCLUDED:\r\n- Food & drinks\r\n- Entry fees\r\n\r\nITINERARY:\r\nMorning: Departure and ascent to Bokor Mountain\r\nAfternoon: Explore ruins and viewpoints\r\nEvening: Return', 'active', '2026-06-02 19:58:58'),
('5', 'Kampot River Sunset Cruise', 'Kampot', '45.00', 'Half Day', 'kampot_river.jpg', 'Relaxing river cruise with sunset views and mountain scenery.', 'OVERVIEW:\r\nA peaceful boat trip along Kampot River during sunset.\r\n\r\nHIGHLIGHTS:\r\n- Sunset river cruise\r\n- Mountain background views\r\n- Local fishing villages\r\n- Chill atmosphere\r\n\r\nINCLUDED:\r\n- Boat ride\r\n- Guide\r\n\r\nEXCLUDED:\r\n- Food & drinks\r\n\r\nITINERARY:\r\nEvening: Departure and river cruise\r\nSunset: Photo stops and return', 'active', '2026-06-02 19:58:58'),
('6', 'Sambor Prei Kuk Ancient Temple Tour', 'Kampong Thom', '79.00', '1 Day', 'sambor_preikuk.jpg', 'Explore pre-Angkorian temples surrounded by jungle.', 'OVERVIEW:\r\nVisit one of Cambodia’s oldest temple complexes.\r\n\r\nHIGHLIGHTS:\r\n- Ancient brick temples\r\n- Jungle exploration\r\n- UNESCO heritage site\r\n- Historical architecture\r\n\r\nINCLUDED:\r\n- Transport\r\n- Guide\r\n\r\nEXCLUDED:\r\n- Entry ticket\r\n- Food\r\n\r\nITINERARY:\r\nMorning: Departure to Kampong Thom\r\nAfternoon: Temple exploration and return', 'active', '2026-06-02 19:58:58'),
('7', 'Cardamom Rainforest Expedition', 'Koh Kong', '135.00', '2 Days 1 Night', 'cardamom.jpg', 'Wild rainforest trekking with rare wildlife spotting.', 'OVERVIEW:\r\nA deep jungle survival-style eco adventure.\r\n\r\nHIGHLIGHTS:\r\n- Rainforest trekking\r\n- Wildlife spotting\r\n- Waterfalls\r\n- Camping experience\r\n\r\nINCLUDED:\r\n- Guide\r\n- Camping gear\r\n- Meals\r\n\r\nEXCLUDED:\r\n- Personal equipment\r\n- Drinks\r\n\r\nITINERARY:\r\nDay 1: Jungle trek and camp setup\r\nDay 2: Morning hike and return', 'active', '2026-06-02 19:58:58'),
('8', 'Siem Reap Countryside Cycling Tour', 'Siem Reap', '55.00', 'Half Day', 'siemreap_cycle.jpg', 'Cycle through rice fields and rural Cambodian villages.', 'OVERVIEW:\r\nA relaxing countryside cycling experience around Siem Reap.\r\n\r\nHIGHLIGHTS:\r\n- Rice fields\r\n- Local village life\r\n- Ox-cart paths\r\n- Traditional houses\r\n\r\nINCLUDED:\r\n- Bicycle rental\r\n- Guide\r\n- Water\r\n\r\nEXCLUDED:\r\n- Food\r\n- Personal expenses\r\n\r\nITINERARY:\r\nMorning or afternoon cycling loop through countryside', 'active', '2026-06-02 19:58:58'),
('9', 'Kep Seafood & Kampot Pepper Trail', 'Kep & Kampot', '69.00', '2 Days', 'kampot_river.jpg', 'Taste fresh crab at Kep Crab Market and explore the famous organic Kampot pepper farms.', 'Embark on a culinary adventure down south. Day 1 starts with a scenic drive to Kampot, visiting a historic organic black pepper farm to learn why Kampot pepper is prized by top chefs worldwide. Have a peaceful sunset cruise along the Kampot River. Day 2 takes you to the Kep Crab Market to savor freshly cooked blue crab with green pepper. Finish with a relaxing swim at Kep beach.', 'active', '2026-06-06 00:14:10'),
('10', 'Siem Reap Temple Trails by Bicycle', 'Siem Reap', '39.00', '1 Day', 'angkor.jpg', 'Cycle along hidden dirt paths around Angkor Archaeological Park and discover ruined temples.', 'Escape the main tour buses and explore the ancient empire on two wheels. Guided by a local historical expert, you will ride through shade-dappled dirt trails, climbing ancient walls, and discovering lesser-known temples like Preah Khan, Ta Nei, and the majestic Bayon temple. Includes a delicious traditional Khmer lunch at a local village.', 'active', '2026-06-06 00:14:10'),
('11', 'Koh Rong Paradise Beach Escape', 'Koh Rong Island', '149.00', '3 Days', 'kohrong.jpg', 'Relax on the white sand beaches of Koh Rong and swim with glowing bioluminescent plankton.', 'Unwind on the pristine white sands of Saracen Bay or Long Beach. Day 1 includes speed ferry transfer from Sihanoukville and checking into your beachside bungalow. Day 2 is a boat excursion for snorkeling, coral reef viewing, and fishing, ending with a magical night swim among glowing bioluminescent plankton. Day 3 is yours for paddleboarding, kayaking, or relaxing before departure.', 'active', '2026-06-06 00:14:10'),
('12', 'Virachey National Park ( Veal Thom )', 'Cambodia', '279.00', '4 Days 3 Nights', 'vireakchey.jpg', '3 days. 3 nights. An unforgettable journey into the wild heart of Ratanakiri.\r\nVeal Thom is a vast natural grassland covering over 1,030 hectares. In the rainy season, it turns green and light blue — and in the dry season, it glows golden. According to Grogoty McCanon, it\'s one of the few places in Southeast Asia that compares in beauty across Cambodia, Vietnam, Laos, Thailand, and Malaysia. It’s located in Kat Pang Commune (Vorn Sai District) and Ta Veng Krom Commune (Ta Veng District), within Virachey National Park.', 'About This Tour\r\n3 days. 3 nights. An unforgettable journey into the wild heart of Ratanakiri.\r\nVeal Thom is a vast natural grassland covering over 1,030 hectares. In the rainy season, it turns green and light blue — and in the dry season, it glows golden. According to Grogoty McCanon, it\'s one of the few places in Southeast Asia that compares in beauty across Cambodia, Vietnam, Laos, Thailand, and Malaysia. It’s located in Kat Pang Commune (Vorn Sai District) and Ta Veng Krom Commune (Ta Veng District), within Virachey National Park.\r\n\r\n🥾 Trekking & camping\r\n🌾 Explore scenic grass meadows\r\n🐾 Wildlife spotting\r\n🍲 Try unique local food & culture\r\n🏍️ Motorbike rides up hills\r\n💧 Visit rivers and waterfalls\r\n🧑🏽‍🌾 Meet Indigenous Brao people\r\n🌌 Stargazing (clear sky only)\r\n📍 Trip Itinerary \r\n🗓️ Day 1: Phnom Penh ➜  Ratanakiri(Overnight_Night Bus as options) \r\n🕟7:30 pm Leaving from Ratanakiri (night bus)\r\n🗓️ Day 2: Village ➜ Veal Thom Campsite\r\n🕗 8:00 am Arriving at the village and have breakfast (Not included)\r\n🎒 8:30 am Pack your lunch, then depart by motorbike to Veal Thom\r\n🍱 12:00 pm Stop for lunch along the way, then continue to the campsite\r\n⛺ 3:00 pm Arriving at Veal Thom Campsite and set up tents\r\n🌄 5:00 pm Explore the hills and landscapes at Veal Thom, enjoy sunset time\r\n🔥 7:00 pm Dinner gathering and sharing session with the Brao people\r\n🗓️ Day 3: Trekking Around Veal Thom\r\n🌅 5:00 am Wake up early for sunrise, with tea and instant coffee\r\n🥾 7:00 am Start trekking around Veal Thom (about 2 hours)\r\n🍽️ 10:00 am Arrive at the campsite and enjoy lunch served by local guides. Traditional local food cooked in bamboo pipes\r\n😌 12:00 pm Rest at the campsite. Free time at your own leisure\r\n🌾 5:00 pm Visit the grass field and enjoy the sunset view\r\n🎲 7:00 pm Dinner gathering and sharing session with the tour leader and games\r\n🗓️ Day 4: Campsite ➜ Ratanakiri ➜ Phnom Penh\r\n🌅 6:00 am Get up early for breakfast with tea and instant coffee. Enjoy the sunrise view\r\n🎒 7:30 am Pack your lunch box and leave for the village\r\n🍱 12:00 pm Have lunch on the way, then continue to the village\r\n🏡 1:00 pm Arrive at the village and prepare your belongings for Banlung town\r\n🌊 1:30 pm Visit Yeak Lom Lake and city tour\r\n🚌 8:00 pm Take the night bus back to Phnom Penh (expected arrival around 5:00 am)\r\n✅ Service Included\r\n🍳 2 Breakfasts + 3 Lunches + 2 Dinners (7 meals total)\r\n🧭 Tour leader & local guide\r\n🚌 Night bus + motorbike: $80 value\r\n⛺ 1 tent for 2 pax/ guesthouse at town \r\n🛏️ Sleeping pad\r\n🎟️ Cambodian entrance fees ($15)\r\n🧰 First-aid kit\r\n☕ Tea & instant coffee\r\n💧 Refilled drinking water\r\n❌ Service Excluded\r\n🌍 Foreigner entrance fee: $15 extra\r\n🍽️ Breakfast on Day 2 & Dinner on Day 1 & Day 4\r\n💤 Pillow, sleeping bag, personal hiking gear\r\n🛡️ Travel insurance\r\n🥤 Snacks, soft drinks, alcohol\r\n💸 Personal expenses\r\nNote:\r\n\r\n***Booking Policies & Confirmation:\r\n\r\n-Your booking will be confirmed within 1 week before the departure dates on the booking platform.\r\n\r\n-A full itinerary and list of services will be provided once your booking is confirmed.\r\n\r\n-Your 100% payment is required to reserve your seat upon the booking being confirmed\r\n\r\n-If you would prefer a private tent it will be extra charged 15$/tent.\r\n\r\n-Our teamwork keeps the right to change the program according to any weather issue.\r\n\r\n-Maximum register 15 Pax (small group) if it is lower than 10 Pax our team has the right to change the departure date within prior informed.\r\n\r\n*Child P​olicies:\r\n\r\n12+ years: Full price6–11 years: 50% discountUnder 6 years: Free of charge or 100% discount (must share tent with parents)\r\n12+ years: Full price\r\n6–11 years: 50% discount\r\nUnder 6 years: Free of charge or 100% discount (must share tent with parents)', 'active', '2026-06-06 19:54:57');

-- --------------------------------------------------------
-- Table structure for table `bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `tour_id` int NOT NULL,
  `booking_date` date DEFAULT NULL,
  `people` int DEFAULT '1',
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `stripe_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`tour_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `bookings`
INSERT INTO `bookings` (`booking_id`, `user_id`, `tour_id`, `booking_date`, `people`, `total_price`, `status`, `stripe_token`, `created_at`) VALUES
('1', '2', '1', '2026-06-10', '2', '198.00', 'pending', NULL, '2026-06-02 19:56:26'),
('2', '2', '1', '2026-06-07', '1', '99.00', 'approved', NULL, '2026-06-06 00:08:44'),
('3', '2', '1', '2026-06-12', '1', '99.00', 'approved', 'tok_1Tf1X4CxnG4IG5j6fgz2X5dH', '2026-06-06 00:27:46'),
('4', '2', '2', '2026-06-06', '1', '149.00', 'approved', 'ch_3Tf1dFCxnG4IG5j60i4oPI3L', '2026-06-06 00:34:10'),
('5', '2', '3', '2026-06-26', '1', '59.00', 'approved', 'ch_3Tf1fICxnG4IG5j60fFFyTmA', '2026-06-06 00:36:16');

SET FOREIGN_KEY_CHECKS = 1;
