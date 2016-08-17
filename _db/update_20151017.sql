ALTER TABLE [dbo].[c_companies]
ADD [address] nvarchar(500) NULL ,
[phone_no] varchar(100) NULL ,
[notes] nvarchar(500) NULL 
GO

---------------------------------------------------------

DROP INDEX [c_locations_idx02] ON [dbo].[c_locations]
GO
EXEC sp_rename N'[dbo].[c_locations].[c_company_id]', N'c_region_id', 'COLUMN'
GO
ALTER TABLE [dbo].[c_locations] DROP CONSTRAINT [c_locations_fk01]
GO

DROP INDEX [c_businesspartners_idx03] ON [dbo].[c_businesspartners]
GO
EXEC sp_rename N'[dbo].[c_businesspartners].[c_company_id]', N'c_region_id', 'COLUMN'
GO
ALTER TABLE [dbo].[c_businesspartners] DROP CONSTRAINT [c_businesspartners_fk02]
GO

EXEC sp_rename 'c_companies', 'c_regions'
GO

CREATE INDEX [c_locations_idx02] ON [dbo].[c_locations]
([c_region_id] ASC) 
GO
ALTER TABLE [dbo].[c_locations] ADD CONSTRAINT [c_locations_fk01] FOREIGN KEY ([c_region_id]) REFERENCES [c_regions] ([id])
GO

CREATE INDEX [c_businesspartners_idx03] ON [dbo].[c_businesspartners]
([c_region_id] ASC) 
GO
ALTER TABLE [dbo].[c_businesspartners] ADD CONSTRAINT [c_businesspartners_fk02] FOREIGN KEY ([c_region_id]) REFERENCES [c_regions] ([id])
GO

UPDATE sys_controls SET name = 'core/region' WHERE name = 'core/company'
GO

---------------------------------------------------------

ALTER TABLE [dbo].[c_locations]
ADD [address_floor] varchar(20) NULL ,
[notes] nvarchar(500) NULL 
GO

---------------------------------------------------------

ALTER TABLE [dbo].[c_businesspartners]
ADD [initial_name] varchar(150) NULL 
GO

---------------------------------------------------------

CREATE INDEX [a_assetamounts_idx01] ON [dbo].[a_assetamounts]
([a_asset_id] ASC) 
GO
ALTER TABLE [dbo].[a_assetamounts] ADD CONSTRAINT [a_assetamounts_fk01] FOREIGN KEY ([a_asset_id]) REFERENCES [a_assets] ([id])
GO

---------------------------------------------------------

EXEC sp_rename N'[dbo].[a_assets].[c_businesspartner_owner_id]', N'c_businesspartner_user_id', 'COLUMN'
GO
DELETE FROM a_assetamounts
GO
DELETE FROM a_asset_movedetails
GO
DELETE FROM a_asset_transferdetails
GO
DELETE FROM a_assets
GO
ALTER TABLE [dbo].[a_assets]
ADD [c_region_id] int NOT NULL ,
[c_department_id] int NOT NULL 
GO
CREATE INDEX [a_asset_idx06] ON [dbo].[a_assets]
([c_region_id] ASC) 
GO
CREATE INDEX [a_asset_idx07] ON [dbo].[a_assets]
([c_department_id] ASC) 
GO
ALTER TABLE [dbo].[a_assets] ADD CONSTRAINT [a_asset_fk05] FOREIGN KEY ([c_region_id]) REFERENCES [c_regions] ([id])
GO
ALTER TABLE [dbo].[a_assets] ADD CONSTRAINT [a_asset_fk06] FOREIGN KEY ([c_department_id]) REFERENCES [c_departments] ([id])
GO

---------------------------------------------------------

EXEC sp_rename N'[dbo].[a_asset_transferdetails].[c_businesspartner_ownerfrom_id]', N'c_businesspartner_userfrom_id', 'COLUMN'
GO
EXEC sp_rename N'[dbo].[a_asset_transferdetails].[c_businesspartner_ownerto_id]', N'c_businesspartner_userto_id', 'COLUMN'
GO
ALTER TABLE [dbo].[a_asset_transferdetails]
ADD [c_departmentfrom_id] int NULL ,
[c_departmentto_id] int NULL ,
[notes] nvarchar(500) NULL 
GO
CREATE INDEX [a_asset_transferdetails_idx05] ON [dbo].[a_asset_transferdetails]
([c_departmentfrom_id] ASC) 
GO
CREATE INDEX [a_asset_transferdetails_idx06] ON [dbo].[a_asset_transferdetails]
([c_departmentto_id] ASC) 
GO
ALTER TABLE [dbo].[a_asset_transferdetails] ADD CONSTRAINT [a_asset_transferdetails_fk05] FOREIGN KEY ([c_departmentfrom_id]) REFERENCES [c_departments] ([id])
GO
ALTER TABLE [dbo].[a_asset_transferdetails] ADD CONSTRAINT [a_asset_transferdetails_fk06] FOREIGN KEY ([c_departmentto_id]) REFERENCES [c_departments] ([id])
GO

---------------------------------------------------------

ALTER TABLE [dbo].[c_regions]
ADD [fax_no] varchar(100) NULL ,
[address_city] varchar(150) NULL 
GO

ALTER TABLE [dbo].[c_businesspartners]
ADD [personal_position] varchar(100) NULL 
GO

---------------------------------------------------------

CREATE TABLE [dbo].[a_assetfiles] (
[id] bigint IDENTITY(1,1) NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[a_asset_id] bigint NOT NULL ,
[file_name] varchar(100) NOT NULL ,
[file_path] varchar(255) NOT NULL ,
[file_size] int NOT NULL ,
[file_mime] varchar(100) NOT NULL ,
[notes] nvarchar(500) NULL 
)
GO
CREATE INDEX [a_assetfiles_idx01] ON [dbo].[a_assetfiles]
([a_asset_id] ASC) 
GO
ALTER TABLE [dbo].[a_assetfiles] ADD PRIMARY KEY ([id])
GO
ALTER TABLE [dbo].[a_assetfiles] ADD FOREIGN KEY ([a_asset_id]) REFERENCES [dbo].[a_assets] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
