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

Date: 2015-01-13 23:10:33
*/


-- ----------------------------
-- Table structure for [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------
DROP TABLE [dbo].[cus_m_inventory_cyclecounts]
GO
CREATE TABLE [dbo].[cus_m_inventory_cyclecounts] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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
-- Table structure for [dbo].[cus_m_inventory_inbounddetails]
-- ----------------------------
DROP TABLE [dbo].[cus_m_inventory_inbounddetails]
GO
CREATE TABLE [dbo].[cus_m_inventory_inbounddetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_inventory_id] bigint NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[barcode] varchar(255) NOT NULL ,
[pallet] varchar(255) NULL ,
[carton_no] varchar(25) NULL ,
[packed_date] date NULL ,
[m_product_id] int NOT NULL 
)


GO

-- ----------------------------
-- Records of cus_m_inventory_inbounddetails
-- ----------------------------
INSERT INTO [dbo].[cus_m_inventory_inbounddetails] VALUES (N'2', N'2015-01-13 23:05:00', N'1', null, null, N'19', N'123.0000', N'P001CAR123150113', N'PLT01', N'CAR', N'2015-01-13', N'1');
GO
INSERT INTO [dbo].[cus_m_inventory_inbounddetails] VALUES (N'3', N'2015-01-13 23:06:00', N'1', null, null, N'20', N'111.0000', N'P001C01111150112', N'PLT02', N'C01', N'2015-01-12', N'1');
GO

-- ----------------------------
-- Table structure for [dbo].[cus_m_inventory_products]
-- ----------------------------
DROP TABLE [dbo].[cus_m_inventory_products]
GO
CREATE TABLE [dbo].[cus_m_inventory_products] (
[id] int NOT NULL IDENTITY(1,1) ,
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
[date_packed_end] int NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of cus_m_inventory_products
-- ----------------------------
INSERT INTO [dbo].[cus_m_inventory_products] VALUES (N'2', N'2015-01-04 19:45:00', N'1', null, null, N'SKU002', N'SKU Description 02', N'9', N'7', N'9', N'1', N'6', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_inventory_products] VALUES (N'3', N'2015-01-04 19:45:00', N'1', null, null, N'SKU003', N'SKU Description 03', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO

-- ----------------------------
-- Table structure for [dbo].[cus_m_products]
-- ----------------------------
DROP TABLE [dbo].[cus_m_products]
GO
CREATE TABLE [dbo].[cus_m_products] (
[id] int NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_product_id] int NOT NULL ,
[barcode_length] int NOT NULL DEFAULT ((0)) ,
[quantity_start] int NOT NULL DEFAULT ((0)) ,
[quantity_end] int NOT NULL DEFAULT ((0)) ,
[sku_start] int NOT NULL DEFAULT ((0)) ,
[sku_end] int NOT NULL DEFAULT ((0)) ,
[carton_start] int NOT NULL DEFAULT ((0)) ,
[carton_end] int NOT NULL DEFAULT ((0)) ,
[packed_date_start] int NOT NULL DEFAULT ((0)) ,
[packed_date_end] int NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of cus_m_products
-- ----------------------------
INSERT INTO [dbo].[cus_m_products] VALUES (N'1', N'2015-01-13 21:35:00', N'1', N'2015-01-13 23:02:00', N'1', N'1', N'16', N'8', N'10', N'1', N'4', N'5', N'7', N'11', N'16');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'2', N'2015-01-13 21:35:00', N'1', N'2015-01-13 22:48:00', N'1', N'2', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'3', N'2015-01-13 21:35:00', N'1', null, N'1', N'3', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'4', N'2015-01-13 21:36:00', N'1', null, N'1', N'4', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'5', N'2015-01-13 21:36:00', N'1', null, N'1', N'5', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'6', N'2015-01-13 21:36:00', N'1', null, N'1', N'6', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'7', N'2015-01-13 21:36:00', N'1', null, N'1', N'7', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'8', N'2015-01-13 21:36:00', N'1', null, N'1', N'8', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'9', N'2015-01-13 21:36:00', N'1', null, N'1', N'9', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO
INSERT INTO [dbo].[cus_m_products] VALUES (N'10', N'2015-01-13 21:36:00', N'1', null, N'1', N'10', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0', N'0');
GO

-- ----------------------------
-- Table structure for [dbo].[c_businesspartners]
-- ----------------------------
DROP TABLE [dbo].[c_businesspartners]
GO
CREATE TABLE [dbo].[c_businesspartners] (
[id] int NOT NULL IDENTITY(1,1) ,
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
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[c_orderins]
-- ----------------------------
DROP TABLE [dbo].[c_orderins]
GO
CREATE TABLE [dbo].[c_orderins] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[c_orderoutdetails]
-- ----------------------------
DROP TABLE [dbo].[c_orderoutdetails]
GO
CREATE TABLE [dbo].[c_orderoutdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[c_orderouts]
-- ----------------------------
DROP TABLE [dbo].[c_orderouts]
GO
CREATE TABLE [dbo].[c_orderouts] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_bomdetails]
-- ----------------------------
DROP TABLE [dbo].[m_bomdetails]
GO
CREATE TABLE [dbo].[m_bomdetails] (
[id] int NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_bom_id] int NOT NULL ,
[m_product_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_bomdetails
-- ----------------------------

-- ----------------------------
-- Table structure for [dbo].[m_boms]
-- ----------------------------
DROP TABLE [dbo].[m_boms]
GO
CREATE TABLE [dbo].[m_boms] (
[id] int NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[name] varchar(150) NOT NULL 
)


GO

-- ----------------------------
-- Records of m_boms
-- ----------------------------

-- ----------------------------
-- Table structure for [dbo].[m_grids]
-- ----------------------------
DROP TABLE [dbo].[m_grids]
GO
CREATE TABLE [dbo].[m_grids] (
[id] int NOT NULL IDENTITY(1,1) ,
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
[id] bigint NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_product_id] int NOT NULL ,
[m_grid_id] int NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) ,
[barcode] varchar(255) NULL ,
[pallet] varchar(255) NULL ,
[carton_no] varchar(25) NULL ,
[packed_date] date NULL 
)


GO

-- ----------------------------
-- Records of m_inventories
-- ----------------------------
INSERT INTO [dbo].[m_inventories] VALUES (N'3', N'2015-01-04 09:14:00', N'1', N'2015-01-06 10:06:00', N'1', N'1', N'9', N'1.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'4', N'2015-01-04 09:14:00', N'1', N'2015-01-06 10:05:00', N'1', N'2', N'19', N'5.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'5', N'2015-01-04 09:14:00', N'1', N'2015-01-06 10:05:00', N'1', N'3', N'21', N'5.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'13', N'2015-01-04 14:47:00', N'1', N'2015-01-06 10:05:00', N'1', N'4', N'9', N'1.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'14', N'2015-01-04 15:32:00', N'1', N'2015-01-06 10:05:00', N'1', N'2', N'19', N'1.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'15', N'2015-01-04 15:32:00', N'1', N'2015-01-06 10:05:00', N'1', N'3', N'21', N'8.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'16', N'2015-01-04 15:34:00', N'1', N'2015-01-06 10:05:00', N'1', N'2', N'19', N'4.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'17', N'2015-01-04 15:34:00', N'1', N'2015-01-06 10:05:00', N'1', N'3', N'21', N'7.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'19', N'2015-01-13 23:05:00', N'1', null, null, N'1', N'9', N'123.0000', null, null, null, null);
GO
INSERT INTO [dbo].[m_inventories] VALUES (N'20', N'2015-01-13 23:06:00', N'1', null, null, N'1', N'9', N'111.0000', N'P001C01111150112', N'PLT02', N'C01', N'2015-01-12');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventorylogs]
-- ----------------------------
DROP TABLE [dbo].[m_inventorylogs]
GO
CREATE TABLE [dbo].[m_inventorylogs] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'60', N'2015-01-06 10:05:00', N'1', null, null, N'10', N'4', N'9', N'1.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'61', N'2015-01-06 10:05:00', N'1', null, null, N'13', N'4', N'13', N'1.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'62', N'2015-01-06 10:05:00', N'1', null, null, N'11', N'5', N'25', N'9.0000', N'M_inventory_picklist PL002 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'63', N'2015-01-06 10:05:00', N'1', null, null, N'12', N'6', N'11', N'5.0000', N'M_inventory_picklist PL002 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'64', N'2015-01-06 10:05:00', N'1', null, null, N'3', N'1', N'10', N'1.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'65', N'2015-01-06 10:05:00', N'1', null, null, N'14', N'2', N'19', N'1.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'66', N'2015-01-06 10:05:00', N'1', null, null, N'16', N'2', N'20', N'4.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'67', N'2015-01-06 10:05:00', N'1', null, null, N'4', N'2', N'19', N'5.0000', N'M_inventory_picklist PL001 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'68', N'2015-01-06 10:05:00', N'1', null, null, N'5', N'3', N'22', N'5.0000', N'M_inventory_picklist PL002 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'69', N'2015-01-06 10:05:00', N'1', null, null, N'17', N'3', N'22', N'2.0000', N'M_inventory_picklist PL002 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'70', N'2015-01-06 10:05:00', N'1', null, null, N'15', N'3', N'21', N'3.0000', N'M_inventory_picklist PL002 Remove Pick List');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'72', N'2015-01-06 10:05:00', N'1', null, null, N'12', N'6', N'11', N'-4.0000', N'M_inventory_adjust A001 Remove Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'73', N'2015-01-06 10:05:00', N'1', null, null, N'11', N'5', N'25', N'-8.0000', N'M_inventory_adjust A001 Remove Adjustment');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'74', N'2015-01-06 10:06:00', N'1', null, null, N'10', N'4', N'9', N'-1.0000', N'M_inventory_inbound I002 Remove Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'75', N'2015-01-06 10:06:00', N'1', null, null, N'11', N'5', N'9', N'-2.0000', N'M_inventory_inbound I002 Remove Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'76', N'2015-01-06 10:06:00', N'1', null, null, N'12', N'6', N'9', N'-2.0000', N'M_inventory_inbound I002 Remove Inbound');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'79', N'2015-01-13 23:04:00', N'1', null, null, N'18', N'1', N'9', N'123.0000', N'Add Custom Inbound Detail ''P001CAR123150113''');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'80', N'2015-01-13 23:05:00', N'1', null, null, N'18', N'1', N'9', N'-123.0000', N'Remove Custom Inbound Detail ''''');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'81', N'2015-01-13 23:05:00', N'1', null, null, N'19', N'1', N'9', N'123.0000', N'Add Custom Inbound Detail ''P001CAR123150113''');
GO
INSERT INTO [dbo].[m_inventorylogs] VALUES (N'82', N'2015-01-13 23:06:00', N'1', null, null, N'20', N'1', N'9', N'111.0000', N'Add Custom Inbound Detail ''P001C01111150112''');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_adjustdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_adjustdetails]
GO
CREATE TABLE [dbo].[m_inventory_adjustdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_adjusts]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_adjusts]
GO
CREATE TABLE [dbo].[m_inventory_adjusts] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_inbounddetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_inbounddetails]
GO
CREATE TABLE [dbo].[m_inventory_inbounddetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_inbounds]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_inbounds]
GO
CREATE TABLE [dbo].[m_inventory_inbounds] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_movedetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_movedetails]
GO
CREATE TABLE [dbo].[m_inventory_movedetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_moves]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_moves]
GO
CREATE TABLE [dbo].[m_inventory_moves] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_pickingdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_pickingdetails]
GO
CREATE TABLE [dbo].[m_inventory_pickingdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_pickings]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_pickings]
GO
CREATE TABLE [dbo].[m_inventory_pickings] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_picklistdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_picklistdetails]
GO
CREATE TABLE [dbo].[m_inventory_picklistdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_picklists]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_picklists]
GO
CREATE TABLE [dbo].[m_inventory_picklists] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_putawaydetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_putawaydetails]
GO
CREATE TABLE [dbo].[m_inventory_putawaydetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'2', N'2015-01-04 14:46:00', N'1', null, null, N'1', N'4', N'9', N'19', N'10.0000', N'10.0000');
GO
INSERT INTO [dbo].[m_inventory_putawaydetails] VALUES (N'3', N'2015-01-04 14:46:00', N'1', null, null, N'1', N'5', N'9', N'21', N'20.0000', N'20.0000');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_putaways]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_putaways]
GO
CREATE TABLE [dbo].[m_inventory_putaways] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[m_inventory_putaways] VALUES (N'1', N'2015-01-04 14:46:00', N'1', N'2015-01-06 10:06:00', N'1', N'PA001', N'2015-01-04');
GO

-- ----------------------------
-- Table structure for [dbo].[m_inventory_receivedetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_receivedetails]
GO
CREATE TABLE [dbo].[m_inventory_receivedetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_receives]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_receives]
GO
CREATE TABLE [dbo].[m_inventory_receives] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_shipmentdetails]
GO
CREATE TABLE [dbo].[m_inventory_shipmentdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_inventory_shipments]
-- ----------------------------
DROP TABLE [dbo].[m_inventory_shipments]
GO
CREATE TABLE [dbo].[m_inventory_shipments] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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

-- ----------------------------
-- Table structure for [dbo].[m_productgroups]
-- ----------------------------
DROP TABLE [dbo].[m_productgroups]
GO
CREATE TABLE [dbo].[m_productgroups] (
[id] int NOT NULL IDENTITY(1,1) ,
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
[id] int NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[m_products] VALUES (N'2', N'2015-01-03 16:44:00', N'1', N'2015-01-13 21:35:00', N'1', N'P002', N'Product 02', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'3', N'2015-01-03 19:08:00', N'1', N'2015-01-03 19:10:00', N'1', N'P003', N'Product 03', N'3');
GO
INSERT INTO [dbo].[m_products] VALUES (N'4', N'2015-01-03 19:09:00', N'1', N'2015-01-03 19:11:00', N'1', N'P004', N'Product 04', N'1');
GO
INSERT INTO [dbo].[m_products] VALUES (N'5', N'2015-01-03 19:09:00', N'1', N'2015-01-03 19:11:00', N'1', N'P005', N'Product 05', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'6', N'2015-01-03 19:09:00', N'1', N'2015-01-13 21:36:00', N'1', N'P006', N'Product 06', null);
GO
INSERT INTO [dbo].[m_products] VALUES (N'7', N'2015-01-03 19:10:00', N'1', N'2015-01-03 19:11:00', N'1', N'P007', N'Product 07', N'1');
GO
INSERT INTO [dbo].[m_products] VALUES (N'8', N'2015-01-03 19:10:00', N'1', N'2015-01-03 19:11:00', N'1', N'P008', N'Product 08', N'2');
GO
INSERT INTO [dbo].[m_products] VALUES (N'9', N'2015-01-03 19:10:00', N'1', N'2015-01-04 14:48:00', N'1', N'P009', N'Product 09', N'4');
GO
INSERT INTO [dbo].[m_products] VALUES (N'10', N'2015-01-03 19:10:00', N'1', N'2015-01-13 21:36:00', N'1', N'P010', N'Product 10', N'1');
GO

-- ----------------------------
-- Table structure for [dbo].[m_warehouses]
-- ----------------------------
DROP TABLE [dbo].[m_warehouses]
GO
CREATE TABLE [dbo].[m_warehouses] (
[id] int NOT NULL IDENTITY(1,1) ,
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
-- Table structure for [dbo].[m_workorderdetails]
-- ----------------------------
DROP TABLE [dbo].[m_workorderdetails]
GO
CREATE TABLE [dbo].[m_workorderdetails] (
[id] bigint NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[m_workorder_id] bigint NOT NULL ,
[c_orderindetail_id] bigint NOT NULL ,
[work_date] date NOT NULL ,
[quantity] decimal(12,4) NOT NULL DEFAULT ((0)) 
)


GO

-- ----------------------------
-- Records of m_workorderdetails
-- ----------------------------

-- ----------------------------
-- Table structure for [dbo].[m_workorders]
-- ----------------------------
DROP TABLE [dbo].[m_workorders]
GO
CREATE TABLE [dbo].[m_workorders] (
[id] bigint NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[code] varchar(30) NOT NULL ,
[workorder_date] date NOT NULL 
)


GO

-- ----------------------------
-- Records of m_workorders
-- ----------------------------

-- ----------------------------
-- Table structure for [dbo].[sys_accesscontrols]
-- ----------------------------
DROP TABLE [dbo].[sys_accesscontrols]
GO
CREATE TABLE [dbo].[sys_accesscontrols] (
[id] bigint NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'1', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'2', null, null, null, null, N'1', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'3', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'1', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'4', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'1', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'5', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'1', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'6', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'2', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'7', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'2', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'8', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'2', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'9', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'2', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'10', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'3', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'11', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'3', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'12', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'3', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'13', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'3', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'14', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'4', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'15', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'4', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'16', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'4', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'17', N'2015-01-03 00:46:00', null, N'2015-01-13 22:26:00', N'1', N'1', N'4', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'18', N'2015-01-01 13:38:00', N'1', N'2015-01-01 13:38:00', N'1', N'3', N'1', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'22', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'5', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'23', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'5', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'24', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'5', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'25', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'5', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'26', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'6', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'27', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'6', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'28', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'6', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'29', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'6', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'30', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'8', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'31', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'8', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'32', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'8', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'33', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'8', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'34', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'9', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'35', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'9', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'36', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'9', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'37', N'2015-01-03 00:46:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'9', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'38', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'10', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'39', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'10', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'40', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'10', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'41', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'10', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'42', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'11', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'43', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'11', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'44', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'11', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'45', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'11', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'46', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'12', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'47', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'12', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'48', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'12', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'49', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'12', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'50', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'13', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'51', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'13', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'52', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'13', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'53', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'13', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'54', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'14', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'55', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'14', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'56', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'14', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'57', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'14', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'58', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'15', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'59', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'15', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'60', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'15', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'61', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'15', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'62', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'16', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'63', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'16', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'64', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'16', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'65', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'16', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'66', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'17', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'67', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'17', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'68', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'17', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'69', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'17', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'70', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'18', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'71', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'18', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'72', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'18', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'73', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'18', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'74', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'19', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'75', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'19', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'76', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'19', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'77', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'19', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'78', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'20', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'79', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'20', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'80', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'20', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'81', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'20', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'82', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'21', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'83', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'21', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'84', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'21', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'85', N'2015-01-03 01:02:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'21', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'86', N'2015-01-03 16:40:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'22', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'87', N'2015-01-03 16:40:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'22', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'88', N'2015-01-03 16:40:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'22', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'89', N'2015-01-03 16:40:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'22', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'90', N'2015-01-03 19:22:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'23', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'91', N'2015-01-03 19:22:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'23', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'92', N'2015-01-03 19:22:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'23', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'93', N'2015-01-03 19:22:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'23', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'94', N'2015-01-03 20:04:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'24', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'95', N'2015-01-03 20:04:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'24', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'96', N'2015-01-03 20:04:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'24', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'97', N'2015-01-03 20:04:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'24', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'98', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'25', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'99', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'25', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'100', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'25', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'101', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'25', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'102', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'26', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'103', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'26', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'104', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'26', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'105', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'26', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'106', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'27', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'107', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'27', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'108', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'27', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'109', N'2015-01-04 19:30:00', N'1', N'2015-01-13 22:26:00', N'1', N'1', N'27', N'4', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'110', N'2015-01-13 22:26:00', N'1', null, null, N'1', N'28', N'1', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'111', N'2015-01-13 22:26:00', N'1', null, null, N'1', N'28', N'2', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'112', N'2015-01-13 22:26:00', N'1', null, null, N'1', N'28', N'3', N'0');
GO
INSERT INTO [dbo].[sys_accesscontrols] VALUES (N'113', N'2015-01-13 22:26:00', N'1', null, null, N'1', N'28', N'4', N'0');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_actions]
-- ----------------------------
DROP TABLE [dbo].[sys_actions]
GO
CREATE TABLE [dbo].[sys_actions] (
[id] int NOT NULL IDENTITY(1,1) ,
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
-- Table structure for [dbo].[sys_calendars]
-- ----------------------------
DROP TABLE [dbo].[sys_calendars]
GO
CREATE TABLE [dbo].[sys_calendars] (
[id] int NOT NULL IDENTITY(1,1) ,
[created] smalldatetime NULL ,
[created_by] int NULL ,
[updated] smalldatetime NULL ,
[updated_by] int NULL ,
[work_date] date NOT NULL ,
[name] varchar(150) NULL 
)


GO

-- ----------------------------
-- Records of sys_calendars
-- ----------------------------

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
INSERT INTO [dbo].[sys_ci_sessions] VALUES (N'20b6b9391f543f6b0fd74c4d4791b063', N'127.0.0.1', N'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0', N'1421165163', N'a:7:{s:9:"user_data";s:0:"";s:7:"user_id";i:1;s:8:"username";s:9:"developer";s:4:"name";s:9:"Developer";s:9:"logged_in";b:1;s:5:"menus";a:5:{i:0;O:8:"stdClass":7:{s:2:"id";i:1;s:4:"name";s:4:"Home";s:7:"control";s:11:"system/home";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:2;s:4:"name";s:17:"Application Setup";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:3:{i:0;O:8:"stdClass":7:{s:2:"id";i:3;s:4:"name";s:15:"User Management";s:7:"control";s:11:"system/user";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:4;s:4:"name";s:14:"Access Control";s:7:"control";s:20:"system/accesscontrol";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:2;O:8:"stdClass":7:{s:2:"id";i:5;s:4:"name";s:18:"Menu Configuration";s:7:"control";s:11:"system/menu";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:2;O:8:"stdClass":7:{s:2:"id";i:6;s:4:"name";s:16:"Order Management";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:3:{i:0;O:8:"stdClass":7:{s:2:"id";i:20;s:4:"name";s:5:"Setup";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:1:{i:0;O:8:"stdClass":7:{s:2:"id";i:21;s:4:"name";s:16:"Business Partner";s:7:"control";s:20:"core/businesspartner";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:1;O:8:"stdClass":7:{s:2:"id";i:10;s:4:"name";s:2:"In";s:7:"control";s:12:"core/orderin";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:2;O:8:"stdClass":7:{s:2:"id";i:11;s:4:"name";s:3:"Out";s:7:"control";s:13:"core/orderout";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:3;O:8:"stdClass":7:{s:2:"id";i:9;s:4:"name";s:10:"Production";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:4:{i:0;O:8:"stdClass":7:{s:2:"id";i:16;s:4:"name";s:5:"Setup";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:2:{i:0;O:8:"stdClass":7:{s:2:"id";i:17;s:4:"name";s:7:"Product";s:7:"control";s:16:"material/product";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:18;s:4:"name";s:9:"Warehouse";s:7:"control";s:18:"material/warehouse";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:1;O:8:"stdClass":7:{s:2:"id";i:13;s:4:"name";s:2:"In";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:3:{i:0;O:8:"stdClass":7:{s:2:"id";i:14;s:4:"name";s:7:"Receive";s:7:"control";s:26:"material/inventory_receive";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:15;s:4:"name";s:7:"Inbound";s:7:"control";s:26:"material/inventory_inbound";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:2;O:8:"stdClass":7:{s:2:"id";i:35;s:4:"name";s:12:"Live Inbound";s:7:"control";s:24:"custom/inventory_inbound";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:2;O:8:"stdClass":7:{s:2:"id";i:12;s:4:"name";s:9:"Inventory";s:7:"control";s:18:"material/inventory";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:4:{i:0;O:8:"stdClass":7:{s:2:"id";i:22;s:4:"name";s:7:"Putaway";s:7:"control";s:26:"material/inventory_putaway";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:23;s:4:"name";s:4:"Move";s:7:"control";s:23:"material/inventory_move";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:2;O:8:"stdClass":7:{s:2:"id";i:24;s:4:"name";s:6:"Adjust";s:7:"control";s:25:"material/inventory_adjust";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:3;O:8:"stdClass":7:{s:2:"id";i:28;s:4:"name";s:3:"Log";s:7:"control";s:21:"material/inventorylog";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}i:3;O:8:"stdClass":7:{s:2:"id";i:19;s:4:"name";s:3:"Out";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:3:{i:0;O:8:"stdClass":7:{s:2:"id";i:25;s:4:"name";s:9:"Pick List";s:7:"control";s:27:"material/inventory_picklist";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:26;s:4:"name";s:7:"Picking";s:7:"control";s:26:"material/inventory_picking";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:2;O:8:"stdClass":7:{s:2:"id";i:27;s:4:"name";s:8:"Shipment";s:7:"control";s:27:"material/inventory_shipment";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}}}i:4;O:8:"stdClass":7:{s:2:"id";i:29;s:4:"name";s:6:"Custom";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:1:{i:0;O:8:"stdClass":7:{s:2:"id";i:30;s:4:"name";s:3:"Etc";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:2:{i:0;O:8:"stdClass":7:{s:2:"id";i:31;s:4:"name";s:7:"Product";s:7:"control";s:24:"custom/inventory_product";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:32;s:4:"name";s:11:"Cycle Count";s:7:"control";N;s:6:"action";N;s:3:"url";N;s:3:"css";N;s:6:"childs";a:2:{i:0;O:8:"stdClass":7:{s:2:"id";i:33;s:4:"name";s:3:"Out";s:7:"control";s:31:"custom/inventory_cyclecount_out";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}i:1;O:8:"stdClass":7:{s:2:"id";i:34;s:4:"name";s:2:"In";s:7:"control";s:30:"custom/inventory_cyclecount_in";s:6:"action";s:5:"index";s:3:"url";N;s:3:"css";N;s:6:"childs";a:0:{}}}}}}}}}s:14:"accesscontrols";a:27:{s:20:"core/businesspartner";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:12:"core/orderin";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:13:"core/orderout";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:30:"custom/inventory_cyclecount_in";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:31:"custom/inventory_cyclecount_out";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:24:"custom/inventory_inbound";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:24:"custom/inventory_product";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:13:"material/grid";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:18:"material/inventory";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:25:"material/inventory_adjust";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:26:"material/inventory_inbound";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:23:"material/inventory_move";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:26:"material/inventory_picking";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:27:"material/inventory_picklist";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:26:"material/inventory_putaway";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:26:"material/inventory_receive";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:27:"material/inventory_shipment";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:21:"material/inventorylog";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:16:"material/product";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:21:"material/productgroup";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:18:"material/warehouse";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:20:"system/accesscontrol";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:13:"system/action";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:14:"system/control";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:11:"system/home";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:11:"system/menu";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}s:11:"system/user";a:4:{s:6:"delete";b:1;s:5:"index";b:1;s:6:"insert";b:1;s:6:"update";b:1;}}}');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_controls]
-- ----------------------------
DROP TABLE [dbo].[sys_controls]
GO
CREATE TABLE [dbo].[sys_controls] (
[id] int NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[sys_controls] VALUES (N'28', N'2015-01-13 22:26:00', N'1', null, null, N'custom/inventory_inbound');
GO

-- ----------------------------
-- Table structure for [dbo].[sys_menus]
-- ----------------------------
DROP TABLE [dbo].[sys_menus]
GO
CREATE TABLE [dbo].[sys_menus] (
[id] int NOT NULL IDENTITY(1,1) ,
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
INSERT INTO [dbo].[sys_menus] VALUES (N'35', N'2015-01-13 22:27:00', N'1', null, null, N'13', N'28', N'1', N'Live Inbound', N'3', null, null);
GO

-- ----------------------------
-- Table structure for [dbo].[sys_usergroups]
-- ----------------------------
DROP TABLE [dbo].[sys_usergroups]
GO
CREATE TABLE [dbo].[sys_usergroups] (
[id] int NOT NULL IDENTITY(1,1) ,
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
[id] int NOT NULL IDENTITY(1,1) ,
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
[id] int NOT NULL IDENTITY(1,1) ,
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
CREATE INDEX [cus_m_inventory_cyclecou_idx01] ON [dbo].[cus_m_inventory_cyclecounts]
([cus_m_inventory_product_id] ASC) 
GO
CREATE UNIQUE INDEX [cus_m_inventory_cyclecou_idx02] ON [dbo].[cus_m_inventory_cyclecounts]
([barcode] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[cus_m_inventory_inbounddetails]
-- ----------------------------
CREATE INDEX [cus_m_inventory_inboundd_idx01] ON [dbo].[cus_m_inventory_inbounddetails]
([m_inventory_id] ASC) 
GO
CREATE INDEX [cus_m_inventory_inboundd_idx02] ON [dbo].[cus_m_inventory_inbounddetails]
([m_product_id] ASC) 
GO
CREATE UNIQUE INDEX [cus_m_inventory_inboundd_idx03] ON [dbo].[cus_m_inventory_inbounddetails]
([barcode] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_inventory_inbounddetails]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[cus_m_inventory_products]
-- ----------------------------
CREATE UNIQUE INDEX [cus_m_inventory_products_idx01] ON [dbo].[cus_m_inventory_products]
([sku] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_inventory_products]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_products] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[cus_m_products]
-- ----------------------------
CREATE UNIQUE INDEX [cus_m_products_idx01] ON [dbo].[cus_m_products]
([m_product_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[cus_m_products]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_products] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_businesspartners]
-- ----------------------------
CREATE UNIQUE INDEX [c_businesspartners_idx01] ON [dbo].[c_businesspartners]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_businesspartners]
-- ----------------------------
ALTER TABLE [dbo].[c_businesspartners] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderindetails]
-- ----------------------------
CREATE INDEX [c_orderindetails_idx01] ON [dbo].[c_orderindetails]
([c_orderin_id] ASC) 
GO
CREATE INDEX [c_orderindetails_idx02] ON [dbo].[c_orderindetails]
([m_product_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderindetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderindetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderins]
-- ----------------------------
CREATE UNIQUE INDEX [c_orderins_idx01] ON [dbo].[c_orderins]
([code] ASC) 
GO
CREATE INDEX [c_orderins_idx02] ON [dbo].[c_orderins]
([c_businesspartner_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderins]
-- ----------------------------
ALTER TABLE [dbo].[c_orderins] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderoutdetails]
-- ----------------------------
CREATE INDEX [c_orderoutdetails_idx01] ON [dbo].[c_orderoutdetails]
([c_orderout_id] ASC) 
GO
CREATE INDEX [c_orderoutdetails_idx02] ON [dbo].[c_orderoutdetails]
([m_product_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderoutdetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderoutdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[c_orderouts]
-- ----------------------------
CREATE UNIQUE INDEX [c_orderouts_idx01] ON [dbo].[c_orderouts]
([code] ASC) 
GO
CREATE INDEX [c_orderouts_idx02] ON [dbo].[c_orderouts]
([c_businesspartner_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[c_orderouts]
-- ----------------------------
ALTER TABLE [dbo].[c_orderouts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_bomdetails]
-- ----------------------------

-- ----------------------------
-- Primary Key structure for table [dbo].[m_bomdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_bomdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_boms]
-- ----------------------------
CREATE UNIQUE INDEX [m_boms_idx01] ON [dbo].[m_boms]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_boms]
-- ----------------------------
ALTER TABLE [dbo].[m_boms] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_grids]
-- ----------------------------
CREATE INDEX [m_grids_idx01] ON [dbo].[m_grids]
([m_warehouse_id] ASC) 
GO
CREATE INDEX [m_grids_idx02] ON [dbo].[m_grids]
([m_productgroup_id] ASC) 
GO
CREATE UNIQUE INDEX [m_grids_idx03] ON [dbo].[m_grids]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_grids]
-- ----------------------------
ALTER TABLE [dbo].[m_grids] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventories]
-- ----------------------------
CREATE INDEX [m_inventories_idx01] ON [dbo].[m_inventories]
([m_product_id] ASC) 
GO
CREATE INDEX [m_inventories_idx02] ON [dbo].[m_inventories]
([m_grid_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventories]
-- ----------------------------
ALTER TABLE [dbo].[m_inventories] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventorylogs]
-- ----------------------------
CREATE INDEX [m_inventorylogs_idx01] ON [dbo].[m_inventorylogs]
([m_inventory_id] ASC) 
GO
CREATE INDEX [m_inventorylogs_idx02] ON [dbo].[m_inventorylogs]
([m_product_id] ASC) 
GO
CREATE INDEX [m_inventorylogs_idx03] ON [dbo].[m_inventorylogs]
([m_grid_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventorylogs]
-- ----------------------------
ALTER TABLE [dbo].[m_inventorylogs] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_adjustdetails]
-- ----------------------------
CREATE INDEX [m_inventory_adjustdetail_idx01] ON [dbo].[m_inventory_adjustdetails]
([m_inventory_adjust_id] ASC) 
GO
CREATE INDEX [m_inventory_adjustdetail_idx02] ON [dbo].[m_inventory_adjustdetails]
([m_inventory_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_adjustdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_adjusts]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_adjusts_idx01] ON [dbo].[m_inventory_adjusts]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_adjusts]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_adjusts] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_inbounddetails]
-- ----------------------------
CREATE INDEX [m_inventory_inbounddetai_idx01] ON [dbo].[m_inventory_inbounddetails]
([m_inventory_inbound_id] ASC) 
GO
CREATE INDEX [m_inventory_inbounddetai_idx02] ON [dbo].[m_inventory_inbounddetails]
([m_inventory_receivedetail_id] ASC) 
GO
CREATE INDEX [m_inventory_inbounddetai_idx03] ON [dbo].[m_inventory_inbounddetails]
([m_inventory_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_inbounddetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_inbounds]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_inbounds_idx01] ON [dbo].[m_inventory_inbounds]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_inbounds]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_inbounds] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_movedetails]
-- ----------------------------
CREATE INDEX [m_inventory_movedetails_idx01] ON [dbo].[m_inventory_movedetails]
([m_inventory_move_id] ASC) 
GO
CREATE INDEX [m_inventory_movedetails_idx02] ON [dbo].[m_inventory_movedetails]
([m_inventory_id] ASC) 
GO
CREATE INDEX [m_inventory_movedetails_idx03] ON [dbo].[m_inventory_movedetails]
([m_gridfrom_id] ASC) 
GO
CREATE INDEX [m_inventory_movedetails_idx04] ON [dbo].[m_inventory_movedetails]
([m_gridto_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_movedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_movedetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_moves]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_moves_idx01] ON [dbo].[m_inventory_moves]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_moves]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_moves] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_pickingdetails]
-- ----------------------------
CREATE INDEX [m_inventory_pickingdetai_idx01] ON [dbo].[m_inventory_pickingdetails]
([m_inventory_picking_id] ASC) 
GO
CREATE INDEX [m_inventory_pickingdetai_idx02] ON [dbo].[m_inventory_pickingdetails]
([m_inventory_picklistdetail_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_pickingdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_pickings]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_pickings_idx01] ON [dbo].[m_inventory_pickings]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_pickings]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_pickings] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_picklistdetails]
-- ----------------------------
CREATE INDEX [m_inventory_picklistdeta_idx01] ON [dbo].[m_inventory_picklistdetails]
([m_inventory_picklist_id] ASC) 
GO
CREATE INDEX [m_inventory_picklistdeta_idx02] ON [dbo].[m_inventory_picklistdetails]
([c_orderoutdetail_id] ASC) 
GO
CREATE INDEX [m_inventory_picklistdeta_idx03] ON [dbo].[m_inventory_picklistdetails]
([m_inventory_id] ASC) 
GO
CREATE INDEX [m_inventory_picklistdeta_idx04] ON [dbo].[m_inventory_picklistdetails]
([m_grid_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_picklistdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_picklists]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_picklists_idx01] ON [dbo].[m_inventory_picklists]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_picklists]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_picklists] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_putawaydetails]
-- ----------------------------
CREATE INDEX [m_inventory_putawaydetai_idx01] ON [dbo].[m_inventory_putawaydetails]
([m_inventory_putaway_id] ASC) 
GO
CREATE INDEX [m_inventory_putawaydetai_idx02] ON [dbo].[m_inventory_putawaydetails]
([m_inventory_id] ASC) 
GO
CREATE INDEX [m_inventory_putawaydetai_idx03] ON [dbo].[m_inventory_putawaydetails]
([m_gridfrom_id] ASC) 
GO
CREATE INDEX [m_inventory_putawaydetai_idx04] ON [dbo].[m_inventory_putawaydetails]
([m_gridto_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_putawaydetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_putaways]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_putaways_idx01] ON [dbo].[m_inventory_putaways]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_putaways]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_putaways] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_receivedetails]
-- ----------------------------
CREATE INDEX [m_inventory_receivedetai_idx01] ON [dbo].[m_inventory_receivedetails]
([m_inventory_receive_id] ASC) 
GO
CREATE INDEX [m_inventory_receivedetai_idx02] ON [dbo].[m_inventory_receivedetails]
([c_orderindetail_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_receivedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_receivedetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_receives]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_receives_idx01] ON [dbo].[m_inventory_receives]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_receives]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_receives] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
CREATE INDEX [m_inventory_shipmentdeta_idx01] ON [dbo].[m_inventory_shipmentdetails]
([m_inventory_shipment_id] ASC) 
GO
CREATE INDEX [m_inventory_shipmentdeta_idx02] ON [dbo].[m_inventory_shipmentdetails]
([m_inventory_pickingdetail_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_inventory_shipments]
-- ----------------------------
CREATE UNIQUE INDEX [m_inventory_shipments_idx01] ON [dbo].[m_inventory_shipments]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_inventory_shipments]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_shipments] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_productgroups]
-- ----------------------------
CREATE UNIQUE INDEX [m_productgroups_idx01] ON [dbo].[m_productgroups]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_productgroups]
-- ----------------------------
ALTER TABLE [dbo].[m_productgroups] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_products]
-- ----------------------------
CREATE UNIQUE INDEX [m_products_idx01] ON [dbo].[m_products]
([code] ASC) 
GO
CREATE INDEX [m_products_idx02] ON [dbo].[m_products]
([m_productgroup_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_products]
-- ----------------------------
ALTER TABLE [dbo].[m_products] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_warehouses]
-- ----------------------------
CREATE UNIQUE INDEX [m_warehouses_idx01] ON [dbo].[m_warehouses]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_warehouses]
-- ----------------------------
ALTER TABLE [dbo].[m_warehouses] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_workorderdetails]
-- ----------------------------
CREATE INDEX [m_workorderdetails_idx01] ON [dbo].[m_workorderdetails]
([m_workorder_id] ASC) 
GO
CREATE INDEX [m_workorderdetails_idx02] ON [dbo].[m_workorderdetails]
([c_orderindetail_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_workorderdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_workorderdetails] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[m_workorders]
-- ----------------------------
CREATE UNIQUE INDEX [m_workorders_idx01] ON [dbo].[m_workorders]
([code] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[m_workorders]
-- ----------------------------
ALTER TABLE [dbo].[m_workorders] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_accesscontrols]
-- ----------------------------
CREATE INDEX [sys_accesscontrols_idx01] ON [dbo].[sys_accesscontrols]
([sys_usergroup_id] ASC) 
GO
CREATE INDEX [sys_accesscontrols_idx02] ON [dbo].[sys_accesscontrols]
([sys_control_id] ASC) 
GO
CREATE INDEX [sys_accesscontrols_idx03] ON [dbo].[sys_accesscontrols]
([sys_action_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_accesscontrols]
-- ----------------------------
ALTER TABLE [dbo].[sys_accesscontrols] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_actions]
-- ----------------------------
CREATE UNIQUE INDEX [sys_actions_idx01] ON [dbo].[sys_actions]
([name] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_actions]
-- ----------------------------
ALTER TABLE [dbo].[sys_actions] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_calendars]
-- ----------------------------
CREATE UNIQUE INDEX [sys_calendars_idx01] ON [dbo].[sys_calendars]
([work_date] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_calendars]
-- ----------------------------
ALTER TABLE [dbo].[sys_calendars] ADD PRIMARY KEY ([id])
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
CREATE UNIQUE INDEX [sys_controls_idx01] ON [dbo].[sys_controls]
([name] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_controls]
-- ----------------------------
ALTER TABLE [dbo].[sys_controls] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_menus]
-- ----------------------------
CREATE INDEX [sys_menus_idx01] ON [dbo].[sys_menus]
([parent_id] ASC) 
GO
CREATE INDEX [sys_menus_idx02] ON [dbo].[sys_menus]
([sys_control_id] ASC) 
GO
CREATE INDEX [sys_menus_idx03] ON [dbo].[sys_menus]
([sys_action_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_menus]
-- ----------------------------
ALTER TABLE [dbo].[sys_menus] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_usergroups]
-- ----------------------------
CREATE UNIQUE INDEX [sys_usergroups_idx01] ON [dbo].[sys_usergroups]
([name] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_usergroups]
-- ----------------------------
ALTER TABLE [dbo].[sys_usergroups] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_usergroup_users]
-- ----------------------------
CREATE INDEX [sys_usergroup_users_idx01] ON [dbo].[sys_usergroup_users]
([sys_usergroup_id] ASC) 
GO
CREATE INDEX [sys_usergroup_users_idx02] ON [dbo].[sys_usergroup_users]
([sys_user_id] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_usergroup_users]
-- ----------------------------
ALTER TABLE [dbo].[sys_usergroup_users] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Indexes structure for table [dbo].[sys_users]
-- ----------------------------
CREATE UNIQUE INDEX [sys_users_idx01] ON [dbo].[sys_users]
([username] ASC) 
GO
CREATE UNIQUE INDEX [sys_users_idx02] ON [dbo].[sys_users]
([email] ASC) 
GO

-- ----------------------------
-- Primary Key structure for table [dbo].[sys_users]
-- ----------------------------
ALTER TABLE [dbo].[sys_users] ADD PRIMARY KEY ([id])
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[cus_m_inventory_cyclecounts]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] ADD FOREIGN KEY ([cus_m_inventory_product_id]) REFERENCES [dbo].[cus_m_inventory_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[cus_m_inventory_inbounddetails]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[cus_m_products]
-- ----------------------------
ALTER TABLE [dbo].[cus_m_products] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_orderindetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderindetails] ADD FOREIGN KEY ([c_orderin_id]) REFERENCES [dbo].[c_orderins] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[c_orderindetails] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_orderins]
-- ----------------------------
ALTER TABLE [dbo].[c_orderins] ADD FOREIGN KEY ([c_businesspartner_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_orderoutdetails]
-- ----------------------------
ALTER TABLE [dbo].[c_orderoutdetails] ADD FOREIGN KEY ([c_orderout_id]) REFERENCES [dbo].[c_orderouts] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[c_orderoutdetails] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[c_orderouts]
-- ----------------------------
ALTER TABLE [dbo].[c_orderouts] ADD FOREIGN KEY ([c_businesspartner_id]) REFERENCES [dbo].[c_businesspartners] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_grids]
-- ----------------------------
ALTER TABLE [dbo].[m_grids] ADD FOREIGN KEY ([m_warehouse_id]) REFERENCES [dbo].[m_warehouses] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_grids] ADD FOREIGN KEY ([m_productgroup_id]) REFERENCES [dbo].[m_productgroups] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventories]
-- ----------------------------
ALTER TABLE [dbo].[m_inventories] ADD FOREIGN KEY ([m_product_id]) REFERENCES [dbo].[m_products] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventories] ADD FOREIGN KEY ([m_grid_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_adjustdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD FOREIGN KEY ([m_inventory_adjust_id]) REFERENCES [dbo].[m_inventory_adjusts] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_inbounddetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD FOREIGN KEY ([m_inventory_inbound_id]) REFERENCES [dbo].[m_inventory_inbounds] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD FOREIGN KEY ([m_inventory_receivedetail_id]) REFERENCES [dbo].[m_inventory_receivedetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_movedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_movedetails] ADD FOREIGN KEY ([m_inventory_move_id]) REFERENCES [dbo].[m_inventory_moves] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_movedetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_movedetails] ADD FOREIGN KEY ([m_gridfrom_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_movedetails] ADD FOREIGN KEY ([m_gridto_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_pickingdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD FOREIGN KEY ([m_inventory_picking_id]) REFERENCES [dbo].[m_inventory_pickings] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD FOREIGN KEY ([m_inventory_picklistdetail_id]) REFERENCES [dbo].[m_inventory_picklistdetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_picklistdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD FOREIGN KEY ([m_inventory_picklist_id]) REFERENCES [dbo].[m_inventory_picklists] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD FOREIGN KEY ([c_orderoutdetail_id]) REFERENCES [dbo].[c_orderoutdetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD FOREIGN KEY ([m_grid_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_putawaydetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD FOREIGN KEY ([m_inventory_putaway_id]) REFERENCES [dbo].[m_inventory_putaways] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD FOREIGN KEY ([m_inventory_id]) REFERENCES [dbo].[m_inventories] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD FOREIGN KEY ([m_gridfrom_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD FOREIGN KEY ([m_gridto_id]) REFERENCES [dbo].[m_grids] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_receivedetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_receivedetails] ADD FOREIGN KEY ([m_inventory_receive_id]) REFERENCES [dbo].[m_inventory_receives] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_receivedetails] ADD FOREIGN KEY ([c_orderindetail_id]) REFERENCES [dbo].[c_orderindetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_inventory_shipmentdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD FOREIGN KEY ([m_inventory_shipment_id]) REFERENCES [dbo].[m_inventory_shipments] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD FOREIGN KEY ([m_inventory_pickingdetail_id]) REFERENCES [dbo].[m_inventory_pickingdetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_products]
-- ----------------------------
ALTER TABLE [dbo].[m_products] ADD FOREIGN KEY ([m_productgroup_id]) REFERENCES [dbo].[m_productgroups] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[m_workorderdetails]
-- ----------------------------
ALTER TABLE [dbo].[m_workorderdetails] ADD FOREIGN KEY ([m_workorder_id]) REFERENCES [dbo].[m_workorders] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[m_workorderdetails] ADD FOREIGN KEY ([c_orderindetail_id]) REFERENCES [dbo].[c_orderindetails] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[sys_accesscontrols]
-- ----------------------------
ALTER TABLE [dbo].[sys_accesscontrols] ADD FOREIGN KEY ([sys_usergroup_id]) REFERENCES [dbo].[sys_usergroups] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[sys_accesscontrols] ADD FOREIGN KEY ([sys_control_id]) REFERENCES [dbo].[sys_controls] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[sys_accesscontrols] ADD FOREIGN KEY ([sys_action_id]) REFERENCES [dbo].[sys_actions] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[sys_menus]
-- ----------------------------
ALTER TABLE [dbo].[sys_menus] ADD FOREIGN KEY ([parent_id]) REFERENCES [dbo].[sys_menus] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[sys_menus] ADD FOREIGN KEY ([sys_control_id]) REFERENCES [dbo].[sys_controls] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[sys_menus] ADD FOREIGN KEY ([sys_action_id]) REFERENCES [dbo].[sys_actions] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

-- ----------------------------
-- Foreign Key structure for table [dbo].[sys_usergroup_users]
-- ----------------------------
ALTER TABLE [dbo].[sys_usergroup_users] ADD FOREIGN KEY ([sys_usergroup_id]) REFERENCES [dbo].[sys_usergroups] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
ALTER TABLE [dbo].[sys_usergroup_users] ADD FOREIGN KEY ([sys_user_id]) REFERENCES [dbo].[sys_users] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
GO
