CREATE DATABASE Movie;
USE Movie;

Create table DimMovie( MovieKey INT NOT NULL IDENTITY (1,1),
title varchar(255),
certificate varchar(255),
runtime int,
genres varchar(255),
rating float(2),
CONSTRAINT DimMovie_PK PRIMARY KEY (MovieKey));

CREATE TABLE DimDate(Date Date, 
                      Year INT, 
					  Month INT, 
					  Day INT, 
					  WeekDayValue INT, 
					  MonthValueName VARCHAR(20),
					  WeekDayValueName VARCHAR(20),
CONSTRAINT DimDate_PK PRIMARY KEY (Date));

  DECLARE @StartDate DATE = '2019-01-01';
  DECLARE @EndDate DATE = '2019-12-31';
  WHILE @StartDate <= @EndDate
  BEGIN
	INSERT INTO  DimDate(Date,
	                     Year, 
						 Month, 
						 Day, 
						 WeekDayValue, 
						 MonthValueName,
						 WeekDayValueName )
	VALUES(@StartDate,
	        DATEPART(YY, @StartDate),
			DATEPART(mm, @StartDate),
			DATEPART(dd, @StartDate), 
			DATEPART(dw, @StartDate), 
			DATENAME(mm, @StartDate),
			DATENAME(dw, @StartDate))
	
	SET @StartDate = DATEADD(dd, 1, @StartDate)
  END;

Create table FactMovie(Rowkey INT NOT NULL IDENTITY (1,1),
MovieKey INT,
Date date,
Day int,
title varchar(255),
Top10Gross int,
Top1Gross int,
CONSTRAINT FactMovie_PK PRIMARY KEY (Rowkey),
CONSTRAINT FactMovie_FK1 Foreign Key (Date) references DimDate(Date),
CONSTRAINT FactMovie_FK2 Foreign Key (MovieKey) references DimMovie(MovieKey));


select * from DimMovie;
select * from FactMovie;
select * from DimDate;

use Movie; 
go

create view Rating1 
AS
select [dbo].[DimMovie].title as MovieTitle,
rating as IMDbRating, [dbo].[FactMovie].Day as NoOfDays
from [dbo].[DimMovie] 
Inner Join [dbo].[FactMovie] on [dbo].[DimMovie].title = [dbo].[FactMovie].title;

select * from Rating1 order by MovieTitle;


delete from [dbo].[DimMovie];
delete from [dbo].[FactMovie];