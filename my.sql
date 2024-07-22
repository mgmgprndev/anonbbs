CREATE DATABASE anonbbs;
CREATE USER 'anonbbs'@'localhost' IDENTIFIED BY 'anonbbs';
GRANT ALL PRIVILEGES ON anonbbs.* TO 'anonbbs'@'localhost';
FLUSH PRIVILEGES;
USE anonbbs;


drop table threads;
drop table comments;

create table threads(
    id INT AUTO_INCREMENT PRIMARY KEY,
    roomkey TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

create table comments(
    id INT AUTO_INCREMENT PRIMARY KEY,
    threadid INT NOT NULL,
    text TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);