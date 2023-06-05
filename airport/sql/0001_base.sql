CREATE TABLE airplanes (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(12) NOT NULL,
    type VARCHAR(5) NOT NULL,
    speed VARCHAR(12) NOT NULL,
    is_flying TINYINT(1) NOT NULL,
    is_parked TINYINT(1) NOT NULL,
    is_loaded TINYINT(1) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (name)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

create table if not exists `versions` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;