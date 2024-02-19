# create test database
CREATE DATABASE IF NOT EXISTS `bookstack_test`;

# grant rights
GRANT ALL PRIVILEGES ON `bookstack_test`.* TO 'bookstack_test'@'%';