CREATE TABLE [dbo].[m_inventory_holds](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[hold_date] [date] NOT NULL,
	[notes] [nvarchar](500) NULL,
	PRIMARY KEY ([id])
)
GO

CREATE TABLE [dbo].[m_inventory_holddetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_hold_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[m_product_id] [int] NULL,
	[m_grid_id] [int] NULL,
	[pallet] [varchar](255) NULL,
	[barcode] [varchar](255) NULL,
	[carton_no] [varchar](25) NULL,
	[lot_no] [varchar](50) NULL,
	[quantity_from] [decimal](12, 4) NOT NULL DEFAULT 0,
	[quantity_to] [decimal](12, 4) NOT NULL DEFAULT 0,
	[quantity_box_from] [int] NOT NULL DEFAULT 0,
	[quantity_box_to] [int] NOT NULL DEFAULT 0,
	[is_hold] bit NOT NULL DEFAULT 0,
	PRIMARY KEY ([id]),
	CONSTRAINT [m_inventory_holddetails_fk01] FOREIGN KEY([m_inventory_hold_id]) REFERENCES [dbo].[m_inventory_holds] ([id]),
	CONSTRAINT [m_inventory_holddetails_fk02] FOREIGN KEY([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]),
	CONSTRAINT [m_inventory_holddetails_fk03] FOREIGN KEY([m_product_id]) REFERENCES [dbo].[m_products] ([id]),
	CONSTRAINT [m_inventory_holddetails_fk04] FOREIGN KEY([m_grid_id]) REFERENCES [dbo].[m_grids] ([id])
)
GO

ALTER TABLE [dbo].[c_orderoutdetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[notes] nvarchar NULL 
GO

