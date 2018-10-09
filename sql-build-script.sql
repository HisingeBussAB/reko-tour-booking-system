DROP TABLE [HammerGuard]
DROP TABLE [Tokens]
--DROP TABLE [Auth]
DROP TABLE [Bookings]
DROP TABLE [Reservations]
DROP TABLE [Customers]
DROP TABLE [Bookings_Customers]
DROP TABLE [Rooms]
DROP TABLE [Tours]
DROP TABLE [Categories_Tours] 
DROP TABLE [Categories]
DROP TABLE [Payments]
DROP TABLE [Leads]
DROP TABLE [Categories_Leads]
DROP TABLE [Deadlines]
DROP TABLE [Budgets]
DROP TABLE [Budgets_Costs]
DROP TABLE [Budgets_Earnings]
DROP TABLE [trashBookings]
DROP TABLE [trashReservations]
DROP TABLE [trashCustomers]
DROP TABLE [trashBookings_Customers]
DROP TABLE [trashRooms]
DROP TABLE [trashTours]
DROP TABLE [trashCategories_Tours] 
DROP TABLE [trashCategories]
DROP TABLE [trashPayments]
DROP TABLE [trashLeads]
DROP TABLE [trashCategories_Leads]
DROP TABLE [trashDeadlines]
DROP TABLE [trashBudgets]
DROP TABLE [trashBudgets_Costs]
DROP TABLE [trashBudgets_Earnings]

-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/schema/w9zPqM6P8UOAWzy4IzSEkQ
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


SET XACT_ABORT ON

BEGIN TRANSACTION QUICKDBD

CREATE TABLE [HammerGuard] (
    [iphash] varchar(255)  NOT NULL ,
    [created] bigint  NOT NULL 
)

CREATE TABLE [Tokens] (
    [token] varchar(255)  NOT NULL ,
    [tokentype] varchar(255)  NOT NULL ,
    [created] bigint  NOT NULL ,
    [username] varchar(255)  NULL 
)

--CREATE TABLE [Auth] (
--    [id] bigint IDENTITY(1,1) NOT NULL ,
--    [user] varchar(255)  NOT NULL ,
--    [pwd] varchar(255)  NOT NULL ,
--    CONSTRAINT [PK_Auth] PRIMARY KEY CLUSTERED (
--        [id] ASC
--    ),
--    CONSTRAINT [UK_Auth_user] UNIQUE (
--        [user]
--    )
--)

CREATE TABLE [Bookings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [group] bit  NOT NULL ,
    [cancelled] bit  NOT NULL ,
    [cancelleddate] date  NULL ,
    [paydate1] date  NULL ,
    [paydate2] date  NOT NULL ,
    CONSTRAINT [PK_Bookings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Reservations] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [roomid] bigint  NOT NULL ,
    [label] varchar(200)  NOT NULL ,
    CONSTRAINT [PK_Reservations] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Customers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstname] varchar(100)  NOT NULL ,
    [lastname] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] int  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [personalnumber] varchar(10)  NULL ,
    [date] date  NOT NULL ,
    [category] varchar(60)  NULL ,
    [compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Customers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Bookings_Customers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [bookingid] bigint  NOT NULL ,
    [customerid] bigint  NOT NULL ,
    [roomid] bigint  NOT NULL ,
    [requests] varchar(360)  NULL ,
    [priceadjustment] bigint  NOT NULL ,
    [departurelocation] varchar(100)  NULL ,
    [departuretime] time  NULL ,
    [cancellationinsurance] bit  NULL ,
    CONSTRAINT [PK_Bookings_Customers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Rooms] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [price] bigint  NOT NULL ,
    [size] int  NOT NULL ,
    [numberavaliable] int  NOT NULL ,
    CONSTRAINT [PK_Rooms] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Tours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [insuranceprice] bigint  NOT NULL ,
    [reservationfeeprice] bigint  NOT NULL ,
    [departuredate] date NOT NULL ,
    CONSTRAINT [PK_Tours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories_Tours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [categoryid] bigint  NOT NULL ,
    CONSTRAINT [PK_Categories_Tours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(60)  NOT NULL ,
    [active] bit  NOT NULL ,
    CONSTRAINT [PK_Categories] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Payments] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [bookingid] bigint  NOT NULL ,
    [customerid] bigint  NOT NULL ,
    [date] date  NOT NULL ,
    [sum] bigint  NOT NULL ,
    [insurancesum] bigint  NULL ,
    [method] varchar(30)  NOT NULL ,
    CONSTRAINT [PK_Payments] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Leads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstname] varchar(100)  NOT NULL ,
    [lastname] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] bigint  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [date] date  NOT NULL ,
    [compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Leads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories_Leads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [leadid] bigint  NOT NULL ,
    [categoryid] bigint  NOT NULL ,
    CONSTRAINT [PK_Categories_Leads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Deadlines] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [label] varchar(200)  NULL ,
    [duedate] date  NULL ,
    [active] bit  NULL ,
    CONSTRAINT [PK_Deadlines] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Budgets] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NULL ,
    [label] varchar(100)  NULL ,
    [rooms] int  NOT NULL ,
    [singlerooms] int  NULL ,
    [estimatedprice] int  NULL ,
    CONSTRAINT [PK_Budgets] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Budgets_Costs] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [fixed] bit  NOT NULL ,
    CONSTRAINT [PK_Budgets_Costs] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Budgets_Earnings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [fixed] bit  NOT NULL ,
    CONSTRAINT [PK_Budgets_Earnings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashBookings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [group] bit  NOT NULL ,
    [cancelled] bit  NOT NULL ,
    [cancelleddate] date  NULL ,
    [paydate1] date  NULL ,
    [paydat2] date  NOT NULL ,
    CONSTRAINT [PK_trashBookings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashReservations] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [roomid] bigint  NOT NULL ,
    [label] varchar(200)  NOT NULL ,
    CONSTRAINT [PK_trashReservations] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashCustomers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstname] varchar(100)  NOT NULL ,
    [lastname] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] int  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [personalnumber] varchar(10)  NULL ,
    [date] date  NOT NULL ,
    [category] varchar(60)  NULL ,
    [compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_trashCustomers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashBookings_Customers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [bookingid] bigint  NOT NULL ,
    [customerid] bigint  NOT NULL ,
    [roomid] bigint  NOT NULL ,
    [requests] varchar(360)  NULL ,
    [priceadjustment] bigint  NOT NULL ,
    [departurelocation] varchar(100)  NULL ,
    [departuretime] time  NULL ,
    [cancellationinsurance] bit  NULL ,
    CONSTRAINT [PK_trashBookings_Customers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashRooms] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [price] bigint  NOT NULL ,
    [size] int  NOT NULL ,
    [numberavaliable] int  NOT NULL ,
    CONSTRAINT [PK_trashRooms] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashTours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [insuranceprice] bigint  NOT NULL ,
    [reservationfeeprice] bigint  NOT NULL ,
    [departuredate] date NOT NULL ,
    CONSTRAINT [PK_trashTours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashCategories_Tours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [categoryid] bigint  NOT NULL ,
    CONSTRAINT [PK_trashCategories_Tours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashCategories] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(60)  NOT NULL ,
    [active] bit  NOT NULL ,
    CONSTRAINT [PK_trashCategories] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashPayments] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [bookingid] bigint  NOT NULL ,
    [customerid] bigint  NOT NULL ,
    [date] date  NOT NULL ,
    [sum] bigint  NOT NULL ,
    [insurancesum] bigint  NULL ,
    [method] varchar(30)  NOT NULL ,
    CONSTRAINT [PK_trashPayments] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashLeads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstname] varchar(100)  NOT NULL ,
    [lastname] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] bigint  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [date] date  NOT NULL ,
    [compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_trashLeads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashCategories_Leads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [leadid] bigint  NOT NULL ,
    [categoryid] bigint  NOT NULL ,
    CONSTRAINT [PK_trashCategories_Leads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashDeadlines] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NOT NULL ,
    [label] varchar(200)  NULL ,
    [duedate] date  NULL ,
    [active] bit  NULL ,
    CONSTRAINT [PK_trashDeadlines] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashBudgets] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourid] bigint  NULL ,
    [label] varchar(100)  NULL ,
    [rooms] int  NOT NULL ,
    [singlerooms] int  NULL ,
    [estimatedprice] int  NULL ,
    CONSTRAINT [PK_trashBudgets] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashBudgets_Costs] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [fixed] bit  NOT NULL ,
    CONSTRAINT [PK_trashBudgets_Costs] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trashBudgets_Earnings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetid] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [fixed] bit  NOT NULL ,
    CONSTRAINT [PK_trashBudgets_Earnings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

ALTER TABLE [Bookings] WITH CHECK ADD CONSTRAINT [FK_Bookings_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Bookings] CHECK CONSTRAINT [FK_Bookings_tourid]

ALTER TABLE [Reservations] WITH CHECK ADD CONSTRAINT [FK_Reservations_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Reservations] CHECK CONSTRAINT [FK_Reservations_tourid]

ALTER TABLE [Reservations] WITH CHECK ADD CONSTRAINT [FK_Reservations_roomid] FOREIGN KEY([roomid])
REFERENCES [Rooms] ([id])

ALTER TABLE [Reservations] CHECK CONSTRAINT [FK_Reservations_roomid]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_bookingid] FOREIGN KEY([bookingid])
REFERENCES [Bookings] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_bookingid]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_customerid] FOREIGN KEY([customerid])
REFERENCES [Customers] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_customerid]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_roomid] FOREIGN KEY([roomid])
REFERENCES [Rooms] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_roomid]

ALTER TABLE [Rooms] WITH CHECK ADD CONSTRAINT [FK_Rooms_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Rooms] CHECK CONSTRAINT [FK_Rooms_tourid]

ALTER TABLE [Categories_Tours] WITH CHECK ADD CONSTRAINT [FK_Categories_Tours_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Categories_Tours] CHECK CONSTRAINT [FK_Categories_Tours_tourid]

ALTER TABLE [Categories_Tours] WITH CHECK ADD CONSTRAINT [FK_Categories_Tours_categoryid] FOREIGN KEY([categoryid])
REFERENCES [Categories] ([id])

ALTER TABLE [Categories_Tours] CHECK CONSTRAINT [FK_Categories_Tours_categoryid]

ALTER TABLE [Payments] WITH CHECK ADD CONSTRAINT [FK_Payments_bookingid] FOREIGN KEY([bookingid])
REFERENCES [Bookings] ([id])

ALTER TABLE [Payments] CHECK CONSTRAINT [FK_Payments_bookingid]

ALTER TABLE [Payments] WITH CHECK ADD CONSTRAINT [FK_Payments_customerid] FOREIGN KEY([customerid])
REFERENCES [Customers] ([id])

ALTER TABLE [Payments] CHECK CONSTRAINT [FK_Payments_customerid]

ALTER TABLE [Categories_Leads] WITH CHECK ADD CONSTRAINT [FK_Categories_Leads_leadid] FOREIGN KEY([leadid])
REFERENCES [Leads] ([id])

ALTER TABLE [Categories_Leads] CHECK CONSTRAINT [FK_Categories_Leads_leadid]

ALTER TABLE [Categories_Leads] WITH CHECK ADD CONSTRAINT [FK_Categories_Leads_categoryid] FOREIGN KEY([categoryid])
REFERENCES [Categories] ([id])

ALTER TABLE [Categories_Leads] CHECK CONSTRAINT [FK_Categories_Leads_categoryid]

ALTER TABLE [Deadlines] WITH CHECK ADD CONSTRAINT [FK_Deadlines_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Deadlines] CHECK CONSTRAINT [FK_Deadlines_tourid]

ALTER TABLE [Budgets] WITH CHECK ADD CONSTRAINT [FK_Budgets_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Budgets] CHECK CONSTRAINT [FK_Budgets_tourid]

ALTER TABLE [Budgets_Costs] WITH CHECK ADD CONSTRAINT [FK_Budgets_Costs_budgetid] FOREIGN KEY([budgetid])
REFERENCES [Budgets] ([id])

ALTER TABLE [Budgets_Costs] CHECK CONSTRAINT [FK_Budgets_Costs_budgetid]

ALTER TABLE [Budgets_Earnings] WITH CHECK ADD CONSTRAINT [FK_Budgets_Earnings_budgetid] FOREIGN KEY([budgetid])
REFERENCES [Budgets] ([id])

ALTER TABLE [Budgets_Earnings] CHECK CONSTRAINT [FK_Budgets_Earnings_budgetid]

ALTER TABLE [trashBookings] WITH CHECK ADD CONSTRAINT [FK_trashBookings_tourid] FOREIGN KEY([tourid])
REFERENCES [trashTours] ([id])

ALTER TABLE [trashBookings] CHECK CONSTRAINT [FK_trashBookings_tourid]

ALTER TABLE [trashReservations] WITH CHECK ADD CONSTRAINT [FK_trashReservations_tourid] FOREIGN KEY([tourid])
REFERENCES [trashTours] ([id])

ALTER TABLE [trashReservations] CHECK CONSTRAINT [FK_trashReservations_tourid]

ALTER TABLE [trashReservations] WITH CHECK ADD CONSTRAINT [FK_trashReservations_roomid] FOREIGN KEY([roomid])
REFERENCES [trashRooms] ([id])

ALTER TABLE [trashReservations] CHECK CONSTRAINT [FK_trashReservations_roomid]

ALTER TABLE [trashBookings_Customers] WITH CHECK ADD CONSTRAINT [FK_trashBookings_Customers_bookingid] FOREIGN KEY([bookingid])
REFERENCES [trashBookings] ([id])

ALTER TABLE [trashBookings_Customers] CHECK CONSTRAINT [FK_trashBookings_Customers_bookingid]

ALTER TABLE [trashBookings_Customers] WITH CHECK ADD CONSTRAINT [FK_trashBookings_Customers_customerid] FOREIGN KEY([customerid])
REFERENCES [trashCustomers] ([id])

ALTER TABLE [trashBookings_Customers] CHECK CONSTRAINT [FK_trashBookings_Customers_customerid]

ALTER TABLE [trashBookings_Customers] WITH CHECK ADD CONSTRAINT [FK_trashBookings_Customers_roomid] FOREIGN KEY([roomid])
REFERENCES [Rooms] ([id])

ALTER TABLE [trashBookings_Customers] CHECK CONSTRAINT [FK_trashBookings_Customers_roomid]

ALTER TABLE [trashRooms] WITH CHECK ADD CONSTRAINT [FK_trashRooms_tourid] FOREIGN KEY([tourid])
REFERENCES [trashTours] ([id])

ALTER TABLE [trashRooms] CHECK CONSTRAINT [FK_trashRooms_tourid]

ALTER TABLE [trashCategories_Tours] WITH CHECK ADD CONSTRAINT [FK_trashCategories_Tours_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [trashCategories_Tours] CHECK CONSTRAINT [FK_trashCategories_Tours_tourid]

ALTER TABLE [trashCategories_Tours] WITH CHECK ADD CONSTRAINT [FK_trashCategories_Tours_categoryid] FOREIGN KEY([categoryid])
REFERENCES [trashCategories] ([id])

ALTER TABLE [trashCategories_Tours] CHECK CONSTRAINT [FK_trashCategories_Tours_categoryid]

ALTER TABLE [trashPayments] WITH CHECK ADD CONSTRAINT [FK_trashPayments_bookingid] FOREIGN KEY([bookingid])
REFERENCES [trashBookings] ([id])

ALTER TABLE [trashPayments] CHECK CONSTRAINT [FK_trashPayments_bookingid]

ALTER TABLE [trashPayments] WITH CHECK ADD CONSTRAINT [FK_trashPayments_customerid] FOREIGN KEY([customerid])
REFERENCES [trashCustomers] ([id])

ALTER TABLE [trashPayments] CHECK CONSTRAINT [FK_trashPayments_customerid]

ALTER TABLE [trashCategories_Leads] WITH CHECK ADD CONSTRAINT [FK_trashCategories_Leads_leadid] FOREIGN KEY([leadid])
REFERENCES [trashLeads] ([id])

ALTER TABLE [trashCategories_Leads] CHECK CONSTRAINT [FK_trashCategories_Leads_leadid]

ALTER TABLE [trashCategories_Leads] WITH CHECK ADD CONSTRAINT [FK_trashCategories_Leads_categoryid] FOREIGN KEY([categoryid])
REFERENCES [trashCategories] ([id])

ALTER TABLE [trashCategories_Leads] CHECK CONSTRAINT [FK_trashCategories_Leads_categoryid]

ALTER TABLE [trashDeadlines] WITH CHECK ADD CONSTRAINT [FK_trashDeadlines_tourid] FOREIGN KEY([tourid])
REFERENCES [trashTours] ([id])

ALTER TABLE [trashDeadlines] CHECK CONSTRAINT [FK_trashDeadlines_tourid]

ALTER TABLE [trashBudgets] WITH CHECK ADD CONSTRAINT [FK_trashBudgets_tourid] FOREIGN KEY([tourid])
REFERENCES [trashTours] ([id])

ALTER TABLE [trashBudgets] CHECK CONSTRAINT [FK_trashBudgets_tourid]

ALTER TABLE [trashBudgets_Costs] WITH CHECK ADD CONSTRAINT [FK_trashBudgets_Costs_budgetid] FOREIGN KEY([budgetid])
REFERENCES [trashBudgets] ([id])

ALTER TABLE [trashBudgets_Costs] CHECK CONSTRAINT [FK_trashBudgets_Costs_budgetid]

ALTER TABLE [trashBudgets_Earnings] WITH CHECK ADD CONSTRAINT [FK_trashBudgets_Earnings_budgetid] FOREIGN KEY([budgetid])
REFERENCES [trashBudgets] ([id])

ALTER TABLE [trashBudgets_Earnings] CHECK CONSTRAINT [FK_trashBudgets_Earnings_budgetid]

COMMIT TRANSACTION QUICKDBD