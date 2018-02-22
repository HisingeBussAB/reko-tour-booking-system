-- Exported from QuickDBD: https://www.quickdatatabasediagrams.com/
-- Link to schema: https://app.quickdatabasediagrams.com/#/schema/w9zPqM6P8UOAWzy4IzSEkQ
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.
DROP TABLE Bokning;
DROP TABLE Kategori_Resa;
DROP TABLE Kategori;
DROP TABLE Programbest;
DROP TABLE Kategori_Programbest;
DROP TABLE Deadline;
DROP TABLE Kalkyl;
DROP TABLE KalkylKostnad;
DROP TABLE KalkylIntakt;
DROP TABLE Kund;
DROP TABLE Bokning_Kund;
DROP TABLE Boende;
DROP TABLE Betalning;
DROP TABLE Resa;
DROP TABLE Reservation

SET XACT_ABORT ON

BEGIN TRANSACTION QUICKDBD

CREATE TABLE [Bokning] (
    [BokningID] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [GruppBokning] bit  NOT NULL ,
    [Makulerad] bit  NOT NULL ,
    [BetalningDatum1] date  NULL ,
    [BetalningDatum2] date  NOT NULL ,
    CONSTRAINT [PK_Bokning] PRIMARY KEY CLUSTERED (
        [BokningID] ASC
    )
)

CREATE TABLE [Reservation] (
    [ReservationID] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [BoendeID] bigint  NOT NULL ,
    [Reservation] varchar(200)  NOT NULL ,
    CONSTRAINT [PK_Reservation] PRIMARY KEY CLUSTERED (
        [ReservationID] ASC
    )
)

CREATE TABLE [Kund] (
    [KundID] bigint  NOT NULL ,
    [Fornamn] varchar(100)  NOT NULL ,
    [Efternamn] varchar(100)  NOT NULL ,
    [Gatuadress] varchar(100)  NULL ,
    [Postnr] int  NULL ,
    [Postort] varchar(100)  NULL ,
    [Telefon] varchar(60)  NULL ,
    [Email] varchar(60)  NULL ,
    [Personnr] varchar(10)  NULL ,
    [Datum] date  NOT NULL ,
    [Compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Kund] PRIMARY KEY CLUSTERED (
        [KundID] ASC
    )
)

CREATE TABLE [Bokning_Kund] (
    [id] bigint  NOT NULL ,
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

CREATE TABLE [Boende] (
    [BoendeID] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [BoendeNamn] varchar(100)  NOT NULL ,
    [Pris] bigint  NOT NULL ,
    [Personer] int  NOT NULL ,
    [AntalTillg] int  NOT NULL ,
    CONSTRAINT [PK_Boende] PRIMARY KEY CLUSTERED (
        [BoendeID] ASC
    )
)

CREATE TABLE [Resa] (
    [ResaID] bigint  NOT NULL ,
    [AvbskyddPris] bigint  NOT NULL ,
    [AnmavgPris] bigint  NOT NULL ,
    CONSTRAINT [PK_Resa] PRIMARY KEY CLUSTERED (
        [ResaID] ASC
    )
)

CREATE TABLE [Kategori_Resa] (
    [id] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [KategoriID] bigint  NOT NULL ,
    CONSTRAINT [PK_Kategori_Resa] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Kategori] (
    [KategoriID] bigint  NOT NULL ,
    [Kategori] varchar(60)  NOT NULL ,
    [Aktiv] bit  NOT NULL ,
    CONSTRAINT [PK_Kategori] PRIMARY KEY CLUSTERED (
        [KategoriID] ASC
    )
)

CREATE TABLE [Betalning] (
    [BetalningID] bigint  NOT NULL ,
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

CREATE TABLE [Programbest] (
    [ProgrambestID] bigint  NOT NULL ,
    [Fornamn] varchar(100)  NOT NULL ,
    [Efternamn] varchar(100)  NOT NULL ,
    [Gatuadress] varchar(100)  NULL ,
    [Postnr] bigint  NULL ,
    [Postort] varchar(100)  NULL ,
    [Telefon] varchar(60)  NULL ,
    [Email] varchar(60)  NULL ,
    [Datum] date  NOT NULL ,
    [Compare] nvarchar(300)  NOT NULL ,
    CONSTRAINT [PK_Programbest] PRIMARY KEY CLUSTERED (
        [ProgrambestID] ASC
    )
)

CREATE TABLE [Kategori_Programbest] (
    [id] bigint  NOT NULL ,
    [ProgrambestID] bigint  NOT NULL ,
    [KategoriID] bigint  NOT NULL ,
    CONSTRAINT [PK_Kategori_Programbest] PRIMARY KEY CLUSTERED (
        [id] ASC
    )
)

CREATE TABLE [Deadline] (
    [DeadlineID] bigint  NOT NULL ,
    [ResaID] bigint  NOT NULL ,
    [DeadlineNote] varchar(200)  NULL ,
    [Forfallodatum] date  NULL ,
    [Aktiv] bit  NULL ,
    CONSTRAINT [PK_Deadline] PRIMARY KEY CLUSTERED (
        [DeadlineID] ASC
    )
)

CREATE TABLE [Kalkyl] (
    [KalkylID] bigint  NOT NULL ,
    [ResaID] bigint  NULL ,
    [ResaNamn] varchar(100)  NULL ,
    [Antal] int  NOT NULL ,
    [AntalER] int  NULL ,
    [BeraknatPris] int  NULL ,
    CONSTRAINT [PK_Kalkyl] PRIMARY KEY CLUSTERED (
        [KalkylID] ASC
    )
)

CREATE TABLE [KalkylKostnad] (
    [KostnadID] bigint  NOT NULL ,
    [KalkylID] bigint  NOT NULL ,
    [Kostnad] varchar(100)  NOT NULL ,
    [Fixed] bit  NOT NULL ,
    CONSTRAINT [PK_KalkylKostnad] PRIMARY KEY CLUSTERED (
        [KostnadID] ASC
    )
)

CREATE TABLE [KalkylIntakt] (
    [IntaktID] bigint  NOT NULL ,
    [KalkylID] bigint  NOT NULL ,
    [Intakt] varchar(100)  NOT NULL ,
    [Fixed] bit  NOT NULL ,
    CONSTRAINT [PK_KalkylIntakt] PRIMARY KEY CLUSTERED (
        [IntaktID] ASC
    )
)

ALTER TABLE [Bokning] WITH CHECK ADD CONSTRAINT [FK_Bokning_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Bokning] CHECK CONSTRAINT [FK_Bokning_ResaID]

ALTER TABLE [Reservation] WITH CHECK ADD CONSTRAINT [FK_Reservation_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Reservation] CHECK CONSTRAINT [FK_Reservation_ResaID]

ALTER TABLE [Reservation] WITH CHECK ADD CONSTRAINT [FK_Reservation_BoendeID] FOREIGN KEY([BoendeID])
REFERENCES [Boende] ([BoendeID])

ALTER TABLE [Reservation] CHECK CONSTRAINT [FK_Reservation_BoendeID]

ALTER TABLE [Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_BokningID] FOREIGN KEY([BokningID])
REFERENCES [Bokning] ([BokningID])

ALTER TABLE [Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_BokningID]

ALTER TABLE [Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_KundID] FOREIGN KEY([KundID])
REFERENCES [Kund] ([KundID])

ALTER TABLE [Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_KundID]

ALTER TABLE [Bokning_Kund] WITH CHECK ADD CONSTRAINT [FK_Bokning_Kund_BoendeID] FOREIGN KEY([BoendeID])
REFERENCES [Boende] ([BoendeID])

ALTER TABLE [Bokning_Kund] CHECK CONSTRAINT [FK_Bokning_Kund_BoendeID]

ALTER TABLE [Boende] WITH CHECK ADD CONSTRAINT [FK_Boende_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Boende] CHECK CONSTRAINT [FK_Boende_ResaID]

ALTER TABLE [Kategori_Resa] WITH CHECK ADD CONSTRAINT [FK_Kategori_Resa_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Kategori_Resa] CHECK CONSTRAINT [FK_Kategori_Resa_ResaID]

ALTER TABLE [Kategori_Resa] WITH CHECK ADD CONSTRAINT [FK_Kategori_Resa_KategoriID] FOREIGN KEY([KategoriID])
REFERENCES [Kategori] ([KategoriID])

ALTER TABLE [Kategori_Resa] CHECK CONSTRAINT [FK_Kategori_Resa_KategoriID]

ALTER TABLE [Betalning] WITH CHECK ADD CONSTRAINT [FK_Betalning_BokningID] FOREIGN KEY([BokningID])
REFERENCES [Bokning] ([BokningID])

ALTER TABLE [Betalning] CHECK CONSTRAINT [FK_Betalning_BokningID]

ALTER TABLE [Betalning] WITH CHECK ADD CONSTRAINT [FK_Betalning_KundID] FOREIGN KEY([KundID])
REFERENCES [Kund] ([KundID])

ALTER TABLE [Betalning] CHECK CONSTRAINT [FK_Betalning_KundID]

ALTER TABLE [Kategori_Programbest] WITH CHECK ADD CONSTRAINT [FK_Kategori_Programbest_ProgrambestID] FOREIGN KEY([ProgrambestID])
REFERENCES [Programbest] ([ProgrambestID])

ALTER TABLE [Kategori_Programbest] CHECK CONSTRAINT [FK_Kategori_Programbest_ProgrambestID]

ALTER TABLE [Kategori_Programbest] WITH CHECK ADD CONSTRAINT [FK_Kategori_Programbest_KategoriID] FOREIGN KEY([KategoriID])
REFERENCES [Kategori] ([KategoriID])

ALTER TABLE [Kategori_Programbest] CHECK CONSTRAINT [FK_Kategori_Programbest_KategoriID]

ALTER TABLE [Deadline] WITH CHECK ADD CONSTRAINT [FK_Deadline_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Deadline] CHECK CONSTRAINT [FK_Deadline_ResaID]

ALTER TABLE [Kalkyl] WITH CHECK ADD CONSTRAINT [FK_Kalkyl_ResaID] FOREIGN KEY([ResaID])
REFERENCES [Resa] ([ResaID])

ALTER TABLE [Kalkyl] CHECK CONSTRAINT [FK_Kalkyl_ResaID]

ALTER TABLE [KalkylKostnad] WITH CHECK ADD CONSTRAINT [FK_KalkylKostnad_KalkylID] FOREIGN KEY([KalkylID])
REFERENCES [Kalkyl] ([KalkylID])

ALTER TABLE [KalkylKostnad] CHECK CONSTRAINT [FK_KalkylKostnad_KalkylID]

ALTER TABLE [KalkylIntakt] WITH CHECK ADD CONSTRAINT [FK_KalkylIntakt_KalkylID] FOREIGN KEY([KalkylID])
REFERENCES [Kalkyl] ([KalkylID])

ALTER TABLE [KalkylIntakt] CHECK CONSTRAINT [FK_KalkylIntakt_KalkylID]

COMMIT TRANSACTION QUICKDBD