ALTER TABLE [dbo].[m_inventory_adjusts] ADD 
	[adjust_type] varchar(25) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD
	[quantity_box_from] int NOT NULL DEFAULT 0 ,
	[quantity_box_to] int NOT NULL DEFAULT 0 ,
	[m_product_id] int NULL ,
	[m_grid_id] int NULL  ,
	[pallet] varchar(255) NULL ,
	[barcode] varchar(255) NULL ,
	[carton_no] varchar(25) NULL ,
	[lot_no] varchar(50) NULL
GO

CREATE INDEX [m_inventory_adjustdetail_idx03] ON [dbo].[m_inventory_adjustdetails]
	([m_product_id] ASC) 
GO

CREATE INDEX [m_inventory_adjustdetail_idx04] ON [dbo].[m_inventory_adjustdetails]
	([m_grid_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD 
	CONSTRAINT [m_inventory_adjustdetails_fk03] FOREIGN KEY ([m_product_id]) REFERENCES [m_products] ([id])
GO

ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD 
	CONSTRAINT [m_inventory_adjustdetails_fk04] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id])
GO

