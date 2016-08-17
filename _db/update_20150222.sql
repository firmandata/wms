ALTER TABLE [dbo].[m_products] ADD
	[type] varchar(50) NULL 
GO

DROP INDEX [cus_m_inventory_inboundd_idx03] ON [dbo].[cus_m_inventory_inbounddetails]
GO

CREATE TABLE [dbo].[cus_c_orderindetail_m_grids] (
	[id] bigint NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[c_orderindetail_id] bigint NOT NULL ,
	[m_grid_id] int NULL ,
	[quantity] decimal(12,4) NOT NULL DEFAULT 0 ,
	PRIMARY KEY ([id]),
	CONSTRAINT [cus_c_orderindetail_m_grids_fk01] FOREIGN KEY ([c_orderindetail_id]) REFERENCES [c_orderindetails] ([id]),
	CONSTRAINT [cus_c_orderindetail_m_grids_fk02] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id])
)
GO

CREATE INDEX [cus_c_orderindetail_m_gr_idx01] ON [dbo].[cus_c_orderindetail_m_grids]
	([c_orderindetail_id] ASC) 
GO

CREATE INDEX [cus_c_orderindetail_m_gr_idx02] ON [dbo].[cus_c_orderindetail_m_grids]
	([m_grid_id] ASC) 
GO

