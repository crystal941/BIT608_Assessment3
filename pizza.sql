DROP DATABASE pizza;
CREATE DATABASE IF NOT EXISTS pizza;
USE pizza;

-- The items/products for the Pizzeria
DROP TABLE IF EXISTS fooditems;
CREATE TABLE IF NOT EXISTS fooditems (
  itemID int unsigned NOT NULL auto_increment,
  pizza varchar(15) NOT NULL, -- name of pizza
  description text default NULL, -- description of the ingredients
  pizzatype character default 'S', -- S - standard, V - vegetarian 
  price float NOT NULL, -- what does the pizza cost
  PRIMARY KEY (itemID)
) AUTO_INCREMENT=1;

INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (1,"PIZZA 1","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing","S",10);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (2,"PIZZA 2","Lorem ipsum dolor sit amet, consectetuer","S",10);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (3,"PIZZA 3","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur","S",6);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (4,"PIZZA 4","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam","S",6);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (5,"PIZZA 5","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing lacus.","S",8);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (6,"PIZZA 6","Lorem ipsum dolor sit amet, consectetuer adipiscing","S",7);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (7,"PIZZA 7","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing lacus.","S",8);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (8,"PIZZA 8","Lorem ipsum dolor sit amet,","S",9);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (9,"PIZZA 9","Lorem ipsum dolor sit","V",9);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (10,"PIZZA 10","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer","V",6);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (11,"PIZZA 11","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur","V",6);
INSERT INTO `fooditems` (`itemID`,`pizza`,`description`,`pizzatype`,`price`) VALUES (12,"PIZZA 12","Lorem","V",7);


-- Bookings for the resturaunt
DROP TABLE IF EXISTS booking;
CREATE TABLE IF NOT EXISTS booking (
  bookingID int unsigned NOT NULL auto_increment,
  customerID int unsigned NOT NULL,
  telephone varchar(14) NOT NULL,
  bookingdate datetime,
  people int default 1,
  PRIMARY KEY (bookingID)
) AUTO_INCREMENT=1;

insert into booking (bookingID, customerID, telephone, bookingdate, people) values (1, 1, '592-232-0521', '2021-12-18 17:29:36', 2);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (2, 8, '775-120-6785', '2021-05-18 06:13:06', 4);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (3, 4, '393-916-0672', '2021-02-11 08:39:29', 1);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (4, 9, '114-541-0005', '2021-11-28 12:20:58', 1);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (5, 2, '561-687-0825', '2021-06-10 03:52:37', 4);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (6, 9, '959-512-2639', '2021-03-24 17:06:28', 3);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (7, 2, '593-781-9360', '2021-03-01 04:11:27', 4);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (8, 7, '473-595-2768', '2021-11-04 08:16:06', 5);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (9, 4, '673-132-5499', '2021-01-29 16:57:48', 3);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (10, 1, '151-149-9447', '2021-05-20 14:13:23', 2);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (11, 4, '382-125-5641', '2021-01-16 17:42:15', 5);
insert into booking (bookingID, customerID, telephone, bookingdate, people) values (12, 7, '507-644-2363', '2021-03-24 05:13:46', 3);


-- Customers
DROP TABLE IF EXISTS customer;
CREATE TABLE IF NOT EXISTS customer (
  customerID int unsigned NOT NULL auto_increment,
  firstname varchar(50) NOT NULL,
  lastname varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  role varchar(50) NOT NULL default 'customer',  
  password varchar(40) NOT NULL default '.',
  PRIMARY KEY (customerID)
) AUTO_INCREMENT=1;

INSERT INTO customer (customerID,firstname,lastname,email) VALUES 
(2,"Desiree","Collier","Maecenas@non.co.uk"),
(3,"Irene","Walker","id.erat.Etiam@id.org"),
(4,"Forrest","Baldwin","eget.nisi.dictum@a.com"),
(5,"Beverly","Sellers","ultricies.sem@pharetraQuisqueac.co.uk"),
(6,"Glenna","Kinney","dolor@orcilobortisaugue.org"),
(7,"Montana","Gallagher","sapien.cursus@ultriciesdignissimlacus.edu"),(8,"Harlan","Lara","Duis@aliquetodioEtiam.edu"),
(9,"Benjamin","King","mollis@Nullainterdum.org"),
(10,"Rajah","Olsen","Vestibulum.ut.eros@nequevenenatislacus.ca"),
(11,"Castor","Kelly","Fusce.feugiat.Lorem@porta.co.uk"),
(12,"Omar","Oconnor","eu.turpis@auctorvelit.co.uk"),
(13,"Porter","Leonard","dui.Fusce@accumsanlaoreet.net"),
(14,"Buckminster","Gaines","convallis.convallis.dolor@ligula.co.uk"),
(15,"Hunter","Rodriquez","ridiculus.mus.Donec@est.co.uk"),
(16,"Zahir","Harper","vel@estNunc.com"),
(17,"Sopoline","Warner","vestibulum.nec.euismod@sitamet.co.uk"),
(18,"Burton","Parrish","consequat.nec.mollis@nequenonquam.org"),
(19,"Abbot","Rose","non@et.ca"),
(20,"Barry","Burks","risus@libero.net");

-- special account for the purposes of this exercise
-- refer to the checksession.php on how it is used
INSERT INTO customer (customerID,firstname,lastname,email,password) VALUES 
(1,"Admin","Admin","admin@pizza.com","password");

-- Orders
DROP TABLE IF EXISTS orders;
CREATE TABLE IF NOT EXISTS orders (
  orderID int unsigned NOT NULL auto_increment,
  customerID int unsigned,
  ordertime datetime NOT NULL,
  extras varchar(500) default NULL,
  PRIMARY KEY (orderID),
  FOREIGN KEY (customerID) REFERENCES customer(customerID)
) AUTO_INCREMENT=1;

-- Fooditems per order 
DROP TABLE IF EXISTS orderlines;
CREATE TABLE IF NOT EXISTS orderlines ( 
  lineID int unsigned NOT NULL auto_increment,
  orderID int unsigned,
  itemID int unsigned,
  quantity int unsigned NOT NULL default 1,
  PRIMARY KEY (lineID),
  FOREIGN KEY (orderID) REFERENCES orders(orderID) ON DELETE CASCADE,
  FOREIGN KEY (itemID) REFERENCES fooditems(itemID) ON DELETE CASCADE
) AUTO_INCREMENT=1;
