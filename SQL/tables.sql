-- Base y opciones
CREATE DATABASE IF NOT EXISTS `draftosaurus_db`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `draftosaurus_db`;

-- 1) Usuarios (ya lo tenés)
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `contrasenia` VARCHAR(255) NOT NULL,         -- guardar HASH, no texto plano
  `foto` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Tipos de dinosaurio (catálogo)
CREATE TABLE IF NOT EXISTS `dino_tipo` (
  `id` TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(20) NOT NULL UNIQUE,        -- p.ej. TRex, Stego, etc.
  `nombre` VARCHAR(60) NOT NULL,
  `emoji` VARCHAR(8) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Zonas / Recintos del tablero (catálogo)
CREATE TABLE IF NOT EXISTS `recinto` (
  `id` TINYINT UNSIGNED PRIMARY KEY,           -- usamos los IDs que definimos (1..8)
  `nombre` VARCHAR(80) NOT NULL,
  `es_especial` TINYINT(1) NOT NULL DEFAULT 0  -- 1 = Río (no cuenta como recinto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Partida
CREATE TABLE IF NOT EXISTS `partida` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `estado` ENUM('creada','en_juego','finalizada') NOT NULL DEFAULT 'creada',
  `modo` ENUM('seguimiento','digital') NOT NULL DEFAULT 'seguimiento',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `finalizada_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Jugadores en una partida (relación usuario-partida)
CREATE TABLE IF NOT EXISTS `partida_jugador` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `partida_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `orden_turno` TINYINT UNSIGNED NOT NULL,
  `puntaje_final` SMALLINT NULL,
  UNIQUE KEY `uq_partida_usuario` (`partida_id`,`usuario_id`),
  CONSTRAINT `fk_pj_partida` FOREIGN KEY (`partida_id`) REFERENCES `partida`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pj_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6) Colocaciones (lo más importante del juego)
-- Registra cada dinosaurio colocado por un jugador en un recinto/posición y turno.
CREATE TABLE IF NOT EXISTS `colocacion` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `partida_id` BIGINT UNSIGNED NOT NULL,
  `jugador_id` BIGINT UNSIGNED NOT NULL,       -- referencia a partida_jugador.id
  `turno` TINYINT UNSIGNED NOT NULL,
  `recinto_id` TINYINT UNSIGNED NOT NULL,      -- 1..8 (Río es especial)
  `posicion` TINYINT UNSIGNED NULL,            -- slot dentro del recinto (si aplica)
  `dino_tipo_id` TINYINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_col_partida` FOREIGN KEY (`partida_id`) REFERENCES `partida`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_col_jugador` FOREIGN KEY (`jugador_id`) REFERENCES `partida_jugador`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_col_recinto` FOREIGN KEY (`recinto_id`) REFERENCES `recinto`(`id`),
  CONSTRAINT `fk_col_dino` FOREIGN KEY (`dino_tipo_id`) REFERENCES `dino_tipo`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7) Puntajes calculados por recinto (útil para "modo herramienta")
CREATE TABLE IF NOT EXISTS `puntaje_recinto` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `partida_id` BIGINT UNSIGNED NOT NULL,
  `jugador_id` BIGINT UNSIGNED NOT NULL,
  `recinto_id` TINYINT UNSIGNED NOT NULL,
  `puntos` SMALLINT NOT NULL,
  UNIQUE KEY `uq_part_jug_rec` (`partida_id`,`jugador_id`,`recinto_id`),
  CONSTRAINT `fk_pr_partida` FOREIGN KEY (`partida_id`) REFERENCES `partida`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_jugador` FOREIGN KEY (`jugador_id`) REFERENCES `partida_jugador`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_recinto` FOREIGN KEY (`recinto_id`) REFERENCES `recinto`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;