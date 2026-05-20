CREATE TABLE IF NOT EXISTS `admin_registration_requests` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `username`      varchar(100) NOT NULL,
  `nome`          varchar(150) NOT NULL,
  `email`         varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status`        enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by`   int(11)      DEFAULT NULL,
  `reviewed_at`   datetime     DEFAULT NULL,
  `requested_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reviewed_by` (`reviewed_by`),
  CONSTRAINT `fk_req_reviewed_by`
    FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
