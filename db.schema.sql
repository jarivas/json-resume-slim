-- app.tokens definition

CREATE TABLE `tokens` (
  `id` char(26) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.basics definition

CREATE TABLE `basics` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `location` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`location`)),
  `profiles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`profiles`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.awards definition

CREATE TABLE `awards` (
  `id` char(26) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `awarder` varchar(255) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `awards_basic_id_foreign` (`basic_id`),
  CONSTRAINT `awards_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.certificates definition

CREATE TABLE `certificates` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `issuer` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `certificates_basic_id_foreign` (`basic_id`),
  CONSTRAINT `certificates_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.educations definition

CREATE TABLE `educations` (
  `id` char(26) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `area` varchar(255) NOT NULL,
  `studyType` varchar(255) NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `score` varchar(255) DEFAULT NULL,
  `summary` varchar(255) NOT NULL,
  `courses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`courses`)),
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `educations_basic_id_foreign` (`basic_id`),
  CONSTRAINT `educations_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.interests definition

CREATE TABLE `interests` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`keywords`)),
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interests_basic_id_foreign` (`basic_id`),
  CONSTRAINT `interests_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.languages definition

CREATE TABLE `languages` (
  `id` char(26) NOT NULL,
  `language` varchar(255) NOT NULL,
  `fluency` varchar(255) NOT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `languages_basic_id_foreign` (`basic_id`),
  CONSTRAINT `languages_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.projects definition

CREATE TABLE `projects` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`highlights`)),
  `url` varchar(255) DEFAULT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_basic_id_foreign` (`basic_id`),
  CONSTRAINT `projects_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.publications definition

CREATE TABLE `publications` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `publisher` varchar(255) NOT NULL,
  `releaseDate` datetime NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `summary` varchar(255) NOT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publications_basic_id_foreign` (`basic_id`),
  CONSTRAINT `publications_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.`references` definition

CREATE TABLE `references` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `references_basic_id_foreign` (`basic_id`),
  CONSTRAINT `references_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.skills definition

CREATE TABLE `skills` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`keywords`)),
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skills_basic_id_foreign` (`basic_id`),
  CONSTRAINT `skills_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.volunteers definition

CREATE TABLE `volunteers` (
  `id` char(26) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `summary` varchar(255) NOT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`highlights`)),
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `volunteers_basic_id_foreign` (`basic_id`),
  CONSTRAINT `volunteers_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- app.works definition

CREATE TABLE `works` (
  `id` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `summary` varchar(255) NOT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`highlights`)),
  `basic_id` char(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `works_basic_id_foreign` (`basic_id`),
  CONSTRAINT `works_basic_id_foreign` FOREIGN KEY (`basic_id`) REFERENCES `basics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;