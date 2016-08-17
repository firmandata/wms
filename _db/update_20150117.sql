ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD
	[m_grid_id] int NULL ,
	[lot_no] varchar(50) NULL 
GO

ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD 
	CONSTRAINT [cus_m_inventory_inbounddetails_fk03] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id])
GO

CREATE INDEX [cus_m_inventory_inboundd_idx04] ON [dbo].[cus_m_inventory_inbounddetails]
	([m_grid_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventories]
ADD [lot_no] varchar(50) NULL 
GO
