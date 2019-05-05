
DROP TABLE `HammerGuard`;
DROP TABLE `Tokens`;
DROP TABLE `Bookings`;
DROP TABLE `Reservations`;
DROP TABLE `Customers`;
DROP TABLE `Bookings_Customers`;
DROP TABLE `Rooms`;
DROP TABLE `Tours`;
DROP TABLE `Categories_Tours`;
DROP TABLE `Categories`;
DROP TABLE `Payments`;
DROP TABLE `Leads`;
DROP TABLE `Categories_Leads`;
DROP TABLE `Deadlines`;
DROP TABLE `Budgets`;
DROP TABLE `Budgets_Costs`;
DROP TABLE `Budgets_Earnings`;
DROP TABLE `Auth`;


-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/d/w9zPqM
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


CREATE TABLE `Auth` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `user` varchar(100)  NOT NULL ,
    `pwd` varchar(255)  NOT NULL ,
    PRIMARY KEY (
        `id`
    ),
    CONSTRAINT `uc_Auth_user` UNIQUE (
        `user`
    )
);

CREATE TABLE `HammerGuard` (
    `iphash` varchar(255)  NOT NULL ,
    `created` bigint  NOT NULL 
);

CREATE TABLE `Tokens` (
    `token` varchar(255)  NOT NULL ,
    `tokentype` varchar(255)  NOT NULL ,
    `created` bigint  NOT NULL ,
    `username` varchar(255)  NULL 
);

CREATE TABLE `Bookings` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `number` bigint  NOT NULL ,
    `tourId` bigint  NOT NULL ,
    `group` tinyint  NOT NULL ,
    `cancelled` tinyint  NOT NULL ,
    `cancelledDate` date  NULL ,
    `payDate1` date  NULL ,
    `payDate2` date  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Reservations` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `tourId` bigint  NOT NULL ,
    `roomId` bigint  NOT NULL ,
    `label` varchar(200)  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Customers` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `firstName` varchar(100)  NOT NULL ,
    `lastName` varchar(100)  NOT NULL ,
    `street` varchar(100)  NULL ,
    `zip` int  NULL ,
    `city` varchar(100)  NULL ,
    `phone` varchar(25)  NULL ,
    `email` varchar(70)  NULL ,
    `personalNumber` char(11)  NULL ,
    `date` date  NOT NULL ,
    `category` varchar(70)  NULL ,
    `compare` char(200)  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Bookings_Customers` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `bookingId` bigint  NOT NULL ,
    `customerId` bigint  NOT NULL ,
    `roomId` bigint  NOT NULL ,
    `requests` varchar(360)  NULL ,
    `priceAdjustment` int  NOT NULL ,
    `departureLocation` varchar(100)  NULL ,
    `departureTime` time  NULL ,
    `cancellationInsurance` tinyint  NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Rooms` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `tourId` bigint  NOT NULL ,
    `label` varchar(100)  NOT NULL ,
    `price` decimal(19,4)  NOT NULL ,
    `size` int  NOT NULL ,
    `numberAvaliable` int  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Tours` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `label` varchar(100)  NOT NULL ,
    `insurancePrice` int  NOT NULL ,
    `reservationFeePrice` int  NOT NULL ,
    `departureDate` date  NOT NULL ,
    `isDeleted` tinyint  NOT NULL ,
    `isDisabled` tinyint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Categories_Tours` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `tourId` bigint  NOT NULL ,
    `categoryId` bigint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Categories` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `label` varchar(60)  NOT NULL ,
    `isDeleted` tinyint  NOT NULL ,
    `isDisabled` tinyint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Payments` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `number` bigint  NOT NULL ,
    `bookingId` bigint  NOT NULL ,
    `customerId` bigint  NOT NULL ,
    `date` date  NOT NULL ,
    `Amount` decimal(19,2)  NOT NULL ,
    `insuranceAmount` decimal(19,2)  NULL ,
    `method` varchar(30)  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Leads` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `firstName` varchar(100)  NOT NULL ,
    `lastName` varchar(100)  NOT NULL ,
    `street` varchar(100)  NULL ,
    `zip` bigint  NULL ,
    `city` varchar(100)  NULL ,
    `phone` varchar(25)  NULL ,
    `email` varchar(60)  NULL ,
    `date` date  NOT NULL ,
    `compare` nchar(200)  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Categories_Leads` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `leadId` bigint  NOT NULL ,
    `categoryId` bigint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Deadlines` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `tourId` bigint  NOT NULL ,
    `label` varchar(200)  NULL ,
    `duedate` date  NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Budgets` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `tourid` bigint  NULL ,
    `label` varchar(100)  NULL ,
    `rooms` int  NOT NULL ,
    `singlerooms` int  NULL ,
    `estimatedprice` int  NULL ,
    `isDeleted` tinyint  NOT NULL ,
    `isDisabled` tinyint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Budgets_Costs` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `budgetId` bigint  NOT NULL ,
    `label` varchar(100)  NOT NULL ,
    `amount` int  NOT NULL ,
    `isFixed` tinyint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Budgets_Earnings` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `budgetId` bigint  NOT NULL ,
    `label` varchar(100)  NOT NULL ,
    `amount` int  NOT NULL ,
    `isFixed` tinyint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Categories_GroupCustomers` (
    `id` bigint AUTO_INCREMENT NOT NULL ,
    `groupId` bigint  NOT NULL ,
    `categoryId` bigint  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

ALTER TABLE `Bookings` ADD CONSTRAINT `fk_Bookings_tourId` FOREIGN KEY(`tourId`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Reservations` ADD CONSTRAINT `fk_Reservations_tourId` FOREIGN KEY(`tourId`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Reservations` ADD CONSTRAINT `fk_Reservations_roomId` FOREIGN KEY(`roomId`)
REFERENCES `Rooms` (`id`);

ALTER TABLE `Bookings_Customers` ADD CONSTRAINT `fk_Bookings_Customers_bookingId` FOREIGN KEY(`bookingId`)
REFERENCES `Bookings` (`id`);

ALTER TABLE `Bookings_Customers` ADD CONSTRAINT `fk_Bookings_Customers_customerId` FOREIGN KEY(`customerId`)
REFERENCES `Customers` (`id`);

ALTER TABLE `Bookings_Customers` ADD CONSTRAINT `fk_Bookings_Customers_roomId` FOREIGN KEY(`roomId`)
REFERENCES `Rooms` (`id`);

ALTER TABLE `Rooms` ADD CONSTRAINT `fk_Rooms_tourId` FOREIGN KEY(`tourId`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Categories_Tours` ADD CONSTRAINT `fk_Categories_Tours_tourId` FOREIGN KEY(`tourId`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Categories_Tours` ADD CONSTRAINT `fk_Categories_Tours_categoryId` FOREIGN KEY(`categoryId`)
REFERENCES `Categories` (`id`);

ALTER TABLE `Payments` ADD CONSTRAINT `fk_Payments_bookingId` FOREIGN KEY(`bookingId`)
REFERENCES `Bookings` (`id`);

ALTER TABLE `Categories_Leads` ADD CONSTRAINT `fk_Categories_Leads_leadId` FOREIGN KEY(`leadId`)
REFERENCES `Leads` (`id`);

ALTER TABLE `Categories_Leads` ADD CONSTRAINT `fk_Categories_Leads_categoryId` FOREIGN KEY(`categoryId`)
REFERENCES `Categories` (`id`);

ALTER TABLE `Deadlines` ADD CONSTRAINT `fk_Deadlines_tourId` FOREIGN KEY(`tourId`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Budgets` ADD CONSTRAINT `fk_Budgets_tourid` FOREIGN KEY(`tourid`)
REFERENCES `Tours` (`id`);

ALTER TABLE `Budgets_Costs` ADD CONSTRAINT `fk_Budgets_Costs_budgetId` FOREIGN KEY(`budgetId`)
REFERENCES `Budgets` (`id`);

ALTER TABLE `Budgets_Earnings` ADD CONSTRAINT `fk_Budgets_Earnings_budgetId` FOREIGN KEY(`budgetId`)
REFERENCES `Budgets` (`id`);

ALTER TABLE `Categories_GroupCustomers` ADD CONSTRAINT `fk_Categories_GroupCustomers_groupId` FOREIGN KEY(`groupId`)
REFERENCES `GroupCustomers` (`id`);

ALTER TABLE `Categories_GroupCustomers` ADD CONSTRAINT `fk_Categories_GroupCustomers_categoryId` FOREIGN KEY(`categoryId`)
REFERENCES `Categories` (`id`);