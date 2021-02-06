CREATE DATABASE Movie;
USE Movie;

CREATE TABLE MovieFact (MovieID INT NOT NULL IDENTITY (1,1),
MovieName varchar(255),
Distributor varchar(255),
ReleaseFromDate date,
ReleaseToDate date,
MPAA varchar(50),
Genres varchar(255),
WidestRelease int,
Budget float,
OpeningRevenue float,
DomesticRevenue float,
InternationalRevenue float,
WorldwideRevenue float,
CONSTRAINT MovieFact_PK PRIMARY KEY (MovieID));

select * from MovieFact;

/*If you want to reload the MovieFact table by rerunning the movie.php file then
first delete all records and then reset the seed to 0 so that auto increment of the PK again starts from 1.*/
delete from MovieFact;
DBCC CHECKIDENT (MovieFact, RESEED, 0);

/* Use the following queries if you have the data in a text file. 
First, create a MovieFactStaging schema WITHOUT the identity column.*/
CREATE TABLE MovieFactStaging (
MovieName varchar(255),
Distributor varchar(255),
ReleaseFromDate date,
ReleaseToDate date,
MPAA varchar(50),
Genres varchar(255),
WidestRelease int,
Budget float,
OpeningRevenue float,
DomesticRevenue float,
InternationalRevenue float,
WorldwideRevenue float
);

--Second, use BULK INSERT to populate the MovieFactStaging table by giving the correct path of the txt file
BULK INSERT MovieFactStaging FROM 'C:\xampp\htdocs\movie.txt' 
WITH (FIELDTERMINATOR = '|' , ROWTERMINATOR = '0x0a');

select * from MovieFactStaging;

--Third, copy the data from the staging table to the MovieFact table.
INSERT INTO MovieFact (MovieName, Distributor, ReleaseFromDate, ReleaseToDate, MPAA, Genres, WidestRelease, Budget, OpeningRevenue, DomesticRevenue, InternationalRevenue, WorldwideRevenue) 
   SELECT MovieName, Distributor, ReleaseFromDate, ReleaseToDate, MPAA, Genres, WidestRelease, Budget, OpeningRevenue, DomesticRevenue, InternationalRevenue, WorldwideRevenue
   FROM MovieFactStaging;

select * from MovieFact;