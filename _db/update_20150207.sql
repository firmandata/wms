ALTER TABLE [dbo].[m_inventory_picklists] ADD
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_picklistdetails] 
	ALTER COLUMN [notes] nvarchar(500)
GO

ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD
	[m_product_id] int NULL ,
	[pallet] varchar(255) NULL ,
	[barcode] varchar(255) NULL ,
	[carton_no] varchar(25) NULL ,
	[lot_no] varchar(50) NULL ,
	[condition] varchar(100) NULL 
GO

CREATE INDEX [m_inventory_picklistdeta_idx05] ON [dbo].[m_inventory_picklistdetails]
	([m_product_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD 
	CONSTRAINT [m_inventory_picklistdetails_fk05] FOREIGN KEY ([m_product_id]) REFERENCES [m_products] ([id])
GO

ALTER TABLE [dbo].[m_inventory_pickings] ADD 
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_shipments] ADD
	[shipment_type] varchar(100) NULL ,
	[request_arrive_date] date NULL ,
	[estimated_time_arrive] datetime NULL ,
	[shipment_to] varchar(255) NULL ,
	[vehicle_no] varchar(50) NULL ,
	[vehicle_driver] varchar(150) NULL ,
	[transport_mode] varchar(20) NULL ,
	[police_name] varchar(150) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[notes] nvarchar(500) NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_allocated] = 0 WHERE [quantity_allocated] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_allocated] decimal(12,4) NOT NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_picked] = 0 WHERE [quantity_picked] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_picked] decimal(12,4) NOT NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_onhand] = 0 WHERE [quantity_onhand] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_onhand] decimal(12,4) NOT NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_box_allocated] = 0 WHERE [quantity_box_allocated] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_box_allocated] int NOT NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_box_picked] = 0 WHERE [quantity_box_picked] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_box_picked] int NOT NULL 
GO

UPDATE [dbo].[m_inventories] SET [quantity_box_onhand] = 0 WHERE [quantity_box_onhand] IS NULL
GO

ALTER TABLE [dbo].[m_inventories] 
	ALTER COLUMN [quantity_box_onhand] int NOT NULL 
GO

ALTER TABLE [dbo].[m_inventorylogs] ADD
	[quantity_allocated] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_picked] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_onhand] decimal(12,4) NOT NULL DEFAULT 0 ,
	[quantity_box_allocated] int NOT NULL DEFAULT 0 ,
	[quantity_box_picked] int NOT NULL DEFAULT 0 ,
	[quantity_box_onhand] int NOT NULL DEFAULT 0 
GO
