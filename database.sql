CREATE DATABASE IF NOT EXISTS share_posts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `users` (
     `id` int(11) NOT NULL,
     `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
     `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
     `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
     `created_at` datetime NOT NULL,
     `last_login_at` datetime NOT NULL
);

ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`username`);

ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `posts` (
     `id` int(11) NOT NULL,
     `user_id` int(11) DEFAULT NULL,
     `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `created_at` datetime NOT NULL
);

ALTER TABLE `posts` ADD PRIMARY KEY (`id`), ADD KEY `IDX_DB021E96A76ED395` (`user_id`);

ALTER TABLE `posts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `posts` ADD CONSTRAINT `FK_DB021E96A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
