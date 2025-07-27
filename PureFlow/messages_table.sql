-- Messaging table for consumer, distributor, and admin
CREATE TABLE messages (
  message_id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  sender_type ENUM('consumer','distributor','admin') NOT NULL,
  receiver_id INT NOT NULL,
  receiver_type ENUM('consumer','distributor','admin') NOT NULL,
  content TEXT NOT NULL,
  sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) DEFAULT 0
);

-- Indexes for faster lookups
CREATE INDEX idx_sender ON messages(sender_id, sender_type);
CREATE INDEX idx_receiver ON messages(receiver_id, receiver_type);
