IF OBJECT_ID ('cus_c_orderindetail_m_grids', 'U') IS NOT NULL
    DROP TABLE cus_c_orderindetail_m_grids;
GO

CREATE TABLE [dbo].[cus_m_inventory_forecasts] (
	[id] bigint NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[code] varchar(50) NOT NULL ,
	[forecast_num] int NOT NULL DEFAULT 0 ,
	[forecast_date] date NOT NULL ,
	PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [cus_m_inventory_forecast_idx01] ON [dbo].[cus_m_inventory_forecasts]
	([code] ASC) 
GO

CREATE TABLE [dbo].[cus_m_inventory_forecastdetails] (
	[id] bigint NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[cus_m_inventory_forecast_id] bigint NOT NULL ,
	[m_inventory_receivedetail_id] bigint NOT NULL ,
	[m_grid_id] int NULL ,
	[quantity] decimal(12,4) NOT NULL DEFAULT 0 ,
	PRIMARY KEY ([id]),
	CONSTRAINT [cus_m_inventory_forecastdetails_fk01] FOREIGN KEY ([cus_m_inventory_forecast_id]) REFERENCES [cus_m_inventory_forecasts] ([id]),
	CONSTRAINT [cus_m_inventory_forecastdetails_fk02] FOREIGN KEY ([m_inventory_receivedetail_id]) REFERENCES [m_inventory_receivedetails] ([id]),
	CONSTRAINT [cus_m_inventory_forecastdetails_fk03] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id])
)
GO

CREATE INDEX [cus_m_inventory_forecasd_idx01] ON [dbo].[cus_m_inventory_forecastdetails]
	([cus_m_inventory_forecast_id] ASC) 
GO

CREATE INDEX [cus_m_inventory_forecasd_idx02] ON [dbo].[cus_m_inventory_forecastdetails]
	([m_inventory_receivedetail_id] ASC) 
GO

CREATE INDEX [cus_m_inventory_forecasd_idx03] ON [dbo].[cus_m_inventory_forecastdetails]
	([m_grid_id] ASC) 
GO
