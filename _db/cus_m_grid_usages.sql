CREATE TABLE [dbo].[cus_m_grid_usages] (
	[id] int NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[m_grid_id] int NOT NULL ,
	[quantity] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[quantity_allocated] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_box_allocated] int NOT NULL DEFAULT 0 ,
	[quantity_picked] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_box_picked] int NOT NULL DEFAULT 0 ,
	[quantity_onhand] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_box_onhand] int NOT NULL DEFAULT 0 ,
	[is_forecast_request] bit NOT NULL DEFAULT 0 ,
	PRIMARY KEY ([id]),
	CONSTRAINT [cus_m_grid_usages_fk01] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
)

GO

CREATE INDEX [cus_m_grid_usages_idx01] ON [dbo].[cus_m_grid_usages]
([m_grid_id] ASC) 
GO

