CREATE DATABASE IF NOT EXISTS music_mood_recommender CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE music_mood_recommender;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS songs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    mood ENUM('Happy', 'Sad', 'Energetic', 'Relaxed', 'Romantic', 'Angry') NOT NULL,
    audio_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_mood (mood)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO songs (title, artist, mood, audio_url) VALUES
('Sunrise Pulse', 'Aurora Lane', 'Happy', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3'),
('Blue Hour', 'Mika Vale', 'Sad', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3'),
('Break the Silence', 'North Static', 'Energetic', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3'),
('Moonlit Drift', 'Nova Harbor', 'Relaxed', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3'),
('Velvet Echo', 'Carmen Bloom', 'Romantic', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3'),
('Fireline', 'Red Circuit', 'Angry', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3');
