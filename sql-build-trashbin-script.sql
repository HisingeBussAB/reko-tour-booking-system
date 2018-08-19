DROP TABLE [trash.Tokens]
DROP TABLE [trash.HammerGuard]
DROP TABLE [trash.Kund]
DROP TABLE [trash.Resa]
DROP TABLE [trash.Kategori]
DROP TABLE [trash.Deadline]
DROP TABLE [trash.Programbest]
DROP TABLE [trash.Betalning]
DROP TABLE [trash.Kategori_Programbest]
DROP TABLE [trash.Boende]
DROP TABLE [trash.Bokning]
DROP TABLE [trash.Reservation]
DROP TABLE [trash.KalkylIntakt]
DROP TABLE [trash.KalkylKostnad]
DROP TABLE [trash.Kalkyl]
DROP TABLE [trash.Kategori_Resa]
DROP TABLE [trash.Bokning_Kund]
DROP TABLE [trash.HammerGuard]
DROP TABLE [trash.Auth_Once]
DROP TABLE [trash.Auth]
DROP TABLE [trash.Test]


-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/schema/w9zPqM6P8UOAWzy4IzSEkQ
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


SET XACT_ABORT ON

BEGIN TRANSACTION QUICKDBD

CREATE TABLE [trash.HammerGuard] (
    [iphash] varchar(255)  NOT NULL ,
    [created] bigint  NOT NULL 
)

CREATE TABLE [trash.Auth_Once] (
    [userID] bigint  NOT NULL ,
    [tokenid] varchar(255)  NOT NULL ,
    [token] varchar(255)  NOT NULL ,
    [created] bigint  NOT NULL 
)

CREATE TABLE [trash.Tokens] (
    [Token] varchar(255)  NOT NULL ,
    [TokenType] varchar(255)  NOT NULL ,
    [Created] bigint  NOT NULL ,
    [username] varchar(255)  NULL 
)

CREATE TABLE [trash.Auth] (
    [AuthID] bigint IDENTITY(1,1) NOT NULL ,
    [user] varchar(255)  NOT NULL ,
    [pwd] varchar(255)  NOT NULL ,
    CONSTRAINT [PK_Auth] PRIMARY KEY CLUSTERED (
        [AuthID] ASC
    ),
    CONSTRAINT [UK_Auth_user] UNIQUE (
        [user]
    )
)

CREATE TABLE [trash.Bokning] (
    [BokningID] bigint IDENTITY(1,1) NOT NULL ,
    [BokningNr] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [GruppBokning] bit  NOT NULL ,
    [Makulerad] bit  NOT NULL ,
    [MakuleradDatum] date  NULL ,
    [BetalningDatum1] date  NULL ,
    [BetalningDatum2] date  NOT NULL ,
    CONSTRAINT [PK_Bokning] PRIMARY KEY CLUSTERED (
        [BokningID] ASC
    )
)

CREATE TABLE [trash.Reservation] (
    [ReservationID] bigint IDENTITY(1,1) NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [BoendeID] bigint  NOT NULL ,
    [Reservation] varchar(200)  NOT NULL ,
    CONSTRAINT [PK_Reservation] PRIMARY KEY CLUSTERED (
        [ReservationID] ASC
    )
)

CREATE TABLE [trash.Kund] (
    [KundID] bigint IDENTITY(1,1) NOT NULL ,
    [Fornamn] varchar(100)  NOT NULL ,
    [Efternamn] varchar(100)  NOT NULL ,
    [Gatuadress] varchar(100)  NULL ,
    [Postnr] int  NULL ,
    [Postort] varchar(100)  NULL ,
    [Telefon] varchar(25)  NULL ,
    [Email] varchar(60)  NULL ,
    [Personnr] varchar(10)  NULL ,
    [Datum] date NOT NULL ,
    [Kategori] varchar(60)  NULL ,
    [Compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Kund] PRIMARY KEY CLUSTERED (
        [KundID] ASC
    )
)

CREATE TABLE [trash.Bokning_Kund] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [BokningID] bigint  NOT NULL ,
    [KundID] bigint  NOT NULL ,
    [BoendeID] bigint  NOT NULL ,
    [Onskemal] varchar(360)  NULL ,
    [Prisjustering] bigint  NOT NULL ,
    [Avresa] varchar(100)  NULL ,
    [AvresaTid] time  NULL ,
    [AvbskyddBetalt] bit  NULL ,
    CONSTRAINT [PK_Bokning_Kund] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trash.Boende] (
    [BoendeID] bigint IDENTITY(1,1) NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [BoendeNamn] varchar(100)  NOT NULL ,
    [Pris] bigint  NOT NULL ,
    [Personer] int  NOT NULL ,
    [AntalTillg] int  NOT NULL ,
    CONSTRAINT [PK_Boende] PRIMARY KEY CLUSTERED (
        [BoendeID] ASC
    )
)

CREATE TABLE [trash.Resa] (
    [ResaID] bigint IDENTITY(1,1) NOT NULL ,
    [Resa] varchar(100)  NOT NULL ,
    [AvbskyddPris] bigint  NOT NULL ,
    [AnmavgPris] bigint  NOT NULL ,
    CONSTRAINT [PK_Resa] PRIMARY KEY CLUSTERED (
        [ResaID] ASC
    )
)

CREATE TABLE [trash.Kategori_Resa] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [KategoriID] bigint  NOT NULL ,
    CONSTRAINT [PK_Kategori_Resa] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trash.Kategori] (
    [KategoriID] bigint IDENTITY(1,1) NOT NULL ,
    [Kategori] varchar(60)  NOT NULL ,
    [Aktiv] bit  NOT NULL ,
    CONSTRAINT [PK_Kategori] PRIMARY KEY CLUSTERED (
        [KategoriID] ASC
    )
)

CREATE TABLE [trash.Betalning] (
    [BetalningID] bigint IDENTITY(1,1) NOT NULL ,
    [BetalningNr] bigint  NOT NULL ,
    [BokningID] bigint  NOT NULL ,
    [KundID] bigint  NOT NULL ,
    [Datum] date  NOT NULL ,
    [Summa] bigint  NOT NULL ,
    [AvbskyddSumma] bigint  NULL ,
    [Betalningsmetod] varchar(30)  NOT NULL ,
    CONSTRAINT [PK_Betalning] PRIMARY KEY CLUSTERED (
        [BetalningID] ASC
    )
)

CREATE TABLE [trash.Programbest] (
    [ProgrambestID] bigint IDENTITY(1,1) NOT NULL ,
    [Fornamn] varchar(100)  NOT NULL ,
    [Efternamn] varchar(100)  NOT NULL ,
    [Gatuadress] varchar(100)  NULL ,
    [Postnr] bigint  NULL ,
    [Postort] varchar(100)  NULL ,
    [Telefon] varchar(25)  NULL ,
    [Email] varchar(60)  NULL ,
    [Datum] date  NOT NULL ,
    [Compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Programbest] PRIMARY KEY CLUSTERED (
        [ProgrambestID] ASC
    )
)

CREATE TABLE [trash.Kategori_Programbest] (
    [id] bigint IDENTITY(1,1) NOT NULL ,
    [ProgrambestID] bigint  NOT NULL ,
    [KategoriID] bigint  NOT NULL ,
    CONSTRAINT [PK_Kategori_Programbest] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [trash.Deadline] (
    [DeadlineID] bigint IDENTITY(1,1) NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [DeadlineNote] varchar(200)  NULL ,
    [Forfallodatum] date  NULL ,
    [Aktiv] bit  NULL ,
    CONSTRAINT [PK_Deadline] PRIMARY KEY CLUSTERED (
        [DeadlineID] ASC
    )
)

CREATE TABLE [trash.Kalkyl] (
    [KalkylID] bigint IDENTITY(1,1) NOT NULL ,
    [ResaID] bigint  NULL ,
    [ResaNamn] varchar(100)  NULL ,
    [Antal] int  NOT NULL ,
    [AntalER] int  NULL ,
    [BeraknatPris] int  NULL ,
    CONSTRAINT [PK_Kalkyl] PRIMARY KEY CLUSTERED (
        [KalkylID] ASC
    )
)

CREATE TABLE [trash.KalkylKostnad] (
    [KostnadID] bigint IDENTITY(1,1) NOT NULL ,
    [KalkylID] bigint  NOT NULL ,
    [Kostnad] varchar(100)  NOT NULL ,
    [Fixed] bit  NOT NULL ,
    CONSTRAINT [PK_KalkylKostnad] PRIMARY KEY CLUSTERED (
        [KostnadID] ASC
    )
)

CREATE TABLE [trash.KalkylIntakt] (
    [IntaktID] bigint IDENTITY(1,1) NOT NULL ,
    [KalkylID] bigint  NOT NULL ,
    [Intakt] varchar(100)  NOT NULL ,
    [Fixed] bit  NOT NULL ,
    CONSTRAINT [PK_KalkylIntakt] PRIMARY KEY CLUSTERED (
        [IntaktID] ASC
    )
)

ALTER TABLE [trash.Auth_Once] WITH CHECK ADD CONSTRAINT [FK_Auth_Once_userID] FOREIGN KEY([userID])
REFERENCES [Auth] ([AuthID])

ALTER TABLE [trash.trash.Auth_Once] CHECK CONSTRAINT [FK_Auth_Once_userID]

ALTER TABLE [trash.trash.Bokning] WITH CHECK ADD CONSTRAINT [FK_Bokning_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.trash.Bokning] CHECK CONSTRAINT [FK_Bokning_ResaID]

ALTER TABLE [trash.trash.Reservation] WITH CHECK ADD CONSTRAINT [FK_Reservation_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.trash.Reservation] CHECK CONSTRAINT [FK_Reservation_ResaID]

ALTER TABLE [trash.trash.Reservation] WITH CHECK ADD CONSTRAINT [FK_Reservation_BoendeID] FOREIGN KEY([BoendeID])
REFERENCES [Boende] ([BoendeID])

ALTER TABLE [trash.Reservation] CHECK CONSTRAINT [FK_Reservation_BoendeID]

ALTER TABLE [trash.Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_BokningID] FOREIGN KEY([BokningID])
REFERENCES [Bokning] ([BokningID])

ALTER TABLE [trash.Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_BokningID]

ALTER TABLE [trash.Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_KundID] FOREIGN KEY([KundID])
REFERENCES [Kund] ([KundID])

ALTER TABLE [trash.Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_KundID]

ALTER TABLE [trash.Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_BoendeID] FOREIGN KEY([BoendeID])
REFERENCES [Boende] ([BoendeID])

ALTER TABLE [trash.Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_BoendeID]

ALTER TABLE [trash.Boende] WITH CHECK ADD CONSTRAINT [FK_Boende_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.Boende] CHECK CONSTRAINT [FK_Boende_ResaID]

ALTER TABLE [trash.Kategori_Resa] WITH CHECK ADD CONSTRAINT [FK_Kategori_Resa_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.Kategori_Resa] CHECK CONSTRAINT [FK_Kategori_Resa_ResaID]

ALTER TABLE [trash.Kategori_Resa] WITH CHECK ADD CONSTRAINT [FK_Kategori_Resa_KategoriID] FOREIGN KEY([KategoriID])
REFERENCES [Kategori] ([KategoriID])

ALTER TABLE [trash.Kategori_Resa] CHECK CONSTRAINT [FK_Kategori_Resa_KategoriID]

ALTER TABLE [trash.Betalning] WITH CHECK ADD CONSTRAINT [FK_Betalning_BokningID] FOREIGN KEY([BokningID])
REFERENCES [Bokning] ([BokningID])

ALTER TABLE [trash.Betalning] CHECK CONSTRAINT [FK_Betalning_BokningID]

ALTER TABLE [trash.Betalning] WITH CHECK ADD CONSTRAINT [FK_Betalning_KundID] FOREIGN KEY([KundID])
REFERENCES [Kund] ([KundID])

ALTER TABLE [trash.Betalning] CHECK CONSTRAINT [FK_Betalning_KundID]

ALTER TABLE [trash.Kategori_Programbest] WITH CHECK ADD CONSTRAINT [FK_Kategori_Programbest_ProgrambestID] FOREIGN KEY([ProgrambestID])
REFERENCES [Programbest] ([ProgrambestID])

ALTER TABLE [trash.Kategori_Programbest] CHECK CONSTRAINT [FK_Kategori_Programbest_ProgrambestID]

ALTER TABLE [trash.Kategori_Programbest] WITH CHECK ADD CONSTRAINT [FK_Kategori_Programbest_KategoriID] FOREIGN KEY([KategoriID])
REFERENCES [Kategori] ([KategoriID])

ALTER TABLE [trash.Kategori_Programbest] CHECK CONSTRAINT [FK_Kategori_Programbest_KategoriID]

ALTER TABLE [trash.Deadline] WITH CHECK ADD CONSTRAINT [FK_Deadline_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.Deadline] CHECK CONSTRAINT [FK_Deadline_ResaID]

ALTER TABLE [trash.Kalkyl] WITH CHECK ADD CONSTRAINT [FK_Kalkyl_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [trash.Kalkyl] CHECK CONSTRAINT [FK_Kalkyl_ResaID]

ALTER TABLE [trash.KalkylKostnad] WITH CHECK ADD CONSTRAINT [FK_KalkylKostnad_KalkylID] FOREIGN KEY([KalkylID])
REFERENCES [Kalkyl] ([KalkylID])

ALTER TABLE [trash.KalkylKostnad] CHECK CONSTRAINT [FK_KalkylKostnad_KalkylID]

ALTER TABLE [trash.KalkylIntakt] WITH CHECK ADD CONSTRAINT [FK_KalkylIntakt_KalkylID] FOREIGN KEY([KalkylID])
REFERENCES [Kalkyl] ([KalkylID])

ALTER TABLE [trash.KalkylIntakt] CHECK CONSTRAINT [FK_KalkylIntakt_KalkylID]

COMMIT TRANSACTION QUICKDBD