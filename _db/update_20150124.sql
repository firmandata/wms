ALTER TABLE [dbo].[c_businesspartners] ADD 
	[phone_no] varchar(100) NULL ,
	[fax_no] varchar(100) NULL ,
	[type] varchar(20) NULL ,
	[model] varchar(20) NULL ,
	[credit_limit] decimal(14,4) NOT NULL DEFAULT 0 ,
	[pic] varchar(150) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_products] ADD
	[uom] varchar(20) NULL ,
	[pack] int NOT NULL DEFAULT 0 ,
	[origin] varchar(100) NULL ,
	[netto] decimal(12,4) NOT NULL DEFAULT 0 ,
	[minimum_stock] decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE [dbo].[m_grids] ADD
	[type] varchar(20) NULL ,
	[length] decimal(12,4) NOT NULL DEFAULT 0 ,
	[width] decimal(12,4) NOT NULL DEFAULT 0 ,
	[height] decimal(12,4) NOT NULL DEFAULT 0 ,
	[status] varchar(20) NULL 
GO

CREATE TABLE [dbo].[c_projects] (
	[id] bigint NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[code] varchar(30) NOT NULL ,
	[name] varchar(255) NOT NULL ,
	[c_businesspartner_id] int NULL ,
	[category] varchar(20) NULL ,
	[pic] varchar(150) NULL ,
	PRIMARY KEY ([id]),
	CONSTRAINT [c_projects_fk01] FOREIGN KEY ([c_businesspartner_id]) REFERENCES [c_businesspartners] ([id])
)
GO

CREATE UNIQUE INDEX [c_projects_idx01] ON [dbo].[c_projects]
	([code] ASC) 
GO

CREATE INDEX [c_projects_idx02] ON [dbo].[c_projects]
	([c_businesspartner_id] ASC) 
GO

ALTER TABLE [dbo].[c_orderins] ADD
	[c_project_id] bigint NULL 
	[origin] varchar(100) NULL ,
	[bol_no] varchar(100) NULL ,
	[external_no] varchar(100) NULL ,
	[notes] nvarchar(500) NULL 
GO

CREATE INDEX [c_orderins_idx03] ON [dbo].[c_orderins]
	([c_project_id] ASC) 
GO

ALTER TABLE [dbo].[c_orderins] ADD 
	CONSTRAINT [c_orderins_fk02] FOREIGN KEY ([c_project_id]) REFERENCES [c_projects] ([id])
GO

ALTER TABLE [dbo].[c_orderindetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[notes] nvarchar(500) NULL
GO

ALTER TABLE [dbo].[m_inventory_receives] ADD
	[vehicle_no] varchar(50) NULL ,
	[vehicle_driver] varchar(150) NULL ,
	[transport_mode] varchar(20) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_receivedetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[condition] varchar(100) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_inbounds] ADD
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[barcode] varchar(255) NULL ,
	[carton_no] varchar(25) NULL ,
	[pallet] varchar(255) NULL ,
	[lot_no] varchar(50) NULL ,
	[packed_date] date NULL ,
	[expired_date] date NULL ,
	[condition] varchar(100) NULL ,
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD
	[m_grid_id] int NULL ,
	[expired_date] date NULL ,
	[condition] varchar(100) NULL 
GO

CREATE INDEX [m_inventory_inbounddetai_idx04] ON [dbo].[m_inventory_inbounddetails]
	([m_grid_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD 
	CONSTRAINT [m_inventory_inbounddetails_fk04] FOREIGN KEY ([m_grid_id]) REFERENCES [m_grids] ([id])
GO

ALTER TABLE [dbo].[cus_m_products] ADD
	[quantity_point_start] int NOT NULL DEFAULT 0 ,
	[quantity_point_end] int NOT NULL DEFAULT 0 
GO

ALTER TABLE [dbo].[m_inventories] ADD
	[quantity_allocated] decimal(12,4) NULL DEFAULT 0 ,
	[quantity_picked] decimal(12,4) NULL DEFAULT 0 ,
	[quantity_onhand] decimal(12,4) NULL DEFAULT 0 ,
	[quantity_box] int NOT NULL DEFAULT 0 ,
	[quantity_box_allocated] int NULL DEFAULT 0 ,
	[quantity_box_picked] int NULL DEFAULT 0 ,
	[quantity_box_onhand] int NULL DEFAULT 0 ,
	[expired_date] date NULL ,
	[condition] varchar(100) NULL 
GO

ALTER TABLE [dbo].[m_inventorylogs] ADD
	[log_type] varchar(35) NULL ,
	[quantity_box] int NOT NULL DEFAULT 0,
	[barcode] varchar(255) NULL ,
	[pallet] varchar(255) NULL ,
	[condition] varchar(100) NULL ,
	[ref1_code] varchar(50) NULL ,
	[ref2_code] varchar(50) NULL 
GO

ALTER TABLE [dbo].[m_inventory_putaways] ADD
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD
	[quantity_box_from] int NOT NULL DEFAULT 0 ,
	[quantity_box_to] int NOT NULL DEFAULT 0 ,
	[m_product_id] int NULL ,
	[pallet] varchar(255) NULL ,
	[barcode] varchar(255) NULL ,
	[carton_no] varchar(25) NULL ,
	[lot_no] varchar(50) NULL 
GO

CREATE INDEX [m_inventory_putawaydetai_idx05] ON [dbo].[m_inventory_putawaydetails]
	([m_product_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD 
	CONSTRAINT [m_inventory_putawaydetails_fk05] FOREIGN KEY ([m_product_id]) REFERENCES [m_products] ([id])
GO

ALTER TABLE [dbo].[m_inventory_moves] ADD
	[notes] nvarchar(500) NULL 
GO

ALTER TABLE [dbo].[m_inventory_movedetails] ADD
	[quantity_box_from] int NOT NULL DEFAULT 0 ,
	[quantity_box_to] int NOT NULL DEFAULT 0 ,
	[m_product_id] int NULL ,
	[pallet_from] varchar(255) NULL ,
	[pallet_to] varchar(255) NULL ,
	[barcode] varchar(255) NULL ,
	[carton_no] varchar(25) NULL ,
	[lot_no] varchar(50) NULL 
GO

CREATE INDEX [m_inventory_movedetails_idx05] ON [dbo].[m_inventory_movedetails]
	([m_product_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventory_movedetails] ADD 
	CONSTRAINT [m_inventory_movedetails_fk05] FOREIGN KEY ([m_product_id]) REFERENCES [m_products] ([id])
GO

ALTER TABLE [dbo].[c_orderouts]	ADD 
	[origin] varchar(100) NULL ,
	[marketing_unit] varchar(150) NULL ,
	[c_project_id] bigint NULL ,
	[external_no] varchar(100) NULL ,
	[request_arrive_date] date NULL ,
	[no_surat_jalan] varchar(100) NULL ,
	[notes] nvarchar(500) NULL 
GO

CREATE INDEX [c_orderouts_idx03] ON [dbo].[c_orderouts]
	([c_project_id] ASC) 
GO

ALTER TABLE [dbo].[c_orderouts] ADD
	CONSTRAINT [c_orderouts_fk02] FOREIGN KEY ([c_project_id]) REFERENCES [c_projects] ([id])
GO

