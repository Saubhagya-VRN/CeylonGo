-- Create diary_comments table
CREATE TABLE IF NOT EXISTS diary_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('tourist', 'guide', 'hotel', 'transporter', 'admin') NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entry_id) REFERENCES trip_diary_entries(id) ON DELETE CASCADE,
    INDEX idx_entry_id (entry_id),
    INDEX idx_user (user_id, user_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

