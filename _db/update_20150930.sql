DROP TABLE [dbo].[m_product_categories]
GO

DROP TABLE [dbo].[m_categories]
GO

DROP TABLE [dbo].[c_locations]
GO

DROP TABLE [dbo].[c_departments]
GO

DROP TABLE [dbo].[c_companies]
GO

DROP TABLE [dbo].[a_asset_movedetails]
GO

DROP TABLE [dbo].[a_asset_moves]
GO

DROP TABLE [dbo].[a_asset_transferdetails]
GO

DROP TABLE [dbo].[a_asset_transfers]
GO

DROP TABLE [dbo].[a_assetamounts]
GO

DROP TABLE [dbo].[a_assets]
GO

-- ----------------------------
-- Table structure for [dbo].[m_categories]
-- ----------------------------
CREATE TABLE [dbo].[m_categories] (
[id] int NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(15) NOT NULL ,
[name] varchar(255) NOT NULL 
)

GO

-- ----------------------------
-- Table structure for [dbo].[m_product_categories]
-- ----------------------------
CREATE TABLE [dbo].[m_product_categories] (
[id] int NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_product_id] int NOT NULL ,
[m_category_id] int NOT NULL 
)

GO

-- ----------------------------
-- Table structure for [dbo].[c_companies]
-- ----------------------------
CREATE TABLE [dbo].[c_companies] (
[id] int NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(15) NOT NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[c_departments]
-- ----------------------------
CREATE TABLE [dbo].[c_departments] (
[id] int NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(15) NOT NULL ,
[name] varchar(255) NOT NULL 
)

GO

-- ----------------------------
-- Table structure for [dbo].[a_asset_moves]
-- ----------------------------
CREATE TABLE [dbo].[a_asset_moves] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[move_date] date NOT NULL ,
[notes] nvarchar(500) NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[c_locations]
-- ----------------------------
CREATE TABLE [dbo].[c_locations] (
[id] int NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(15) NOT NULL ,
[name] varchar(255) NOT NULL ,
[c_company_id] int NOT NULL ,
[c_department_id] int NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[a_asset_movedetails]
-- ----------------------------
CREATE TABLE [dbo].[a_asset_movedetails] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[a_asset_move_id] bigint NOT NULL ,
[a_asset_id] bigint NOT NULL ,
[c_locationfrom_id] int NOT NULL ,
[c_locationto_id] int NOT NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[a_asset_transfers]
-- ----------------------------
CREATE TABLE [dbo].[a_asset_transfers] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[transfer_date] date NOT NULL ,
[notes] nvarchar(500) NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[c_businesspartners]
-- ----------------------------
ALTER TABLE [dbo].[c_businesspartners] ADD
[c_department_id] int NULL ,
[c_company_id] int NULL 


GO

-- ----------------------------
-- Table structure for [dbo].[a_asset_transferdetails]
-- ----------------------------
CREATE TABLE [dbo].[a_asset_transferdetails] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[a_asset_transfer_id] bigint NOT NULL ,
[a_asset_id] bigint NOT NULL ,
[c_businesspartner_ownerfrom_id] int NULL ,
[c_businesspartner_ownerto_id] int NULL 
)


GO

-- ----------------------------
-- Table structure for [dbo].[a_assets]
-- ----------------------------
CREATE TABLE [dbo].[a_assets] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_product_id] int NOT NULL ,
[c_location_id] int NOT NULL ,
[c_businesspartner_supplier_id] int NULL ,
[c_businesspartner_owner_id] int NULL ,
[code] varchar(100) NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[name] varchar(255) NULL ,
[notes] nvarchar(500) NULL ,
[purchase_date] date NOT NULL ,
[purchase_price] decimal(16,2) NOT NULL DEFAULT ((0)) ,
[currency] char(3) NOT NULL ,
[depreciation_period_type] varchar(10) NULL ,
[depreciation_period_time] int NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Table structure for [dbo].[a_assetamounts]
-- ----------------------------
CREATE TABLE [dbo].[a_assetamounts] (
[id] bigint NOT NULL IDENTITY(1,1),
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[a_asset_id] bigint NOT NULL ,
[depreciated_date] date NOT NULL ,
[book_value] decimal(16,2) NOT NULL DEFAULT ((0)) ,
[market_value] decimal(16,2) NOT NULL ,
[depreciation_accumulated] decimal(16,2) NOT NULL DEFAULT ((0)) ,
[depreciated_value] decimal(16,2) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_categories]
-- ----------------------------
CREATE UNIQUE INDEX [m_categories_idx01] ON [dbo].[m_categories]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_categories]
-- ----------------------------
ALTER TABLE [dbo].[m_categories] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_product_categories]
-- ----------------------------
CREATE INDEX [m_product_categories_idx01] ON [dbo].[m_product_categories]
([m_product_id] ASC) 
GO
CREATE INDEX [m_product_categories_idx02] ON [dbo].[m_product_categories]
([m_category_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_product_categories]
-- ----------------------------
ALTER TABLE [dbo].[m_product_categories] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_companies]
-- ----------------------------
CREATE UNIQUE INDEX [c_companies_idx01] ON [dbo].[c_companies]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_companies]
-- ----------------------------
ALTER TABLE [dbo].[c_companies] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_departments]
-- ----------------------------
CREATE UNIQUE INDEX [c_departments_idx01] ON [dbo].[c_departments]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_departments]
-- ----------------------------
ALTER TABLE [dbo].[c_departments] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_asset_moves]
-- ----------------------------
CREATE UNIQUE INDEX [a_asset_moves_idx01] ON [dbo].[a_asset_moves]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[a_asset_moves]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_moves] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_locations]
-- ----------------------------
CREATE UNIQUE INDEX [c_locations_idx01] ON [dbo].[c_locations]
([code] ASC) 
GO
CREATE INDEX [c_locations_idx02] ON [dbo].[c_locations]
([c_company_id] ASC) 
GO
CREATE INDEX [c_locations_idx03] ON [dbo].[c_locations]
([c_department_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_locations]
-- ----------------------------
ALTER TABLE [dbo].[c_locations] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_asset_movedetails]
-- ----------------------------
CREATE INDEX [a_asset_movedetail_idx01] ON [dbo].[a_asset_movedetails]
([a_asset_move_id] ASC) 
GO
CREATE INDEX [a_asset_movedetail_idx02] ON [dbo].[a_asset_movedetails]
([a_asset_id] ASC) 
GO
CREATE INDEX [a_asset_movedetail_idx03] ON [dbo].[a_asset_movedetails]
([c_locationfrom_id] ASC) 
GO
CREATE INDEX [a_asset_movedetail_idx04] ON [dbo].[a_asset_movedetails]
([c_locationto_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[a_asset_movedetails]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_movedetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_asset_transfers]
-- ----------------------------
CREATE UNIQUE INDEX [a_asset_transfers_idx01] ON [dbo].[a_asset_transfers]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[a_asset_transfers]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_transfers] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_businesspartners]
-- ----------------------------
CREATE INDEX [c_businesspartners_idx02] ON [dbo].[c_businesspartners]
([c_department_id] ASC) 
GO
CREATE INDEX [c_businesspartners_idx03] ON [dbo].[c_businesspartners]
([c_company_id] ASC) 
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_asset_transferdetails]
-- ----------------------------
CREATE INDEX [a_asset_transferdetails_idx01] ON [dbo].[a_asset_transferdetails]
([a_asset_transfer_id] ASC) 
GO
CREATE INDEX [a_asset_transferdetails_idx02] ON [dbo].[a_asset_transferdetails]
([a_asset_id] ASC) 
GO
CREATE INDEX [a_asset_transferdetails_idx03] ON [dbo].[a_asset_transferdetails]
([c_businesspartner_ownerfrom_id] ASC) 
GO
CREATE INDEX [a_asset_transferdetails_idx04] ON [dbo].[a_asset_transferdetails]
([c_businesspartner_ownerto_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[a_asset_transferdetails]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_transferdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_assets]
-- ----------------------------
CREATE UNIQUE INDEX [a_asset_idx01] ON [dbo].[a_assets]
([code] ASC) 
GO
CREATE INDEX [a_asset_idx02] ON [dbo].[a_assets]
([m_product_id] ASC) 
GO
CREATE INDEX [a_asset_idx03] ON [dbo].[a_assets]
([c_location_id] ASC) 
GO
CREATE INDEX [a_asset_idx04] ON [dbo].[a_assets]
([c_businesspartner_supplier_id] ASC) 
GO
CREATE INDEX [a_asset_idx05] ON [dbo].[a_assets]
([c_businesspartner_owner_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[a_assets]
-- ----------------------------
ALTER TABLE [dbo].[a_assets] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[a_assetamounts]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[a_assetamounts]
-- ----------------------------
ALTER TABLE [dbo].[a_assetamounts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_product_categories]
-- ----------------------------
ALTER TABLE [dbo].[m_product_categories] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_product_categories] ADD FOREIGN KEY ([m_category_id]) REFERENCES [dbo].[m_categories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_locations]
-- ----------------------------
ALTER TABLE [dbo].[c_locations] ADD FOREIGN KEY ([c_company_id]) REFERENCES [dbo].[c_companies] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[c_locations] ADD FOREIGN KEY ([c_department_id]) REFERENCES [dbo].[c_departments] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[a_asset_movedetails]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_movedetails] ADD FOREIGN KEY ([a_asset_move_id]) REFERENCES [dbo].[a_asset_moves] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_movedetails] ADD FOREIGN KEY ([a_asset_id]) REFERENCES [dbo].[a_assets] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_movedetails] ADD FOREIGN KEY ([c_locationfrom_id]) REFERENCES [dbo].[c_locations] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_movedetails] ADD FOREIGN KEY ([c_locationto_id]) REFERENCES [dbo].[c_locations] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_businesspartners]
-- ----------------------------
ALTER TABLE [dbo].[c_businesspartners] ADD FOREIGN KEY ([c_department_id]) REFERENCES [dbo].[c_departments] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[c_businesspartners] ADD FOREIGN KEY ([c_company_id]) REFERENCES [dbo].[c_companies] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[a_asset_transferdetails]
-- ----------------------------
ALTER TABLE [dbo].[a_asset_transferdetails] ADD FOREIGN KEY ([a_asset_transfer_id]) REFERENCES [dbo].[a_asset_transfers] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_transferdetails] ADD FOREIGN KEY ([a_asset_id]) REFERENCES [dbo].[a_assets] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_transferdetails] ADD FOREIGN KEY ([c_businesspartner_ownerfrom_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_asset_transferdetails] ADD FOREIGN KEY ([c_businesspartner_ownerto_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[a_assets]
-- ----------------------------
ALTER TABLE [dbo].[a_assets] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_assets] ADD FOREIGN KEY ([c_location_id]) REFERENCES [dbo].[c_locations] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_assets] ADD FOREIGN KEY ([c_businesspartner_supplier_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[a_assets] ADD FOREIGN KEY ([c_businesspartner_owner_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
