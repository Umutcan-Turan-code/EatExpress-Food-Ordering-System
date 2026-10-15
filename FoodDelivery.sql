DROP DATABASE IF EXISTS fooddelivery;
CREATE DATABASE fooddelivery CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE fooddelivery;

CREATE TABLE users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  FirstName VARCHAR(50) NOT NULL,
  LastName VARCHAR(50) NOT NULL,
  Email VARCHAR(100) NOT NULL UNIQUE,
  Phone VARCHAR(20) NOT NULL,
  Address VARCHAR(255) NOT NULL,
  Password VARCHAR(255) NOT NULL
);

CREATE TABLE deliveryzone (
  ZoneID INT AUTO_INCREMENT PRIMARY KEY,
  ZoneName VARCHAR(80) NOT NULL,
  CoverageArea VARCHAR(255) NOT NULL
);

CREATE TABLE restaurant (
  RestaurantID INT AUTO_INCREMENT PRIMARY KEY,
  RestaurantName VARCHAR(100) NOT NULL,
  Location VARCHAR(150) NOT NULL,
  Phone VARCHAR(20),
  Email VARCHAR(100),
  Rating DECIMAL(2,1),
  ZoneID INT,
  FOREIGN KEY (ZoneID) REFERENCES deliveryzone(ZoneID)
);

CREATE TABLE category (
  CategoryID INT AUTO_INCREMENT PRIMARY KEY,
  CategoryName VARCHAR(50) NOT NULL,
  Description VARCHAR(255)
);

CREATE TABLE fooditem (
  ItemID INT AUTO_INCREMENT PRIMARY KEY,
  RestaurantID INT NOT NULL,
  CategoryID INT NOT NULL,
  ItemName VARCHAR(100) NOT NULL,
  Price DECIMAL(10,2) NOT NULL,
  PreparationTime INT NOT NULL,
  FOREIGN KEY (RestaurantID) REFERENCES restaurant(RestaurantID),
  FOREIGN KEY (CategoryID) REFERENCES category(CategoryID)
);

CREATE TABLE deliveryrider (
  RiderID INT AUTO_INCREMENT PRIMARY KEY,
  FirstName VARCHAR(50) NOT NULL,
  LastName VARCHAR(50) NOT NULL,
  Phone VARCHAR(20) NOT NULL,
  VehicleType VARCHAR(40) NOT NULL,
  LicenseNumber VARCHAR(50) NOT NULL UNIQUE,
  Shift VARCHAR(30) NOT NULL
);

CREATE TABLE riderzone (
  RiderID INT NOT NULL,
  ZoneID INT NOT NULL,
  PRIMARY KEY (RiderID, ZoneID),
  FOREIGN KEY (RiderID) REFERENCES deliveryrider(RiderID) ON DELETE CASCADE,
  FOREIGN KEY (ZoneID) REFERENCES deliveryzone(ZoneID) ON DELETE CASCADE
);

CREATE TABLE orders (
  OrderID INT AUTO_INCREMENT PRIMARY KEY,
  UserID INT NOT NULL,
  RestaurantID INT NOT NULL,
  OrderDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  TotalAmount DECIMAL(10,2) NOT NULL,
  Status VARCHAR(30) NOT NULL DEFAULT 'Pending',
  Address VARCHAR(255) NOT NULL,
  FOREIGN KEY (UserID) REFERENCES users(UserID),
  FOREIGN KEY (RestaurantID) REFERENCES restaurant(RestaurantID)
);

CREATE TABLE orderdetails (
  OrderID INT NOT NULL,
  ItemID INT NOT NULL,
  Quantity INT NOT NULL,
  Price DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (OrderID, ItemID),
  FOREIGN KEY (OrderID) REFERENCES orders(OrderID) ON DELETE CASCADE,
  FOREIGN KEY (ItemID) REFERENCES fooditem(ItemID)
);

CREATE TABLE payment (
  PaymentID INT AUTO_INCREMENT PRIMARY KEY,
  OrderID INT NOT NULL UNIQUE,
  PaymentMethod VARCHAR(30) NOT NULL,
  PaymentStatus VARCHAR(30) NOT NULL DEFAULT 'Pending',
  PaymentDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Amount DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (OrderID) REFERENCES orders(OrderID) ON DELETE CASCADE
);

CREATE TABLE delivery (
  DeliveryID INT AUTO_INCREMENT PRIMARY KEY,
  OrderID INT NOT NULL UNIQUE,
  RiderID INT NOT NULL,
  ZoneID INT NOT NULL,
  DeliveryTime DATETIME NULL,
  EstimatedDeliveryTime DATETIME NOT NULL,
  DeliveryStatus VARCHAR(30) NOT NULL DEFAULT 'Assigned',
  FOREIGN KEY (OrderID) REFERENCES orders(OrderID) ON DELETE CASCADE,
  FOREIGN KEY (RiderID) REFERENCES deliveryrider(RiderID),
  FOREIGN KEY (ZoneID) REFERENCES deliveryzone(ZoneID)
);

INSERT INTO deliveryzone (ZoneName, CoverageArea) VALUES
('Karabuk Center', 'Karabuk city center and university area'),
('Istanbul Europe', 'Taksim, Besiktas, Sisli, Fatih'),
('Ankara Center', 'Kizilay, Cankaya, Bahcelievler'),
('Izmir Center', 'Konak, Alsancak, Bornova'),
('Bursa Center', 'Osmangazi, Nilufer'),
('Antalya Center', 'Muratpasa, Lara'),
('Eskisehir Center', 'Odunpazari, Tepebasi');

INSERT INTO restaurant (RestaurantName, Location, Phone, Email, Rating, ZoneID) VALUES
('Kebab House', 'Karabuk', '+90 370 123 4567', 'kebab@example.com', 4.8, 1),
('Burger House', 'Istanbul', '+90 212 111 2233', 'burger@example.com', 4.6, 2),
('Pizza Palace', 'Ankara', '+90 312 444 5566', 'pizza@example.com', 4.4, 3),
('Sushi House', 'Izmir', '+90 232 555 1122', 'sushi@example.com', 4.7, 4),
('Pasta Point', 'Bursa', '+90 224 333 7788', 'pasta@example.com', 4.5, 5),
('Dessert Spot', 'Antalya', '+90 242 777 8899', 'dessert@example.com', 4.3, 6),
('Doner Express', 'Istanbul', '+90 212 888 3344', 'doner@example.com', 4.6, 2),
('Taco Fiesta', 'Ankara', '+90 312 222 9900', 'taco@example.com', 4.2, 3),
('Healthy Bowl', 'Eskisehir', '+90 222 111 4455', 'healthy@example.com', 4.5, 7),
('Coffee Corner', 'Karabuk', '+90 370 555 6677', 'coffee@example.com', 4.1, 1);

INSERT INTO category (CategoryName, Description) VALUES
('Fast Food', 'Burger, sandwich and similar foods'),
('Pizza', 'Italian pizza types'),
('Turkish Food', 'Traditional Turkish meals'),
('Drinks', 'Cold and hot drinks'),
('Asian Food', 'Sushi and Asian meals'),
('Italian Food', 'Pasta and Italian meals'),
('Dessert', 'Cakes, sweets and desserts'),
('Mexican Food', 'Tacos and Mexican meals'),
('Healthy Food', 'Salads and healthy bowls'),
('Cafe', 'Coffee and bakery products');

INSERT INTO deliveryrider (FirstName, LastName, Phone, VehicleType, LicenseNumber, Shift) VALUES
('Ahmet', 'Yilmaz', '+90 530 111 2233', 'Motorcycle', 'KR-001-A', 'Morning'),
('Mehmet', 'Demir', '+90 531 222 3344', 'Scooter', 'IST-002-M', 'Evening'),
('Ali', 'Kaya', '+90 532 333 4455', 'Motorcycle', 'ANK-003-A', 'Morning'),
('Zeynep', 'Arslan', '+90 533 444 5566', 'Car', 'IZM-004-Z', 'Evening'),
('Hasan', 'Celik', '+90 534 555 6677', 'Motorcycle', 'BRS-005-H', 'Morning'),
('Elif', 'Sahin', '+90 535 666 7788', 'Scooter', 'ANT-006-E', 'Evening'),
('Burak', 'Polat', '+90 536 777 8899', 'Bicycle', 'ESK-007-B', 'Morning'),
('Murat', 'Aydin', '+90 537 888 9900', 'Motorcycle', 'KR-008-M', 'Evening');

INSERT INTO riderzone (RiderID, ZoneID) VALUES
(1,1), (8,1), (2,2), (3,3), (4,4), (5,5), (6,6), (7,7),
(2,3), (3,2), (8,2);

INSERT INTO fooditem (RestaurantID, CategoryID, ItemName, Price, PreparationTime) VALUES
(1, 3, 'Adana Kebab', 240.00, 25),
(1, 3, 'Lahmacun', 80.00, 15),
(1, 3, 'Chicken Shish', 210.00, 22),
(2, 1, 'Chicken Burger', 120.00, 15),
(2, 1, 'Cheese Burger', 135.00, 18),
(2, 4, 'Cola', 35.00, 2),
(3, 2, 'Margherita Pizza', 180.00, 25),
(3, 2, 'Pepperoni Pizza', 210.00, 30),
(3, 2, 'Mixed Pizza', 230.00, 32),
(4, 5, 'California Roll', 260.00, 30),
(4, 5, 'Salmon Sushi Set', 320.00, 35),
(5, 6, 'Fettuccine Alfredo', 190.00, 24),
(5, 6, 'Spaghetti Bolognese', 175.00, 22),
(6, 7, 'Chocolate Cake', 95.00, 12),
(6, 7, 'Waffle', 130.00, 15),
(7, 3, 'Chicken Doner Wrap', 110.00, 14),
(7, 3, 'Meat Doner Plate', 185.00, 20),
(8, 8, 'Beef Taco', 155.00, 22),
(8, 8, 'Chicken Quesadilla', 170.00, 24),
(9, 9, 'Caesar Salad', 140.00, 15),
(9, 9, 'Protein Bowl', 190.00, 18),
(10, 10, 'Latte', 70.00, 6),
(10, 10, 'Cheesecake', 95.00, 10);
