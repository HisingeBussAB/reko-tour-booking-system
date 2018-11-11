
DROP TABLE [HammerGuard]
DROP TABLE [Tokens]
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


-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/d/w9zPqM
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/d/w9zPqM
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
 --   [user] varchar(255)  NOT NULL ,
--    [pwd] varchar(255)  NOT NULL ,
 --   CONSTRAINT [PK_Auth] PRIMARY KEY CLUSTERED (
 --       [id] ASC
 --   ),
--   CONSTRAINT [UK_Auth_user] UNIQUE (
 --       [user]
--    )
--)

CREATE TABLE [Bookings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [tourId] bigint  NOT NULL ,
    [group] bit  NOT NULL ,
    [cancelled] bit  NOT NULL ,
    [cancelledDate] date  NULL ,
    [payDate1] date  NULL ,
    [payDate2] date  NOT NULL ,
    CONSTRAINT [PK_Bookings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Reservations] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourId] bigint  NOT NULL ,
    [roomId] bigint  NOT NULL ,
    [label] varchar(200)  NOT NULL ,
    CONSTRAINT [PK_Reservations] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Customers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstName] varchar(100)  NOT NULL ,
    [lastName] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] int  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [personalNumber] char(11)  NULL ,
    [date] date  NOT NULL ,
    [category] varchar(60)  NULL ,
    [compare] nchar(200)  NOT NULL ,
    CONSTRAINT [PK_Customers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Bookings_Customers] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [bookingId] bigint  NOT NULL ,
    [customerId] bigint  NOT NULL ,
    [roomId] bigint  NOT NULL ,
    [requests] varchar(360)  NULL ,
    [priceAdjustment] money  NOT NULL ,
    [departureLocation] varchar(100)  NULL ,
    [departureTime] time  NULL ,
    [cancellationInsurance] bit  NULL ,
    CONSTRAINT [PK_Bookings_Customers] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Rooms] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourId] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [price] money  NOT NULL ,
    [size] int  NOT NULL ,
    [numberAvaliable] int  NOT NULL ,
    CONSTRAINT [PK_Rooms] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Tours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [insurancePrice] money  NOT NULL ,
    [reservationFeePrice] money  NOT NULL ,
    [departureDate] date  NOT NULL ,
    [isDeleted] bit  NOT NULL ,
    [isDisabled] bit  NOT NULL ,
    CONSTRAINT [PK_Tours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories_Tours] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourId] bigint  NOT NULL ,
    [categoryId] bigint  NOT NULL ,
    CONSTRAINT [PK_Categories_Tours] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [label] varchar(60)  NOT NULL ,
    [isDeleted] bit  NOT NULL ,
    [isDisabled] bit  NOT NULL ,
    CONSTRAINT [PK_Categories] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Payments] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [number] bigint  NOT NULL ,
    [bookingId] bigint  NOT NULL ,
    [customerId] bigint  NOT NULL ,
    [date] date  NOT NULL ,
    [Amount] money  NOT NULL ,
    [insuranceAmount] money  NULL ,
    [method] varchar(30)  NOT NULL ,
    CONSTRAINT [PK_Payments] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Leads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [firstName] varchar(100)  NOT NULL ,
    [lastName] varchar(100)  NOT NULL ,
    [street] varchar(100)  NULL ,
    [zip] bigint  NULL ,
    [city] varchar(100)  NULL ,
    [phone] varchar(25)  NULL ,
    [email] varchar(60)  NULL ,
    [date] date  NOT NULL ,
    [compare] nchar(200)  NOT NULL ,
    CONSTRAINT [PK_Leads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Categories_Leads] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [leadId] bigint  NOT NULL ,
    [categoryId] bigint  NOT NULL ,
    CONSTRAINT [PK_Categories_Leads] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Deadlines] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [tourId] bigint  NOT NULL ,
    [label] varchar(200)  NULL ,
    [duedate] date  NULL ,
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
    [isDeleted] bit  NOT NULL ,
    [isDisabled] bit  NOT NULL ,
    CONSTRAINT [PK_Budgets] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Budgets_Costs] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetId] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [amount] money  NOT NULL ,
    [isFixed] bit  NOT NULL ,
    CONSTRAINT [PK_Budgets_Costs] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Budgets_Earnings] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [budgetId] bigint  NOT NULL ,
    [label] varchar(100)  NOT NULL ,
    [amount] money  NOT NULL ,
    [isFixed] bit  NOT NULL ,
    CONSTRAINT [PK_Budgets_Earnings] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

ALTER TABLE [Bookings] WITH CHECK ADD CONSTRAINT [FK_Bookings_tourId] FOREIGN KEY([tourId])
REFERENCES [Tours] ([id])

ALTER TABLE [Bookings] CHECK CONSTRAINT [FK_Bookings_tourId]

ALTER TABLE [Reservations] WITH CHECK ADD CONSTRAINT [FK_Reservations_tourId] FOREIGN KEY([tourId])
REFERENCES [Tours] ([id])

ALTER TABLE [Reservations] CHECK CONSTRAINT [FK_Reservations_tourId]

ALTER TABLE [Reservations] WITH CHECK ADD CONSTRAINT [FK_Reservations_roomId] FOREIGN KEY([roomId])
REFERENCES [Rooms] ([id])

ALTER TABLE [Reservations] CHECK CONSTRAINT [FK_Reservations_roomId]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_bookingId] FOREIGN KEY([bookingId])
REFERENCES [Bookings] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_bookingId]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_customerId] FOREIGN KEY([customerId])
REFERENCES [Customers] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_customerId]

ALTER TABLE [Bookings_Customers] WITH CHECK ADD CONSTRAINT [FK_Bookings_Customers_roomId] FOREIGN KEY([roomId])
REFERENCES [Rooms] ([id])

ALTER TABLE [Bookings_Customers] CHECK CONSTRAINT [FK_Bookings_Customers_roomId]

ALTER TABLE [Rooms] WITH CHECK ADD CONSTRAINT [FK_Rooms_tourId] FOREIGN KEY([tourId])
REFERENCES [Tours] ([id])

ALTER TABLE [Rooms] CHECK CONSTRAINT [FK_Rooms_tourId]

ALTER TABLE [Categories_Tours] WITH CHECK ADD CONSTRAINT [FK_Categories_Tours_tourId] FOREIGN KEY([tourId])
REFERENCES [Tours] ([id])

ALTER TABLE [Categories_Tours] CHECK CONSTRAINT [FK_Categories_Tours_tourId]

ALTER TABLE [Categories_Tours] WITH CHECK ADD CONSTRAINT [FK_Categories_Tours_categoryId] FOREIGN KEY([categoryId])
REFERENCES [Categories] ([id])

ALTER TABLE [Categories_Tours] CHECK CONSTRAINT [FK_Categories_Tours_categoryId]

ALTER TABLE [Payments] WITH CHECK ADD CONSTRAINT [FK_Payments_bookingId] FOREIGN KEY([bookingId])
REFERENCES [Bookings] ([id])

ALTER TABLE [Payments] CHECK CONSTRAINT [FK_Payments_bookingId]

ALTER TABLE [Categories_Leads] WITH CHECK ADD CONSTRAINT [FK_Categories_Leads_leadId] FOREIGN KEY([leadId])
REFERENCES [Leads] ([id])

ALTER TABLE [Categories_Leads] CHECK CONSTRAINT [FK_Categories_Leads_leadId]

ALTER TABLE [Categories_Leads] WITH CHECK ADD CONSTRAINT [FK_Categories_Leads_categoryId] FOREIGN KEY([categoryId])
REFERENCES [Categories] ([id])

ALTER TABLE [Categories_Leads] CHECK CONSTRAINT [FK_Categories_Leads_categoryId]

ALTER TABLE [Deadlines] WITH CHECK ADD CONSTRAINT [FK_Deadlines_tourId] FOREIGN KEY([tourId])
REFERENCES [Tours] ([id])

ALTER TABLE [Deadlines] CHECK CONSTRAINT [FK_Deadlines_tourId]

ALTER TABLE [Budgets] WITH CHECK ADD CONSTRAINT [FK_Budgets_tourid] FOREIGN KEY([tourid])
REFERENCES [Tours] ([id])

ALTER TABLE [Budgets] CHECK CONSTRAINT [FK_Budgets_tourid]

ALTER TABLE [Budgets_Costs] WITH CHECK ADD CONSTRAINT [FK_Budgets_Costs_budgetId] FOREIGN KEY([budgetId])
REFERENCES [Budgets] ([id])

ALTER TABLE [Budgets_Costs] CHECK CONSTRAINT [FK_Budgets_Costs_budgetId]

ALTER TABLE [Budgets_Earnings] WITH CHECK ADD CONSTRAINT [FK_Budgets_Earnings_budgetId] FOREIGN KEY([budgetId])
REFERENCES [Budgets] ([id])

ALTER TABLE [Budgets_Earnings] CHECK CONSTRAINT [FK_Budgets_Earnings_budgetId]

COMMIT TRANSACTION QUICKDBD