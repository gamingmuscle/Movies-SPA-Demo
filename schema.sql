CREATE DATABASE IF NOT EXISTS movies_app;
USE movies_app;

-- Table of movies
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
	release_date date,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- Table to store votes actually cast
CREATE TABLE IF NOT EXISTS vote_transactions
(
	id INT AUTO_INCREMENT PRIMARY KEY,
	movies_id INT,
	vote_type VARCHAR(4) NOT NULL,
	ip_address VARBINARY(16) NOT NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	foreign key (movies_id) references movies(id)
);
-- Create trigger to update vote totals
delimiter |
CREATE TRIGGER update_movie_votes
AFTER INSERT ON vote_transactions
FOR EACH ROW
BEGIN
	if NEW.vote_type = 'up' then
		UPDATE movies
			SET upvotes = upvotes + 1
		WHERE id = NEW.movies_id;
	elseif NEW.vote_type = 'down' then
		UPDATE movies
			SET downvotes = downvotes + 1
		WHERE id = NEW.movies_id;
	end if;
END|
delimiter ;


-- Sample data
INSERT INTO movies (title, release_date, upvotes, downvotes) VALUES
('Star Wars','1977-05-25', 150,10),
('Return of the Jedi','1983-05-25', 150, 10),
('Empire Strikes back','1980-05-21', 150, 10),
('Zootopia','2016-03-04', 130, 20),
('Moana','2016-11-23', 120, 18),
('Frozen','2013-11-27', 110, 12),
('Toy Story','1995-11-22', 100, 25),
('Cars','2006-06-9', 95, 30),
('Turning Red','2022-03-11', 90, 22),
('Kpop Demon Hunters','2025-06-20', 125,0);
