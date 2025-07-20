CREATE TABLE messages (
  id int(11) NOT NULL AUTO_INCREMENT,
  consumer_id int(11) NOT NULL,
  distributor_id int(11) NOT NULL,
  message text NOT NULL,
  sent_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY consumer_id (consumer_id),
  KEY distributor_id (distributor_id),
  CONSTRAINT messages_ibfk_1 FOREIGN KEY (consumer_id) REFERENCES consumer (consumer_id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_2 FOREIGN KEY (distributor_id) REFERENCES distributor (distributor_id) ON DELETE CASCADE
) ENGINE=InnoDB; 