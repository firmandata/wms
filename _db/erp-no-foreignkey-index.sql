/*
Navicat SQL Server Data Transfer

Source Server         : SQL Server 2008 R2
Source Server Version : 105000
Source Host           : NOAHMOBILE14-PC\SQLEXPRESS:1433
Source Database       : ERP
Source Schema         : dbo

Target Server Type    : SQL Server
Target Server Version : 105000
File Encoding         : 65001

Date: 2015-01-06 06:18:11
*/


-- ----------------------------
-- Table structure for [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------
DROP TABLE [dbo].[cus_m_inventory_cyclecounts]
GO
CREATE TABLE [dbo].[cus_m_inventory_cyclecounts] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[cus_m_inventory_product_id] int NOT NULL ,
[barcode] varchar(255) NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[pallet] varchar(255) NULL ,
[carton_no] varchar(20) NULL ,
[status] smallint NOT NULL DEFAULT ((0)) ,
[date_packed] date NULL 
)


GO

-- ----------------------------
-- Records of cus_m_inventory_cyclecounts
-- ----------------------------
INSERT INTO [dbo].[cus_m_inventory_cyclecounts] VALUES (N'1', N'2015-01-04 20:00:00', N'1', N'2015-01-04 20:06:00', N'1', N'2', N'2345678998', N'899.0000', N'asdf', N'asdf', N'1', N'2014-01-04');
GO
INSERT INTO [dbo].[cus_m_inventory_cyclecounts] VALUES (N'4', N'2015-01-04 20:01:00', N'1', N'2015-01-04 20:06:00', N'1', N'2', N'12345678901234', N'789.0000', N'asdee', N'asdf', N'1', N'2014-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[cus_m_inventory_products]
-- ----------------------------
DROP TABLE [dbo].[cus_m_inventory_products]
GO
CREATE TABLE [dbo].[cus_m_inventory_products] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[sku] varchar(150) NOT NULL ,
[description] varchar(255) NULL ,
[barcode_length] int NOT NULL DEFAULT ((0)) ,
[qty_start] int NOT NULL DEFAULT ((0)) ,
[qty_end] int NOT NULL DEFAULT ((0)) ,
[sku_start] int NOT NULL DEFAULT ((0)) ,
[sku_end] int NOT NULL DEFAULT ((0)) ,
[carton_start] int NOT NULL DEFAULT ((0)) ,
[carton_end] int NOT NULL DEFAULT ((0)) ,
[date_packed_start] int NOT NULL DEFAULT ((0)) ,
[date_packed_end] int NOT NULL DEFAULT ((0)) ,
[m_product_id] int NULL 
)


GO

-- ----------------------------
-- Records of cus_m_inventory_products
-- ----------------------------
INSERT INTO [dbo].[cus_m_inventory_products] VALUES (N'2', N'2015-01-04 19:45:00', N'1', null, null, N'SKU002', N'SKU Description 02', N'9', N'7', N'9', N'1', N'6', N'0', N'0', N'0', N'0', null);
GO
INSERT INTO [dbo].[cus_m_inventory_products] VALUES (N'3', N'2015-01-04 19:45:00', N'1', null, null, N'SKU003', N'SKU Description 03', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', null);
GO

-- ----------------------------
-- Table structure for [dbo].[c_businesspartners]
-- ----------------------------
DROP TABLE [dbo].[c_businesspartners]
GO
CREATE TABLE [dbo].[c_businesspartners] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(10) NOT NULL ,
[name] varchar(255) NOT NULL ,
[address] nvarchar(500) NULL 
)


GO

-- ----------------------------
-- Records of c_businesspartners
-- ----------------------------
INSERT INTO [dbo].[c_businesspartners] VALUES (N'1', N'2015-01-03 12:38:00', N'1', null, null, N'BP001', N'Business Partner 1', null);
GO
INSERT INTO [dbo].[c_businesspartners] VALUES (N'2', N'2015-01-03 12:38:00', N'1', null, null, N'BP002', N'Business Partner 2', null);
GO
INSERT INTO [dbo].[c_businesspartners] VALUES (N'3', N'2015-01-03 12:38:00', N'1', N'2015-01-04 14:13:00', N'1', N'BP003', N'Business Partner 3', null);
GO

-- ----------------------------
-- Table structure for [dbo].[c_orderindetails]
-- ----------------------------
DROP TABLE [dbo].[c_orderindetails]
GO
CREATE TABLE [dbo].[c_orderindetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[c_orderin_id] bigint NOT NULL ,
[m_product_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL 
)


GO

-- ----------------------------
-- Records of c_orderindetails
-- ----------------------------
INSERT INTO [dbo].[c_orderindetails] VALUES (N'1', N'2015-01-03 19:12:00', N'1', null, null, N'1', N'1', N'10.0000');
GO
INSERT INTO [dbo].[c_orderindetails] VALUES (N'2', N'2015-01-03 19:12:00', N'1', null, null, N'1', N'2', N'20.0000');
GO
INSERT INTO [dbo].[c_orderindetails] VALUES (N'3', N'2015-01-03 19:12:00', N'1', null, null, N'1', N'3', N'30.0000');
GO
INSERT INTO [dbo].[c_orderindetails] VALUES (N'4', N'2015-01-03 19:13:00', N'1', N'2015-01-03 19:17:00', N'1', N'2', N'4', N'4.0000');
GO
INSERT INTO [dbo].[c_orderindetails] VALUES (N'5', N'2015-01-03 19:13:00', N'1', N'2015-01-03 19:17:00', N'1', N'2', N'5', N'5.0000');
GO
INSERT INTO [dbo].[c_orderindetails] VALUES (N'6', N'2015-01-03 19:13:00', N'1', N'2015-01-03 19:17:00', N'1', N'2', N'6', N'6.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[c_orderins]
-- ----------------------------
DROP TABLE [dbo].[c_orderins]
GO
CREATE TABLE [dbo].[c_orderins] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[orderin_date] date NOT NULL ,
[c_businesspartner_id] int NOT NULL 
)


GO

-- ----------------------------
-- Records of c_orderins
-- ----------------------------
INSERT INTO [dbo].[c_orderins] VALUES (N'1', N'2015-01-03 19:12:00', N'1', null, null, N'OI001', N'2015-01-03', N'1');
GO
INSERT INTO [dbo].[c_orderins] VALUES (N'2', N'2015-01-03 19:13:00', N'1', N'2015-01-03 19:17:00', N'1', N'OI002', N'2015-01-03', N'2');
GO

-- ----------------------------
-- Table structure for [dbo].[c_orderoutdetails]
-- ----------------------------
DROP TABLE [dbo].[c_orderoutdetails]
GO
CREATE TABLE [dbo].[c_orderoutdetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[c_orderout_id] bigint NOT NULL ,
[m_product_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of c_orderoutdetails
-- ----------------------------
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'1', N'2015-01-04 07:00:00', N'1', null, null, N'5', N'1', N'5.0000');
GO
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'2', N'2015-01-04 07:00:00', N'1', null, null, N'5', N'2', N'10.0000');
GO
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'3', N'2015-01-04 07:00:00', N'1', null, null, N'5', N'3', N'15.0000');
GO
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'4', N'2015-01-04 07:01:00', N'1', null, null, N'6', N'4', N'10.0000');
GO
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'5', N'2015-01-04 07:01:00', N'1', null, null, N'6', N'5', N'9.0000');
GO
INSERT INTO [dbo].[c_orderoutdetails] VALUES (N'6', N'2015-01-04 07:01:00', N'1', null, null, N'6', N'6', N'8.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[c_orderouts]
-- ----------------------------
DROP TABLE [dbo].[c_orderouts]
GO
CREATE TABLE [dbo].[c_orderouts] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[orderout_date] date NOT NULL ,
[c_businesspartner_id] int NOT NULL 
)


GO

-- ----------------------------
-- Records of c_orderouts
-- ----------------------------
INSERT INTO [dbo].[c_orderouts] VALUES (N'5', N'2015-01-04 07:00:00', N'1', null, null, N'OO001', N'2015-01-04', N'1');
GO
INSERT INTO [dbo].[c_orderouts] VALUES (N'6', N'2015-01-04 07:01:00', N'1', null, null, N'OO002', N'2015-01-04', N'2');
GO

-- ----------------------------
-- Table structure for [dbo].[m_grids]
-- ----------------------------
DROP TABLE [dbo].[m_grids]
GO
CREATE TABLE [dbo].[m_grids] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_warehouse_id] int NOT NULL ,
[m_productgroup_id] int NULL ,
[code] varchar(12) NOT NULL ,
[row] int NOT NULL DEFAULT ((0)) ,
[col] int NOT NULL DEFAULT ((0)) ,
[level] int NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_grids
-- ----------------------------
INSERT INTO [dbo].[m_grids] VALUES (N'1', N'2015-01-03 19:35:00', N'1', N'2015-01-03 19:52:00', N'1', N'1', N'1', N'WH01010101', N'1', N'1', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'9', N'2015-01-03 19:37:00', N'1', null, null, N'2', null, N'WHSYS000000', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'10', N'2015-01-03 19:37:00', N'1', N'2015-01-03 19:52:00', N'1', N'1', N'1', N'WH01010102', N'1', N'1', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'11', N'2015-01-03 19:37:00', N'1', null, null, N'1', null, N'WH01010201', N'1', N'2', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'12', N'2015-01-03 19:37:00', N'1', null, null, N'1', null, N'WH01010202', N'1', N'2', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'13', N'2015-01-03 19:37:00', N'1', N'2015-01-03 19:52:00', N'1', N'1', N'1', N'WH01020101', N'2', N'1', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'14', N'2015-01-03 19:37:00', N'1', N'2015-01-03 19:52:00', N'1', N'1', N'1', N'WH01020102', N'2', N'1', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'15', N'2015-01-03 19:37:00', N'1', null, null, N'1', null, N'WH01020201', N'2', N'2', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'16', N'2015-01-03 19:37:00', N'1', null, null, N'1', null, N'WH01020202', N'2', N'2', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'17', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02010101', N'1', N'1', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'18', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02010102', N'1', N'1', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'19', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02010201', N'1', N'2', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'20', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02010202', N'1', N'2', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'21', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02010301', N'1', N'3', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'22', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02010302', N'1', N'3', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'23', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02020101', N'2', N'1', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'24', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02020102', N'2', N'1', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'25', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02020201', N'2', N'2', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'26', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02020202', N'2', N'2', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'27', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02020301', N'2', N'3', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'28', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02020302', N'2', N'3', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'29', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02030101', N'3', N'1', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'30', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'1', N'WH02030102', N'3', N'1', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'31', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02030201', N'3', N'2', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'32', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'2', N'WH02030202', N'3', N'2', N'2');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'33', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02030301', N'3', N'3', N'1');
GO
INSERT INTO [dbo].[m_grids] VALUES (N'34', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'3', N'3', N'WH02030302', N'3', N'3', N'2');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventories]
-- ----------------------------
DROP TABLE [dbo].[m_inventories]
GO
CREATE TABLE [dbo].[m_inventories] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_product_id] int NOT NULL ,
[m_grid_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventories
-- ----------------------------
INSERT INTO [dbo].[m_inventories] VALUES (N'3', N'2015-01-04 09:14:00', N'1', N'2015-01-04 16:24:00', N'1', N'1', N'10', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'4', N'2015-01-04 09:14:00', N'1', N'2015-01-04 16:24:00', N'1', N'2', N'19', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'5', N'2015-01-04 09:14:00', N'1', N'2015-01-04 16:27:00', N'1', N'3', N'22', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'10', N'2015-01-04 14:11:00', N'1', N'2015-01-04 16:24:00', N'1', N'4', N'9', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'11', N'2015-01-04 14:11:00', N'1', N'2015-01-04 16:27:00', N'1', N'5', N'25', N'1.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'12', N'2015-01-04 14:11:00', N'1', N'2015-01-04 16:27:00', N'1', N'6', N'11', N'1.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'13', N'2015-01-04 14:47:00', N'1', N'2015-01-04 16:24:00', N'1', N'4', N'13', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'14', N'2015-01-04 15:32:00', N'1', N'2015-01-04 16:24:00', N'1', N'2', N'19', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'15', N'2015-01-04 15:32:00', N'1', N'2015-01-04 16:27:00', N'1', N'3', N'21', N'5.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'16', N'2015-01-04 15:34:00', N'1', N'2015-01-04 16:24:00', N'1', N'2', N'20', N'.0000');
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'17', N'2015-01-04 15:34:00', N'1', N'2015-01-04 16:27:00', N'1', N'3', N'22', N'5.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventorylogs]
-- ----------------------------
DROP TABLE [dbo].[m_inventorylogs]
GO
CREATE TABLE [dbo].[m_inventorylogs] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_id] bigint NOT NULL ,
[m_product_id] int NOT NULL ,
[m_grid_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[notes] varchar(255) NULL 
)


GO

-- ----------------------------
-- Records of m_inventorylogs
-- ----------------------------
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'3', N'2015-01-04 09:14:00', N'1', null, null, N'3', N'1', N'9', N'1.0000', null);
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'4', N'2015-01-04 09:14:00', N'1', null, null, N'4', N'2', N'9', N'10.0000', null);
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'5', N'2015-01-04 09:14:00', N'1', null, null, N'5', N'3', N'9', N'20.0000', null);
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'27', N'2015-01-04 14:11:00', N'1', null, null, N'10', N'4', N'9', N'2.0000', N'M_inventory_inbound I002 Add Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'28', N'2015-01-04 14:11:00', N'1', null, null, N'11', N'5', N'9', N'2.0000', N'M_inventory_inbound I002 Add Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'29', N'2015-01-04 14:11:00', N'1', null, null, N'12', N'6', N'9', N'2.0000', N'M_inventory_inbound I002 Add Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'30', N'2015-01-04 14:47:00', N'1', null, null, N'13', N'4', N'13', N'1.0000', N'M_inventory_putaway PA002 Add Putaway');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'31', N'2015-01-04 14:47:00', N'1', null, null, N'10', N'4', N'9', N'-1.0000', N'M_inventory_putaway PA002 Add Putaway');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'32', N'2015-01-04 15:32:00', N'1', null, null, N'14', N'2', N'19', N'5.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'33', N'2015-01-04 15:32:00', N'1', null, null, N'4', N'2', N'19', N'-5.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'34', N'2015-01-04 15:32:00', N'1', null, null, N'15', N'3', N'22', N'15.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'35', N'2015-01-04 15:32:00', N'1', null, null, N'5', N'3', N'21', N'-15.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'36', N'2015-01-04 15:34:00', N'1', null, null, N'16', N'2', N'20', N'4.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'37', N'2015-01-04 15:34:00', N'1', null, null, N'14', N'2', N'19', N'-4.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'38', N'2015-01-04 15:34:00', N'1', null, null, N'17', N'3', N'22', N'7.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'39', N'2015-01-04 15:34:00', N'1', null, null, N'15', N'3', N'21', N'-7.0000', N'M_inventory_move M001 Add Move');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'40', N'2015-01-04 15:57:00', N'1', null, null, N'11', N'5', N'25', N'3.0000', N'M_inventory_adjust A001 Add Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'41', N'2015-01-04 15:57:00', N'1', null, null, N'12', N'6', N'11', N'4.0000', N'M_inventory_adjust A001 Add Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'42', N'2015-01-04 15:57:00', N'1', null, null, N'11', N'5', N'25', N'-3.0000', N'M_inventory_adjust A001 Remove Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'43', N'2015-01-04 15:58:00', N'1', null, null, N'11', N'5', N'25', N'8.0000', N'M_inventory_adjust A001 Add Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'48', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'1', N'10', N'-1.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'49', N'2015-01-04 16:24:00', N'1', null, null, N'14', N'2', N'19', N'-1.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'50', N'2015-01-04 16:24:00', N'1', null, null, N'16', N'2', N'20', N'-4.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'51', N'2015-01-04 16:24:00', N'1', null, null, N'4', N'2', N'19', N'-5.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'52', N'2015-01-04 16:24:00', N'1', null, null, N'10', N'4', N'9', N'-1.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'53', N'2015-01-04 16:24:00', N'1', null, null, N'13', N'4', N'13', N'-1.0000', N'M_inventory_picklist PL001 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'54', N'2015-01-04 16:27:00', N'1', null, null, N'5', N'3', N'22', N'-5.0000', N'M_inventory_picklist PL02 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'55', N'2015-01-04 16:27:00', N'1', null, null, N'17', N'3', N'22', N'-7.0000', N'M_inventory_picklist PL02 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'56', N'2015-01-04 16:27:00', N'1', null, null, N'15', N'3', N'21', N'-3.0000', N'M_inventory_picklist PL02 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'57', N'2015-01-04 16:27:00', N'1', null, null, N'11', N'5', N'25', N'-9.0000', N'M_inventory_picklist PL02 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'58', N'2015-01-04 16:27:00', N'1', null, null, N'12', N'6', N'11', N'-5.0000', N'M_inventory_picklist PL02 Add Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'59', N'2015-01-04 16:27:00', N'1', null, null, N'17', N'3', N'22', N'5.0000', N'M_inventory_picklist PL002 Modify Pick List');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_adjustdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_adjustdetails]
GO
CREATE TABLE [dbo].[m_inventory_adjustdetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_adjust_id] bigint NOT NULL ,
[m_inventory_id] bigint NOT NULL ,
[quantity_from] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[quantity_to] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_adjustdetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_adjustdetails] VALUES (N'2', N'2015-01-04 15:57:00', N'1', null, null, N'1', N'12', N'2.0000', N'6.0000');
GO
INSERT INTO [dbo].[m_inventory_adjustdetails] VALUES (N'3', N'2015-01-04 15:58:00', N'1', null, null, N'1', N'11', N'2.0000', N'10.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_adjusts]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_adjusts]
GO
CREATE TABLE [dbo].[m_inventory_adjusts] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[adjust_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_adjusts
-- ----------------------------
INSERT INTO [dbo].[m_inventory_adjusts] VALUES (N'1', N'2015-01-04 15:57:00', N'1', N'2015-01-04 15:57:00', N'1', N'A001', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_inbounddetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_inbounddetails]
GO
CREATE TABLE [dbo].[m_inventory_inbounddetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_inbound_id] bigint NOT NULL ,
[m_inventory_receivedetail_id] bigint NOT NULL ,
[m_inventory_id] bigint NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_inbounddetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'3', N'2015-01-04 09:14:00', N'1', null, null, N'4', N'1', N'3', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'4', N'2015-01-04 09:14:00', N'1', null, null, N'4', N'2', N'4', N'10.0000');
GO
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'5', N'2015-01-04 09:14:00', N'1', null, null, N'4', N'3', N'5', N'20.0000');
GO
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'6', N'2015-01-04 14:11:00', N'1', null, null, N'10', N'4', N'10', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'7', N'2015-01-04 14:11:00', N'1', null, null, N'10', N'5', N'11', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_inbounddetails] VALUES (N'8', N'2015-01-04 14:11:00', N'1', null, null, N'10', N'6', N'12', N'2.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_inbounds]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_inbounds]
GO
CREATE TABLE [dbo].[m_inventory_inbounds] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[inbound_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_inbounds
-- ----------------------------
INSERT INTO [dbo].[m_inventory_inbounds] VALUES (N'4', N'2015-01-04 09:14:00', N'1', null, null, N'I001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_inbounds] VALUES (N'10', N'2015-01-04 14:11:00', N'1', null, null, N'I002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_movedetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_movedetails]
GO
CREATE TABLE [dbo].[m_inventory_movedetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_move_id] bigint NOT NULL ,
[m_inventory_id] bigint NOT NULL ,
[m_gridfrom_id] int NOT NULL ,
[m_gridto_id] int NOT NULL ,
[quantity_from] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[quantity_to] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_movedetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_movedetails] VALUES (N'4', N'2015-01-04 15:34:00', N'1', null, null, N'2', N'3', N'1', N'10', N'1.0000', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_movedetails] VALUES (N'5', N'2015-01-04 15:34:00', N'1', null, null, N'2', N'16', N'19', N'20', N'5.0000', N'4.0000');
GO
INSERT INTO [dbo].[m_inventory_movedetails] VALUES (N'6', N'2015-01-04 15:34:00', N'1', null, null, N'2', N'5', N'21', N'22', N'5.0000', N'5.0000');
GO
INSERT INTO [dbo].[m_inventory_movedetails] VALUES (N'7', N'2015-01-04 15:34:00', N'1', null, null, N'2', N'17', N'21', N'22', N'15.0000', N'7.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_moves]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_moves]
GO
CREATE TABLE [dbo].[m_inventory_moves] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[move_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_moves
-- ----------------------------
INSERT INTO [dbo].[m_inventory_moves] VALUES (N'2', N'2015-01-04 15:34:00', N'1', null, null, N'M001', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_pickingdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_pickingdetails]
GO
CREATE TABLE [dbo].[m_inventory_pickingdetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_picking_id] bigint NOT NULL ,
[m_inventory_picklistdetail_id] bigint NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_pickingdetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'1', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'5', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'2', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'6', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'3', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'7', N'4.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'4', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'11', N'5.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'5', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'12', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'6', N'2015-01-04 16:56:00', N'1', null, null, N'1', N'13', N'3.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'7', N'2015-01-04 16:57:00', N'1', null, null, N'2', N'9', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'8', N'2015-01-04 16:57:00', N'1', null, null, N'2', N'10', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'9', N'2015-01-04 16:57:00', N'1', null, null, N'2', N'14', N'4.0000');
GO
INSERT INTO [dbo].[m_inventory_pickingdetails] VALUES (N'10', N'2015-01-04 16:57:00', N'1', null, null, N'2', N'15', N'3.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_pickings]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_pickings]
GO
CREATE TABLE [dbo].[m_inventory_pickings] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[picking_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_pickings
-- ----------------------------
INSERT INTO [dbo].[m_inventory_pickings] VALUES (N'1', N'2015-01-04 16:56:00', N'1', null, null, N'PG001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_pickings] VALUES (N'2', N'2015-01-04 16:57:00', N'1', null, null, N'PG002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_picklistdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_picklistdetails]
GO
CREATE TABLE [dbo].[m_inventory_picklistdetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_picklist_id] bigint NOT NULL ,
[c_orderoutdetail_id] bigint NOT NULL ,
[m_inventory_id] bigint NOT NULL ,
[m_grid_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_picklistdetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'5', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'1', N'3', N'10', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'6', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'2', N'14', N'19', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'7', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'2', N'16', N'20', N'4.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'8', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'2', N'4', N'19', N'5.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'9', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'4', N'10', N'9', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'10', N'2015-01-04 16:24:00', N'1', null, null, N'3', N'4', N'13', N'13', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'11', N'2015-01-04 16:27:00', N'1', null, null, N'4', N'3', N'5', N'22', N'5.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'12', N'2015-01-04 16:27:00', N'1', N'2015-01-04 16:27:00', N'1', N'4', N'3', N'17', N'22', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'13', N'2015-01-04 16:27:00', N'1', null, null, N'4', N'3', N'15', N'21', N'3.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'14', N'2015-01-04 16:27:00', N'1', null, null, N'4', N'5', N'11', N'25', N'9.0000');
GO
INSERT INTO [dbo].[m_inventory_picklistdetails] VALUES (N'15', N'2015-01-04 16:27:00', N'1', null, null, N'4', N'6', N'12', N'11', N'5.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_picklists]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_picklists]
GO
CREATE TABLE [dbo].[m_inventory_picklists] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[picklist_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_picklists
-- ----------------------------
INSERT INTO [dbo].[m_inventory_picklists] VALUES (N'3', N'2015-01-04 16:24:00', N'1', null, null, N'PL001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_picklists] VALUES (N'4', N'2015-01-04 16:27:00', N'1', N'2015-01-04 16:27:00', N'1', N'PL002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_putawaydetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_putawaydetails]
GO
CREATE TABLE [dbo].[m_inventory_putawaydetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_putaway_id] bigint NOT NULL ,
[m_inventory_id] bigint NOT NULL ,
[m_gridfrom_id] int NOT NULL ,
[m_gridto_id] int NOT NULL ,
[quantity_from] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[quantity_to] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_putawaydetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'1', N'2015-01-04 14:46:00', N'1', null, null, N'1', N'3', N'9', N'1', N'1.0000', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'2', N'2015-01-04 14:46:00', N'1', null, null, N'1', N'4', N'9', N'19', N'10.0000', N'10.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'3', N'2015-01-04 14:46:00', N'1', null, null, N'1', N'5', N'9', N'21', N'20.0000', N'20.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'5', N'2015-01-04 14:47:00', N'1', null, null, N'2', N'11', N'9', N'25', N'2.0000', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'6', N'2015-01-04 14:47:00', N'1', null, null, N'2', N'12', N'9', N'11', N'2.0000', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'7', N'2015-01-04 15:02:00', N'1', null, null, N'2', N'13', N'9', N'13', N'1.0000', N'1.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_putaways]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_putaways]
GO
CREATE TABLE [dbo].[m_inventory_putaways] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[putaway_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_putaways
-- ----------------------------
INSERT INTO [dbo].[m_inventory_putaways] VALUES (N'1', N'2015-01-04 14:46:00', N'1', null, null, N'PA001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_putaways] VALUES (N'2', N'2015-01-04 14:47:00', N'1', N'2015-01-04 14:58:00', N'1', N'PA002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_receivedetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_receivedetails]
GO
CREATE TABLE [dbo].[m_inventory_receivedetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_receive_id] bigint NOT NULL ,
[c_orderindetail_id] bigint NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_receivedetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'1', N'2015-01-04 08:05:00', N'1', N'2015-01-04 08:07:00', N'1', N'1', N'1', N'3.0000');
GO
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'2', N'2015-01-04 08:05:00', N'1', N'2015-01-04 08:07:00', N'1', N'1', N'2', N'13.0000');
GO
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'3', N'2015-01-04 08:05:00', N'1', N'2015-01-04 08:07:00', N'1', N'1', N'3', N'23.0000');
GO
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'4', N'2015-01-04 08:07:00', N'1', null, null, N'2', N'4', N'3.0000');
GO
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'5', N'2015-01-04 08:07:00', N'1', null, null, N'2', N'5', N'4.0000');
GO
INSERT INTO [dbo].[m_inventory_receivedetails] VALUES (N'6', N'2015-01-04 08:07:00', N'1', null, null, N'2', N'6', N'5.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_receives]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_receives]
GO
CREATE TABLE [dbo].[m_inventory_receives] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[receive_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_receives
-- ----------------------------
INSERT INTO [dbo].[m_inventory_receives] VALUES (N'1', N'2015-01-04 08:05:00', N'1', N'2015-01-04 08:06:00', N'1', N'R001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_receives] VALUES (N'2', N'2015-01-04 08:07:00', N'1', null, null, N'R002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_shipmentdetails]
GO
CREATE TABLE [dbo].[m_inventory_shipmentdetails] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_shipment_id] bigint NOT NULL ,
[m_inventory_pickingdetail_id] bigint NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_inventory_shipmentdetails
-- ----------------------------
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'1', N'2015-01-04 19:22:00', N'1', null, null, N'1', N'1', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'2', N'2015-01-04 19:22:00', N'1', null, null, N'1', N'2', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'3', N'2015-01-04 19:22:00', N'1', N'2015-01-04 19:22:00', N'1', N'1', N'3', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'4', N'2015-01-04 19:22:00', N'1', N'2015-01-04 19:22:00', N'1', N'1', N'4', N'5.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'5', N'2015-01-04 19:22:00', N'1', N'2015-01-04 19:22:00', N'1', N'1', N'5', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'6', N'2015-01-04 19:22:00', N'1', N'2015-01-04 19:22:00', N'1', N'1', N'6', N'3.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'7', N'2015-01-04 19:23:00', N'1', null, null, N'2', N'3', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'8', N'2015-01-04 19:23:00', N'1', null, null, N'2', N'7', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'9', N'2015-01-04 19:23:00', N'1', null, null, N'2', N'8', N'1.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'10', N'2015-01-04 19:23:00', N'1', null, null, N'2', N'9', N'2.0000');
GO
INSERT INTO [dbo].[m_inventory_shipmentdetails] VALUES (N'11', N'2015-01-04 19:23:00', N'1', null, null, N'2', N'10', N'2.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_shipments]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_shipments]
GO
CREATE TABLE [dbo].[m_inventory_shipments] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(50) NOT NULL ,
[shipment_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_inventory_shipments
-- ----------------------------
INSERT INTO [dbo].[m_inventory_shipments] VALUES (N'1', N'2015-01-04 19:22:00', N'1', N'2015-01-04 19:22:00', N'1', N'SP001', N'2015-01-04');
GO
INSERT INTO [dbo].[m_inventory_shipments] VALUES (N'2', N'2015-01-04 19:23:00', N'1', null, null, N'SP002', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_productgroups]
-- ----------------------------
DROP TABLE [dbo].[m_productgroups]
GO
CREATE TABLE [dbo].[m_productgroups] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(15) NOT NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Records of m_productgroups
-- ----------------------------
INSERT INTO [dbo].[m_productgroups] VALUES (N'1', N'2015-01-03 16:43:00', N'1', N'2015-01-03 16:43:00', N'1', N'GRP01', N'Group 01');
GO
INSERT INTO [dbo].[m_productgroups] VALUES (N'2', N'2015-01-03 16:43:00', N'1', null, null, N'GRP02', N'Group 02');
GO
INSERT INTO [dbo].[m_productgroups] VALUES (N'3', N'2015-01-03 16:43:00', N'1', N'2015-01-03 16:43:00', N'1', N'GRP03', N'Group 03');
GO
INSERT INTO [dbo].[m_productgroups] VALUES (N'4', N'2015-01-04 14:48:00', N'1', null, null, N'GRP04', N'Group 04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_products]
-- ----------------------------
DROP TABLE [dbo].[m_products]
GO
CREATE TABLE [dbo].[m_products] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(50) NOT NULL ,
[name] varchar(255) NOT NULL ,
[m_productgroup_id] int NULL 
)


GO

-- ----------------------------
-- Records of m_products
-- ----------------------------
INSERT INTO [dbo].[m_products] VALUES (N'1', N'2015-01-03 16:39:00', N'1', N'2015-01-03 16:57:00', N'1', N'P001', N'Product 01', N'1');
GO
INSERT INTO [dbo].[m_products] VALUES (N'2', N'2015-01-03 16:44:00', N'1', null, null, N'P002', N'Product 02', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'3', N'2015-01-03 19:08:00', N'1', N'2015-01-03 19:10:00', N'1', N'P003', N'Product 03', N'3');
GO
INSERT INTO [dbo].[m_products] VALUES (N'4', N'2015-01-03 19:09:00', N'1', N'2015-01-03 19:11:00', N'1', N'P004', N'Product 04', N'1');
GO
INSERT INTO [dbo].[m_products] VALUES (N'5', N'2015-01-03 19:09:00', N'1', N'2015-01-03 19:11:00', N'1', N'P005', N'Product 05', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'6', N'2015-01-03 19:09:00', N'1', null, null, N'P006', N'Product 06', null);
GO
INSERT INTO [dbo].[m_products] VALUES (N'7', N'2015-01-03 19:10:00', N'1', N'2015-01-03 19:11:00', N'1', N'P007', N'Product 07', N'1');
GO
INSERT INTO [dbo].[m_products] VALUES (N'8', N'2015-01-03 19:10:00', N'1', N'2015-01-03 19:11:00', N'1', N'P008', N'Product 08', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'9', N'2015-01-03 19:10:00', N'1', N'2015-01-04 14:48:00', N'1', N'P009', N'Product 09', N'4');
GO
INSERT INTO [dbo].[m_products] VALUES (N'10', N'2015-01-03 19:10:00', N'1', null, null, N'P010', N'Product 10', N'1');
GO

-- ----------------------------
-- Table structure for [dbo].[m_warehouses]
-- ----------------------------
DROP TABLE [dbo].[m_warehouses]
GO
CREATE TABLE [dbo].[m_warehouses] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(5) NOT NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Records of m_warehouses
-- ----------------------------
INSERT INTO [dbo].[m_warehouses] VALUES (N'1', N'2015-01-03 19:35:00', N'1', N'2015-01-03 19:37:00', N'1', N'WH01', N'Warehouse 01');
GO
INSERT INTO [dbo].[m_warehouses] VALUES (N'2', N'2015-01-03 19:37:00', N'1', null, null, N'WHSYS', N'Default Warehouse');
GO
INSERT INTO [dbo].[m_warehouses] VALUES (N'3', N'2015-01-03 19:38:00', N'1', N'2015-01-03 19:53:00', N'1', N'WH02', N'Warehouse 02');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_accesscontrols]
-- ----------------------------
DROP TABLE [dbo].[sys_accesscontrols]
GO
CREATE TABLE [dbo].[sys_accesscontrols] (
[id] bigint NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[sys_usergroup_id] int NOT NULL ,
[sys_control_id] int NOT NULL ,
[sys_action_id] int NOT NULL ,
[is_denied] bit NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of sys_accesscontrols
-- ----------------------------
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'1', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'2', null, null, null, null, N'1', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'3', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'1', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'4', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'1', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'5', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'1', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'6', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'2', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'7', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'2', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'8', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'2', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'9', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'2', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'10', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'3', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'11', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'3', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'12', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'3', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'13', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'3', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'14', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'4', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'15', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'4', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'16', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'4', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'17', N'2015-01-03 00:46:00', null, N'2015-01-04 19:30:00', N'1', N'1', N'4', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'18', N'2015-01-01 13:38:00', N'1', N'2015-01-01 13:38:00', N'1', N'3', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'22', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'5', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'23', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'5', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'24', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'5', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'25', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'5', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'26', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'6', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'27', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'6', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'28', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'6', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'29', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'6', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'30', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'8', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'31', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'8', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'32', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'8', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'33', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'8', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'34', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'9', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'35', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'9', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'36', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'9', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'37', N'2015-01-03 00:46:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'9', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'38', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'10', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'39', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'10', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'40', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'10', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'41', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'10', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'42', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'11', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'43', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'11', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'44', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'11', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'45', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'11', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'46', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'12', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'47', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'12', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'48', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'12', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'49', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'12', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'50', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'13', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'51', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'13', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'52', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'13', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'53', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'13', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'54', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'14', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'55', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'14', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'56', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'14', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'57', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'14', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'58', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'15', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'59', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'15', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'60', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'15', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'61', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'15', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'62', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'16', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'63', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'16', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'64', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'16', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'65', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'16', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'66', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'17', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'67', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'17', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'68', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'17', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'69', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'17', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'70', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'18', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'71', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'18', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'72', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'18', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'73', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'18', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'74', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'19', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'75', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'19', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'76', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'19', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'77', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'19', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'78', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'20', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'79', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'20', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'80', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'20', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'81', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'20', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'82', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'21', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'83', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'21', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'84', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'21', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'85', N'2015-01-03 01:02:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'21', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'86', N'2015-01-03 16:40:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'22', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'87', N'2015-01-03 16:40:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'22', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'88', N'2015-01-03 16:40:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'22', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'89', N'2015-01-03 16:40:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'22', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'90', N'2015-01-03 19:22:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'23', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'91', N'2015-01-03 19:22:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'23', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'92', N'2015-01-03 19:22:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'23', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'93', N'2015-01-03 19:22:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'23', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'94', N'2015-01-03 20:04:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'24', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'95', N'2015-01-03 20:04:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'24', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'96', N'2015-01-03 20:04:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'24', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'97', N'2015-01-03 20:04:00', N'1', N'2015-01-04 19:30:00', N'1', N'1', N'24', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'98', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'25', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'99', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'25', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'100', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'25', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'101', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'25', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'102', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'26', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'103', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'26', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'104', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'26', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'105', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'26', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'106', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'27', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'107', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'27', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'108', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'27', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'109', N'2015-01-04 19:30:00', N'1', null, null, N'1', N'27', N'4', N'0');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_actions]
-- ----------------------------
DROP TABLE [dbo].[sys_actions]
GO
CREATE TABLE [dbo].[sys_actions] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Records of sys_actions
-- ----------------------------
INSERT INTO [dbo].[sys_actions] VALUES (N'1', N'2015-01-03 19:59:00', null, N'2015-01-03 19:59:00', N'1', N'index');
GO
INSERT INTO [dbo].[sys_actions] VALUES (N'2', null, null, null, null, N'insert');
GO
INSERT INTO [dbo].[sys_actions] VALUES (N'3', null, null, null, null, N'update');
GO
INSERT INTO [dbo].[sys_actions] VALUES (N'4', null, null, null, null, N'delete');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_ci_sessions]
-- ----------------------------
DROP TABLE [dbo].[sys_ci_sessions]
GO
CREATE TABLE [dbo].[sys_ci_sessions] (
[session_id] varchar(40) NOT NULL ,
[ip_address] varchar(45) NOT NULL ,
[user_agent] varchar(255) NOT NULL ,
[last_activity] int NOT NULL ,
[user_data] text NULL 
)


GO

-- ----------------------------
-- Records of sys_ci_sessions
-- ----------------------------
INSERT INTO [dbo].[sys_ci_sessions] VALUES (N'852a57c5f297d574620fc0cd5d146c8f', N'127.0.0.1', N'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0', N'1420377107', N'');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_controls]
-- ----------------------------
DROP TABLE [dbo].[sys_controls]
GO
CREATE TABLE [dbo].[sys_controls] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Records of sys_controls
-- ----------------------------
INSERT INTO [dbo].[sys_controls] VALUES (N'1', null, null, null, null, N'system/home');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'2', N'2015-01-03 20:00:00', null, N'2015-01-03 20:00:00', N'1', N'system/user');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'3', null, null, null, null, N'system/accesscontrol');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'4', null, null, null, null, N'system/menu');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'5', N'2015-01-02 18:09:00', N'1', N'2015-01-02 18:09:00', N'1', N'system/control');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'6', N'2015-01-02 18:09:00', N'1', null, null, N'system/action');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'8', N'2015-01-03 00:44:00', N'1', null, null, N'core/orderin');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'9', N'2015-01-03 00:45:00', N'1', null, null, N'core/orderout');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'10', N'2015-01-03 00:47:00', N'1', null, null, N'material/inventory');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'11', N'2015-01-03 00:49:00', N'1', null, null, N'material/inventory_receive');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'12', N'2015-01-03 00:50:00', N'1', N'2015-01-03 00:50:00', N'1', N'material/inventory_inbound');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'13', N'2015-01-03 00:52:00', N'1', null, null, N'material/product');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'14', N'2015-01-03 00:53:00', N'1', null, null, N'material/warehouse');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'15', N'2015-01-03 00:55:00', N'1', null, null, N'core/businesspartner');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'16', N'2015-01-03 00:57:00', N'1', null, null, N'material/inventory_putaway');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'17', N'2015-01-03 00:57:00', N'1', N'2015-01-03 00:58:00', N'1', N'material/inventory_move');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'18', N'2015-01-03 00:58:00', N'1', null, null, N'material/inventory_adjust');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'19', N'2015-01-03 01:00:00', N'1', null, null, N'material/inventory_picklist');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'20', N'2015-01-03 01:00:00', N'1', null, null, N'material/inventory_picking');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'21', N'2015-01-03 01:01:00', N'1', null, null, N'material/inventory_shipment');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'22', N'2015-01-03 16:40:00', N'1', null, null, N'material/productgroup');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'23', N'2015-01-03 19:22:00', N'1', null, null, N'material/grid');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'24', N'2015-01-03 20:03:00', N'1', null, null, N'material/inventorylog');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'25', N'2015-01-04 19:26:00', N'1', N'2015-01-04 19:38:00', N'1', N'custom/inventory_product');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'26', N'2015-01-04 19:28:00', N'1', N'2015-01-04 19:38:00', N'1', N'custom/inventory_cyclecount_out');
GO
INSERT INTO [dbo].[sys_controls] VALUES (N'27', N'2015-01-04 19:29:00', N'1', N'2015-01-04 19:38:00', N'1', N'custom/inventory_cyclecount_in');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_menus]
-- ----------------------------
DROP TABLE [dbo].[sys_menus]
GO
CREATE TABLE [dbo].[sys_menus] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[parent_id] int NULL ,
[sys_control_id] int NULL ,
[sys_action_id] int NULL ,
[name] varchar(255) NOT NULL ,
[sequence] int NOT NULL DEFAULT ((0)) ,
[url] varchar(255) NULL ,
[css] varchar(255) NULL 
)


GO

-- ----------------------------
-- Records of sys_menus
-- ----------------------------
INSERT INTO [dbo].[sys_menus] VALUES (N'1', null, null, null, null, null, N'1', N'1', N'Home', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'2', null, null, null, null, null, null, null, N'Application Setup', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'3', null, null, null, null, N'2', N'2', N'1', N'User Management', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'4', null, null, null, null, N'2', N'3', N'1', N'Access Control', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'5', null, null, null, null, N'2', N'4', N'1', N'Menu Configuration', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'6', N'2015-01-01 13:40:00', N'1', N'2015-01-03 00:44:00', N'1', null, null, null, N'Order Management', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'9', N'2015-01-03 00:43:00', N'1', N'2015-01-03 01:08:00', N'1', null, null, null, N'Production', N'4', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'10', N'2015-01-03 00:45:00', N'1', N'2015-01-03 00:55:00', N'1', N'6', N'8', N'1', N'In', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'11', N'2015-01-03 00:45:00', N'1', N'2015-01-03 00:55:00', N'1', N'6', N'9', N'1', N'Out', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'12', N'2015-01-03 00:47:00', N'1', N'2015-01-03 00:51:00', N'1', N'9', N'10', N'1', N'Inventory', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'13', N'2015-01-03 00:48:00', N'1', N'2015-01-03 00:51:00', N'1', N'9', null, null, N'In', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'14', N'2015-01-03 00:49:00', N'1', null, null, N'13', N'11', N'1', N'Receive', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'15', N'2015-01-03 00:50:00', N'1', null, null, N'13', N'12', N'1', N'Inbound', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'16', N'2015-01-03 00:51:00', N'1', N'2015-01-03 00:52:00', N'1', N'9', null, null, N'Setup', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'17', N'2015-01-03 00:52:00', N'1', null, null, N'16', N'13', N'1', N'Product', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'18', N'2015-01-03 00:53:00', N'1', null, null, N'16', N'14', N'1', N'Warehouse', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'19', N'2015-01-03 00:54:00', N'1', null, null, N'9', null, null, N'Out', N'4', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'20', N'2015-01-03 00:55:00', N'1', null, null, N'6', null, null, N'Setup', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'21', N'2015-01-03 00:56:00', N'1', null, null, N'20', N'15', N'1', N'Business Partner', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'22', N'2015-01-03 00:57:00', N'1', null, null, N'12', N'16', N'1', N'Putaway', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'23', N'2015-01-03 00:57:00', N'1', N'2015-01-03 00:58:00', N'1', N'12', N'17', N'1', N'Move', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'24', N'2015-01-03 00:58:00', N'1', null, null, N'12', N'18', N'1', N'Adjust', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'25', N'2015-01-03 01:00:00', N'1', null, null, N'19', N'19', N'1', N'Pick List', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'26', N'2015-01-03 01:00:00', N'1', null, null, N'19', N'20', N'1', N'Picking', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'27', N'2015-01-03 01:01:00', N'1', null, null, N'19', N'21', N'1', N'Shipment', N'3', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'28', N'2015-01-03 20:03:00', N'1', null, null, N'12', N'24', N'1', N'Log', N'4', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'29', N'2015-01-04 19:26:00', N'1', null, null, null, null, null, N'Custom', N'4', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'30', N'2015-01-04 19:27:00', N'1', N'2015-01-04 19:27:00', N'1', N'29', null, null, N'Etc', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'31', N'2015-01-04 19:27:00', N'1', N'2015-01-04 19:35:00', N'1', N'30', N'25', N'1', N'Product', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'32', N'2015-01-04 19:28:00', N'1', null, null, N'30', null, null, N'Cycle Count', N'2', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'33', N'2015-01-04 19:29:00', N'1', null, null, N'32', N'26', N'1', N'Out', N'1', null, null);
GO
INSERT INTO [dbo].[sys_menus] VALUES (N'34', N'2015-01-04 19:29:00', N'1', null, null, N'32', N'27', N'1', N'In', N'2', null, null);
GO

-- ----------------------------
-- Table structure for [dbo].[sys_usergroups]
-- ----------------------------
DROP TABLE [dbo].[sys_usergroups]
GO
CREATE TABLE [dbo].[sys_usergroups] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[name] varchar(255) NOT NULL 
)


GO

-- ----------------------------
-- Records of sys_usergroups
-- ----------------------------
INSERT INTO [dbo].[sys_usergroups] VALUES (N'1', N'2015-01-03 00:46:00', null, N'2015-01-03 00:46:00', N'1', N'Developer');
GO
INSERT INTO [dbo].[sys_usergroups] VALUES (N'2', null, null, null, null, N'Administrator');
GO
INSERT INTO [dbo].[sys_usergroups] VALUES (N'3', N'2015-01-01 13:38:00', N'1', N'2015-01-01 13:38:00', N'1', N'WMS Administrator');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_usergroup_users]
-- ----------------------------
DROP TABLE [dbo].[sys_usergroup_users]
GO
CREATE TABLE [dbo].[sys_usergroup_users] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[sys_usergroup_id] int NOT NULL ,
[sys_user_id] int NOT NULL 
)


GO

-- ----------------------------
-- Records of sys_usergroup_users
-- ----------------------------
INSERT INTO [dbo].[sys_usergroup_users] VALUES (N'1', null, null, null, null, N'1', N'1');
GO
INSERT INTO [dbo].[sys_usergroup_users] VALUES (N'2', null, null, null, null, N'2', N'2');
GO
INSERT INTO [dbo].[sys_usergroup_users] VALUES (N'7', N'2015-01-02 18:54:00', N'1', null, null, N'3', N'3');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_users]
-- ----------------------------
DROP TABLE [dbo].[sys_users]
GO
CREATE TABLE [dbo].[sys_users] (
[id] int NOT NULL ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[username] varchar(20) NOT NULL ,
[password] varchar(150) NOT NULL ,
[name] varchar(255) NOT NULL ,
[email] varchar(255) NOT NULL ,
[is_active] bit NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of sys_users
-- ----------------------------
INSERT INTO [dbo].[sys_users] VALUES (N'1', N'2014-12-28 11:03:00', null, N'2015-01-03 00:46:00', N'1', N'developer', N'81dc9bdb52d04dc20036dbd8313ed055', N'Developer', N'firman.data@gmail.com', N'1');
GO
INSERT INTO [dbo].[sys_users] VALUES (N'2', null, null, null, null, N'administrator', N'81dc9bdb52d04dc20036dbd8313ed055', N'Administrator', N'firman.data@yahoo.com', N'1');
GO
INSERT INTO [dbo].[sys_users] VALUES (N'3', N'2015-01-01 13:38:00', N'1', N'2015-01-02 18:54:00', N'1', N'wms', N'81dc9bdb52d04dc20036dbd8313ed055', N'WMS Administrator', N'firman.wms@gmail.com', N'1');
GO

-- ----------------------------
-- Indexes structure for table [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[cus_m_inventory_products]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_inventory_products]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_products] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_businesspartners]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[c_businesspartners]
-- ----------------------------
ALTER TABLE [dbo].[c_businesspartners] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderindetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderindetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderindetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderins]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderins]
-- ----------------------------
ALTER TABLE [dbo].[c_orderins] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderoutdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderoutdetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderoutdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderouts]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderouts]
-- ----------------------------
ALTER TABLE [dbo].[c_orderouts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_grids]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_grids]
-- ----------------------------
ALTER TABLE [dbo].[m_grids] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventories]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventories]
-- ----------------------------
ALTER TABLE [dbo].[m_inventories] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventorylogs]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventorylogs]
-- ----------------------------
ALTER TABLE [dbo].[m_inventorylogs] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_adjustdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_adjustdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_adjusts]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_adjusts]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_adjusts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_inbounddetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_inbounddetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_inbounds]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_inbounds]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_inbounds] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_movedetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_movedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_movedetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_moves]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_moves]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_moves] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_pickingdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_pickingdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_pickings]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_pickings]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_pickings] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_picklistdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_picklistdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_picklists]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_picklists]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_picklists] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_putawaydetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_putawaydetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_putaways]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_putaways]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_putaways] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_receivedetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_receivedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_receivedetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_receives]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_receives]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_receives] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_shipmentdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_shipments]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_shipments]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_shipments] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_productgroups]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_productgroups]
-- ----------------------------
ALTER TABLE [dbo].[m_productgroups] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_products]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_products]
-- ----------------------------
ALTER TABLE [dbo].[m_products] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_warehouses]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_warehouses]
-- ----------------------------
ALTER TABLE [dbo].[m_warehouses] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_accesscontrols]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_accesscontrols]
-- ----------------------------
ALTER TABLE [dbo].[sys_accesscontrols] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_actions]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_actions]
-- ----------------------------
ALTER TABLE [dbo].[sys_actions] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_ci_sessions]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_ci_sessions]
-- ----------------------------
ALTER TABLE [dbo].[sys_ci_sessions] ADD PRIMARY KEY ([session_id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_controls]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_controls]
-- ----------------------------
ALTER TABLE [dbo].[sys_controls] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_menus]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_menus]
-- ----------------------------
ALTER TABLE [dbo].[sys_menus] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_usergroups]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_usergroups]
-- ----------------------------
ALTER TABLE [dbo].[sys_usergroups] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_usergroup_users]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_usergroup_users]
-- ----------------------------
ALTER TABLE [dbo].[sys_usergroup_users] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_users]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_users]
-- ----------------------------
ALTER TABLE [dbo].[sys_users] ADD PRIMARY KEY ([id])
GO
