ALTER TABLE [dbo].[c_orderins] ADD
	[status_inventory_receive] varchar(50) NULL 
GO

ALTER TABLE [dbo].[c_orderindetails] ADD
	[status_inventory_receive] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_receives] ADD
	[status_inventory_inbound] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_receivedetails] ADD
	[status_inventory_inbound] varchar(50) NULL 
GO

ALTER TABLE [dbo].[c_orderouts] ADD
	[status_inventory_picklist] varchar(50) NULL 
GO

ALTER TABLE [dbo].[c_orderoutdetails] ADD
	[status_inventory_picklist] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_picklists] ADD
	[status_inventory_picking] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD
	[status_inventory_picking] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_pickings] ADD
	[status_inventory_shipment] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD
	[status_inventory_shipment] varchar(50) NULL 
GO

