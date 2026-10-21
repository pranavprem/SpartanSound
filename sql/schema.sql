-- Reconstructed catalog for SpartanSound.
-- The original MySQL data was never committed. These ten products use the
-- images and audio that were already in the repo, and the twenty users are
-- fictional stand-ins for the class assignment. Re-running this file drops
-- both tables.

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(100) NOT NULL,
  lastname VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  homeaddress VARCHAR(255) NOT NULL,
  homephone VARCHAR(50) NOT NULL,
  cellphone VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  imgUrl VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  ud1 VARCHAR(255) NOT NULL DEFAULT '',
  keywords VARCHAR(255) NOT NULL,
  hits INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (firstname, lastname, email, homeaddress, homephone, cellphone) VALUES
('Mary', 'Smith', 'mary.smith@example.com', '100 Campus Dr, San Jose, CA', '408-555-0101', '408-555-0191'),
('John', 'Wang', 'john.wang@example.com', '14 S 4th St, San Jose, CA', '408-555-0102', '408-555-0192'),
('Alex', 'Bington', 'alex.bington@example.com', '1 Washington Sq, San Jose, CA', '408-555-0103', '408-555-0193'),
('Priya', 'Raman', 'priya.raman@example.com', '88 E Santa Clara St, San Jose, CA', '408-555-0104', '408-555-0194'),
('Diego', 'Alvarez', 'diego.alvarez@example.com', '200 S 1st St, San Jose, CA', '408-555-0105', '408-555-0195'),
('Hannah', 'Cho', 'hannah.cho@example.com', '50 N Almaden Blvd, San Jose, CA', '408-555-0106', '408-555-0196'),
('Omar', 'Farouk', 'omar.farouk@example.com', '320 Park Ave, San Jose, CA', '408-555-0107', '408-555-0197'),
('Lila', 'Nguyen', 'lila.nguyen@example.com', '77 N San Pedro St, San Jose, CA', '408-555-0108', '408-555-0198'),
('Chris', 'Dalton', 'chris.dalton@example.com', '410 E William St, San Jose, CA', '408-555-0109', '408-555-0199'),
('Sofia', 'Rossi', 'sofia.rossi@example.com', '12 S Market St, San Jose, CA', '408-555-0110', '408-555-0200'),
('Noah', 'Patel', 'noah.patel@example.com', '640 Coleman Ave, San Jose, CA', '408-555-0111', '408-555-0201'),
('Grace', 'Kim', 'grace.kim@example.com', '9 N 2nd St, San Jose, CA', '408-555-0112', '408-555-0202'),
('Ethan', 'Brooks', 'ethan.brooks@example.com', '155 E San Fernando St, San Jose, CA', '408-555-0113', '408-555-0203'),
('Maya', 'Chen', 'maya.chen@example.com', '27 Post St, San Jose, CA', '408-555-0114', '408-555-0204'),
('Lucas', 'Ferreira', 'lucas.ferreira@example.com', '500 W San Carlos St, San Jose, CA', '408-555-0115', '408-555-0205'),
('Aisha', 'Rahman', 'aisha.rahman@example.com', '63 Devine St, San Jose, CA', '408-555-0116', '408-555-0206'),
('Ben', 'Okonkwo', 'ben.okonkwo@example.com', '18 N 5th St, San Jose, CA', '408-555-0117', '408-555-0207'),
('Clara', 'Jensen', 'clara.jensen@example.com', '240 N 1st St, San Jose, CA', '408-555-0118', '408-555-0208'),
('Samir', 'Kapoor', 'samir.kapoor@example.com', '8 S 3rd St, San Jose, CA', '408-555-0119', '408-555-0209'),
('Nina', 'Volkov', 'nina.volkov@example.com', '90 E San Salvador St, San Jose, CA', '408-555-0120', '408-555-0210');

INSERT INTO products (id, name, description, imgUrl, price, ud1, keywords, hits) VALUES
(1, 'Three Horsemen', 'Live trio recorded loud enough to rattle a practice room.', 'img/3horsemen.jpg', 5, 'audio/3horsemen.mp3', 'track, rock', 48),
(2, 'All of Me', 'Piano-led cover added to the catalog for the jazz crowd.', 'img/piano.jpg', 3, 'audio/allOfMe.mp3', 'track, piano', 36),
(3, 'Radioactive', 'A heavy cover for late-night headbanging.', 'img/headbang.jpg', 4, 'audio/RADIOACTIVE.mp3', 'track, rock', 41),
(4, 'Guitar Sample', 'The guitar take mentioned on the contacts page. Free to play.', 'img/pranav.png', 0, 'audio/Pranav.mp3', 'track, sample', 29),
(5, 'Ibanez RG and Marshall Stack', 'Used gig rig: one guitar and a full stack.', 'img/ibanez-marshall.PNG', 650, '', 'gear, guitar', 22),
(6, 'Club Turntable', 'Direct-drive deck from a campus DJ.', 'img/turntable.jpg', 180, '', 'gear, dj', 17),
(7, 'Travel Ukulele', 'Small, a little scuffed, and still in tune.', 'img/ukulele.jpg', 40, '', 'gear, instrument', 11),
(8, 'Studio Microphone', 'Vocal mic for dorm-room sessions.', 'img/mic.jpg', 75, '', 'gear, studio', 14),
(9, 'CX-05 Gimbal', 'Camera stabilizer for music-video shoots.', 'img/cx-05.jpg', 90, '', 'gear, video', 8),
(10, 'California Theatre Set', 'The Spartan jam from the news section, recorded live.', 'img/concert.jpg', 6, '', 'track, live', 19);
