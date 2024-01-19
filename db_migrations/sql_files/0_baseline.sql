CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `release_year` int(4) NOT NULL,
  `format` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `stars` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `stars_in_movies` (
  `id` int(11) NOT NULL,
  `star_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password_hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `email`, `password_hash`) VALUES
(1, 'serhii_bilohub@webbylab.com', '$2y$10$alh2jTfg4x7z4BvZeqzT9.p6kzNAXCRjreV./tzfG2LLYB5MJvw2W');

ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stars`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stars_in_movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `star_id` (`star_id`),
  ADD KEY `movie_id` (`movie_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stars_in_movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `stars_in_movies`
  ADD CONSTRAINT `movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `star_id` FOREIGN KEY (`star_id`) REFERENCES `stars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;