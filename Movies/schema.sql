CREATE DATABASE IF NOT EXISTS movies_app;

USE movies_app;

-- Intentionally drop tables if they exist
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS vote_transactions;

-- Table of movies
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
	release_date date,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table to store votes actually cast
CREATE TABLE vote_transactions
(
	id INT AUTO_INCREMENT PRIMARY KEY,
	movies_id INT NOT NULL,
	vote_type VARCHAR(4) NOT NULL,
	ip_address VARBINARY(16) NOT NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	foreign key (movies_id) references movies(id) on delete CASCADE,
	index idx_movies_id (movies_id)
);

-- If triggers exists, drop them
DROP TRIGGER IF EXISTS increment_movie_votes;
DROP TRIGGER IF EXISTS decrement_movie_votes;
DROP TRIGGER IF EXISTS prevent_manual_update;
-- Create trigger to increment vote totals
delimiter |
CREATE TRIGGER increment_movie_votes
AFTER INSERT ON vote_transactions
FOR EACH ROW
BEGIN
	SET @internal_vote_update = 1;
	if NEW.vote_type = 'up' then
		UPDATE movies
			SET upvotes = upvotes + 1
		WHERE id = NEW.movies_id;
	elseif NEW.vote_type = 'down' then
		UPDATE movies
			SET downvotes = downvotes + 1
		WHERE id = NEW.movies_id;
	end if;
	SET @internal_vote_update = null;
END|
-- keeps counts consistent if someone deletes a vote transactions
CREATE TRIGGER decrement_movie_votes
AFTER delete ON vote_transactions
FOR EACH ROW
BEGIN
	SET @internal_vote_update = 1;
	if OLD.vote_type = 'up' then
		UPDATE movies
			SET upvotes = upvotes - 1
		WHERE id = OLD.movies_id;
	elseif OLD.vote_type = 'down' then
		UPDATE movies
			SET downvotes = downvotes - 1
		WHERE id = OLD.movies_id;
	end if;
	SET @internal_vote_update = null;
END|

-- Prevents manual updates on the upvote/downvote columns
CREATE TRIGGER prevent_manual_update
BEFORE UPDATE ON movies
FOR EACH ROW
BEGIN
	IF @internal_vote_update = 1 THEN
        -- Do nothing; allow update to proceed
        SET @internal_vote_update = @internal_vote_update;
    ELSEIF NEW.upvotes <> OLD.upvotes OR NEW.downvotes <> OLD.downvotes THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Manual update of vote counts is not allowed';
    END IF;
END|

delimiter ;


-- Sample data
INSERT INTO movies (title, release_date, upvotes, downvotes) VALUES
('Star Wars','1977-05-25', 0,0),
('Return of the Jedi','1983-05-25', 0, 0),
('Empire Strikes back','1980-05-21', 0, 0),
('Zootopia','2016-03-04', 0, 0),
('Moana','2016-11-23', 0, 0),
('Frozen','2013-11-27', 0, 0),
('Toy Story','1995-11-22', 0, 0),
('Cars','2006-06-09', 0, 0),
('Turning Red','2022-03-11', 0, 0),
('Kpop Demon Hunters','2025-06-20', 0,0);
