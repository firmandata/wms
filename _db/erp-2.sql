USE [master]
GO
/****** Object:  Database [ERP]    Script Date: 01/06/2015 07:42:50 ******/
CREATE DATABASE [ERP] ON  PRIMARY 
( NAME = N'ERP', FILENAME = N'D:\SQLSERVERDB\ERP.mdf' , SIZE = 4096KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'ERP_log', FILENAME = N'D:\SQLSERVERDB\ERP_log.ldf' , SIZE = 1024KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [ERP] SET COMPATIBILITY_LEVEL = 100
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [ERP].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [ERP] SET ANSI_NULL_DEFAULT OFF
GO
ALTER DATABASE [ERP] SET ANSI_NULLS OFF
GO
ALTER DATABASE [ERP] SET ANSI_PADDING OFF
GO
ALTER DATABASE [ERP] SET ANSI_WARNINGS OFF
GO
ALTER DATABASE [ERP] SET ARITHABORT OFF
GO
ALTER DATABASE [ERP] SET AUTO_CLOSE OFF
GO
ALTER DATABASE [ERP] SET AUTO_CREATE_STATISTICS ON
GO
ALTER DATABASE [ERP] SET AUTO_SHRINK OFF
GO
ALTER DATABASE [ERP] SET AUTO_UPDATE_STATISTICS ON
GO
ALTER DATABASE [ERP] SET CURSOR_CLOSE_ON_COMMIT OFF
GO
ALTER DATABASE [ERP] SET CURSOR_DEFAULT  GLOBAL
GO
ALTER DATABASE [ERP] SET CONCAT_NULL_YIELDS_NULL OFF
GO
ALTER DATABASE [ERP] SET NUMERIC_ROUNDABORT OFF
GO
ALTER DATABASE [ERP] SET QUOTED_IDENTIFIER OFF
GO
ALTER DATABASE [ERP] SET RECURSIVE_TRIGGERS OFF
GO
ALTER DATABASE [ERP] SET  DISABLE_BROKER
GO
ALTER DATABASE [ERP] SET AUTO_UPDATE_STATISTICS_ASYNC OFF
GO
ALTER DATABASE [ERP] SET DATE_CORRELATION_OPTIMIZATION OFF
GO
ALTER DATABASE [ERP] SET TRUSTWORTHY OFF
GO
ALTER DATABASE [ERP] SET ALLOW_SNAPSHOT_ISOLATION OFF
GO
ALTER DATABASE [ERP] SET PARAMETERIZATION SIMPLE
GO
ALTER DATABASE [ERP] SET READ_COMMITTED_SNAPSHOT OFF
GO
ALTER DATABASE [ERP] SET HONOR_BROKER_PRIORITY OFF
GO
ALTER DATABASE [ERP] SET  READ_WRITE
GO
ALTER DATABASE [ERP] SET RECOVERY SIMPLE
GO
ALTER DATABASE [ERP] SET  MULTI_USER
GO
ALTER DATABASE [ERP] SET PAGE_VERIFY CHECKSUM
GO
ALTER DATABASE [ERP] SET DB_CHAINING OFF
GO
USE [ERP]
GO
/****** Object:  Table [dbo].[m_inventory_pickings]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_pickings](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[picking_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_pickings_idx01] ON [dbo].[m_inventory_pickings] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_picklists]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_picklists](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[picklist_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_picklists_idx01] ON [dbo].[m_inventory_picklists] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_inbounds]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_inbounds](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[inbound_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_inbounds_idx01] ON [dbo].[m_inventory_inbounds] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_moves]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_moves](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[move_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_moves_idx01] ON [dbo].[m_inventory_moves] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[c_businesspartners]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[c_businesspartners](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](10) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[address] [nvarchar](500) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [c_businesspartners_idx01] ON [dbo].[c_businesspartners] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_adjusts]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_adjusts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[adjust_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_adjusts_idx01] ON [dbo].[m_inventory_adjusts] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cus_m_inventory_products]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[cus_m_inventory_products](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[sku] [varchar](150) NOT NULL,
	[description] [varchar](255) NULL,
	[barcode_length] [int] NOT NULL,
	[qty_start] [int] NOT NULL,
	[qty_end] [int] NOT NULL,
	[sku_start] [int] NOT NULL,
	[sku_end] [int] NOT NULL,
	[carton_start] [int] NOT NULL,
	[carton_end] [int] NOT NULL,
	[date_packed_start] [int] NOT NULL,
	[date_packed_end] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [cus_m_inventory_products_idx01] ON [dbo].[cus_m_inventory_products] 
(
	[sku] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_putaways]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_putaways](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[putaway_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_putaways_idx01] ON [dbo].[m_inventory_putaways] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_warehouses]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_warehouses](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](5) NOT NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_warehouses_idx01] ON [dbo].[m_warehouses] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_users]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_users](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[username] [varchar](20) NOT NULL,
	[password] [varchar](150) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[email] [varchar](255) NOT NULL,
	[is_active] [bit] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [sys_users_idx01] ON [dbo].[sys_users] 
(
	[username] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE UNIQUE NONCLUSTERED INDEX [sys_users_idx02] ON [dbo].[sys_users] 
(
	[email] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_usergroups]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_usergroups](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [sys_usergroups_idx01] ON [dbo].[sys_usergroups] 
(
	[name] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_productgroups]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_productgroups](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](15) NOT NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_productgroups_idx01] ON [dbo].[m_productgroups] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventorylogs]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventorylogs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[m_product_id] [int] NOT NULL,
	[m_grid_id] [int] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
	[notes] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE NONCLUSTERED INDEX [m_inventorylogs_idx01] ON [dbo].[m_inventorylogs] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventorylogs_idx02] ON [dbo].[m_inventorylogs] 
(
	[m_product_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventorylogs_idx03] ON [dbo].[m_inventorylogs] 
(
	[m_grid_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_shipments]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_shipments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](50) NOT NULL,
	[shipment_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_shipments_idx01] ON [dbo].[m_inventory_shipments] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_receives]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_inventory_receives](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[receive_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_inventory_receives_idx01] ON [dbo].[m_inventory_receives] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_controls]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_controls](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [sys_controls_idx01] ON [dbo].[sys_controls] 
(
	[name] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_ci_sessions]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_ci_sessions](
	[session_id] [varchar](40) NOT NULL,
	[ip_address] [varchar](45) NOT NULL,
	[user_agent] [varchar](255) NOT NULL,
	[last_activity] [int] NOT NULL,
	[user_data] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[session_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[sys_actions]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_actions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [sys_actions_idx01] ON [dbo].[sys_actions] 
(
	[name] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_accesscontrols]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sys_accesscontrols](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[sys_usergroup_id] [int] NOT NULL,
	[sys_control_id] [int] NOT NULL,
	[sys_action_id] [int] NOT NULL,
	[is_denied] [bit] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_accesscontrols_idx01] ON [dbo].[sys_accesscontrols] 
(
	[sys_usergroup_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_accesscontrols_idx02] ON [dbo].[sys_accesscontrols] 
(
	[sys_control_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_accesscontrols_idx03] ON [dbo].[sys_accesscontrols] 
(
	[sys_action_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_usergroup_users]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sys_usergroup_users](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[sys_usergroup_id] [int] NOT NULL,
	[sys_user_id] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_usergroup_users_idx01] ON [dbo].[sys_usergroup_users] 
(
	[sys_usergroup_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_usergroup_users_idx02] ON [dbo].[sys_usergroup_users] 
(
	[sys_user_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sys_menus]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sys_menus](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[parent_id] [int] NULL,
	[sys_control_id] [int] NULL,
	[sys_action_id] [int] NULL,
	[name] [varchar](255) NOT NULL,
	[sequence] [int] NOT NULL,
	[url] [varchar](255) NULL,
	[css] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE NONCLUSTERED INDEX [sys_menus_idx01] ON [dbo].[sys_menus] 
(
	[parent_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_menus_idx02] ON [dbo].[sys_menus] 
(
	[sys_control_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [sys_menus_idx03] ON [dbo].[sys_menus] 
(
	[sys_action_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_products]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_products](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](50) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[m_productgroup_id] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_products_idx01] ON [dbo].[m_products] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_products_idx02] ON [dbo].[m_products] 
(
	[m_productgroup_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[c_orderins]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[c_orderins](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[orderin_date] [date] NOT NULL,
	[c_businesspartner_id] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [c_orderins_idx01] ON [dbo].[c_orderins] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderins_idx02] ON [dbo].[c_orderins] 
(
	[c_businesspartner_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cus_m_inventory_cyclecounts]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[cus_m_inventory_cyclecounts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[cus_m_inventory_product_id] [int] NOT NULL,
	[barcode] [varchar](255) NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
	[pallet] [varchar](255) NULL,
	[carton_no] [varchar](20) NULL,
	[status] [smallint] NOT NULL,
	[date_packed] [date] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE NONCLUSTERED INDEX [cus_m_inventory_cyclecou_idx01] ON [dbo].[cus_m_inventory_cyclecounts] 
(
	[cus_m_inventory_product_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE UNIQUE NONCLUSTERED INDEX [cus_m_inventory_cyclecou_idx02] ON [dbo].[cus_m_inventory_cyclecounts] 
(
	[barcode] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[c_orderouts]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[c_orderouts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[code] [varchar](30) NOT NULL,
	[orderout_date] [date] NOT NULL,
	[c_businesspartner_id] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE UNIQUE NONCLUSTERED INDEX [c_orderouts_idx01] ON [dbo].[c_orderouts] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderouts_idx02] ON [dbo].[c_orderouts] 
(
	[c_businesspartner_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_grids]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[m_grids](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_warehouse_id] [int] NOT NULL,
	[m_productgroup_id] [int] NULL,
	[code] [varchar](12) NOT NULL,
	[row] [int] NOT NULL,
	[col] [int] NOT NULL,
	[level] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
CREATE NONCLUSTERED INDEX [m_grids_idx01] ON [dbo].[m_grids] 
(
	[m_warehouse_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_grids_idx02] ON [dbo].[m_grids] 
(
	[m_productgroup_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE UNIQUE NONCLUSTERED INDEX [m_grids_idx03] ON [dbo].[m_grids] 
(
	[code] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventories]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_product_id] [int] NOT NULL,
	[m_grid_id] [int] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventories_idx01] ON [dbo].[m_inventories] 
(
	[m_product_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventories_idx02] ON [dbo].[m_inventories] 
(
	[m_grid_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[c_orderoutdetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[c_orderoutdetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[c_orderout_id] [bigint] NOT NULL,
	[m_product_id] [int] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderoutdetails_idx01] ON [dbo].[c_orderoutdetails] 
(
	[c_orderout_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderoutdetails_idx02] ON [dbo].[c_orderoutdetails] 
(
	[m_product_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[c_orderindetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[c_orderindetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[c_orderin_id] [bigint] NOT NULL,
	[m_product_id] [int] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderindetails_idx01] ON [dbo].[c_orderindetails] 
(
	[c_orderin_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [c_orderindetails_idx02] ON [dbo].[c_orderindetails] 
(
	[m_product_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_putawaydetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_putawaydetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_putaway_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[m_gridfrom_id] [int] NOT NULL,
	[m_gridto_id] [int] NOT NULL,
	[quantity_from] [decimal](12, 4) NOT NULL,
	[quantity_to] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_putawaydetai_idx01] ON [dbo].[m_inventory_putawaydetails] 
(
	[m_inventory_putaway_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_putawaydetai_idx02] ON [dbo].[m_inventory_putawaydetails] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_putawaydetai_idx03] ON [dbo].[m_inventory_putawaydetails] 
(
	[m_gridfrom_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_putawaydetai_idx04] ON [dbo].[m_inventory_putawaydetails] 
(
	[m_gridto_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_receivedetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_receivedetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_receive_id] [bigint] NOT NULL,
	[c_orderindetail_id] [bigint] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_receivedetai_idx01] ON [dbo].[m_inventory_receivedetails] 
(
	[m_inventory_receive_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_receivedetai_idx02] ON [dbo].[m_inventory_receivedetails] 
(
	[c_orderindetail_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_adjustdetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_adjustdetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_adjust_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[quantity_from] [decimal](12, 4) NOT NULL,
	[quantity_to] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_adjustdetail_idx01] ON [dbo].[m_inventory_adjustdetails] 
(
	[m_inventory_adjust_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_adjustdetail_idx02] ON [dbo].[m_inventory_adjustdetails] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_movedetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_movedetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_move_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[m_gridfrom_id] [int] NOT NULL,
	[m_gridto_id] [int] NOT NULL,
	[quantity_from] [decimal](12, 4) NOT NULL,
	[quantity_to] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_movedetails_idx01] ON [dbo].[m_inventory_movedetails] 
(
	[m_inventory_move_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_movedetails_idx02] ON [dbo].[m_inventory_movedetails] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_movedetails_idx03] ON [dbo].[m_inventory_movedetails] 
(
	[m_gridfrom_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_movedetails_idx04] ON [dbo].[m_inventory_movedetails] 
(
	[m_gridto_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_picklistdetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_picklistdetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_picklist_id] [bigint] NOT NULL,
	[c_orderoutdetail_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[m_grid_id] [int] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_picklistdeta_idx01] ON [dbo].[m_inventory_picklistdetails] 
(
	[m_inventory_picklist_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_picklistdeta_idx02] ON [dbo].[m_inventory_picklistdetails] 
(
	[c_orderoutdetail_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_picklistdeta_idx03] ON [dbo].[m_inventory_picklistdetails] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_picklistdeta_idx04] ON [dbo].[m_inventory_picklistdetails] 
(
	[m_grid_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_pickingdetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_pickingdetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_picking_id] [bigint] NOT NULL,
	[m_inventory_picklistdetail_id] [bigint] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_pickingdetai_idx01] ON [dbo].[m_inventory_pickingdetails] 
(
	[m_inventory_picking_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_pickingdetai_idx02] ON [dbo].[m_inventory_pickingdetails] 
(
	[m_inventory_picklistdetail_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_inbounddetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_inbounddetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_inbound_id] [bigint] NOT NULL,
	[m_inventory_receivedetail_id] [bigint] NOT NULL,
	[m_inventory_id] [bigint] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_inbounddetai_idx01] ON [dbo].[m_inventory_inbounddetails] 
(
	[m_inventory_inbound_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_inbounddetai_idx02] ON [dbo].[m_inventory_inbounddetails] 
(
	[m_inventory_receivedetail_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_inbounddetai_idx03] ON [dbo].[m_inventory_inbounddetails] 
(
	[m_inventory_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_inventory_shipmentdetails]    Script Date: 01/06/2015 07:42:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_inventory_shipmentdetails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[created] [smalldatetime] NULL,
	[created_by] [int] NULL,
	[updated] [smalldatetime] NULL,
	[updated_by] [int] NULL,
	[m_inventory_shipment_id] [bigint] NOT NULL,
	[m_inventory_pickingdetail_id] [bigint] NOT NULL,
	[quantity] [decimal](12, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_shipmentdeta_idx01] ON [dbo].[m_inventory_shipmentdetails] 
(
	[m_inventory_shipment_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [m_inventory_shipmentdeta_idx02] ON [dbo].[m_inventory_shipmentdetails] 
(
	[m_inventory_pickingdetail_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
/****** Object:  Default [DF__cus_m_inv__barco__0C50D423]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [barcode_length]
GO
/****** Object:  Default [DF__cus_m_inv__qty_s__0D44F85C]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [qty_start]
GO
/****** Object:  Default [DF__cus_m_inv__qty_e__0E391C95]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [qty_end]
GO
/****** Object:  Default [DF__cus_m_inv__sku_s__0F2D40CE]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [sku_start]
GO
/****** Object:  Default [DF__cus_m_inv__sku_e__10216507]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [sku_end]
GO
/****** Object:  Default [DF__cus_m_inv__carto__11158940]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [carton_start]
GO
/****** Object:  Default [DF__cus_m_inv__carto__1209AD79]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [carton_end]
GO
/****** Object:  Default [DF__cus_m_inv__date___12FDD1B2]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [date_packed_start]
GO
/****** Object:  Default [DF__cus_m_inv__date___13F1F5EB]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_products] ADD  DEFAULT ((0)) FOR [date_packed_end]
GO
/****** Object:  Default [DF__sys_users__is_ac__41EDCAC5]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_users] ADD  DEFAULT ((0)) FOR [is_active]
GO
/****** Object:  Default [DF__m_invento__quant__01D345B0]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventorylogs] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__sys_acces__is_de__29221CFB]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_accesscontrols] ADD  DEFAULT ((0)) FOR [is_denied]
GO
/****** Object:  Default [DF__sys_menus__seque__3587F3E0]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_menus] ADD  DEFAULT ((0)) FOR [sequence]
GO
/****** Object:  Default [DF__cus_m_inv__quant__0697FACD]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__cus_m_inv__statu__078C1F06]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] ADD  DEFAULT ((0)) FOR [status]
GO
/****** Object:  Default [DF__m_grids__row__05D8E0BE]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_grids] ADD  DEFAULT ((0)) FOR [row]
GO
/****** Object:  Default [DF__m_grids__col__06CD04F7]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_grids] ADD  DEFAULT ((0)) FOR [col]
GO
/****** Object:  Default [DF__m_grids__level__07C12930]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_grids] ADD  DEFAULT ((0)) FOR [level]
GO
/****** Object:  Default [DF__m_invento__quant__46B27FE2]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventories] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__c_orderou__quant__797309D9]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderoutdetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__m_putaway__quant__4F47C5E3]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD  DEFAULT ((0)) FOR [quantity_from]
GO
/****** Object:  Default [DF__m_putaway__quant__503BEA1C]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails] ADD  DEFAULT ((0)) FOR [quantity_to]
GO
/****** Object:  Default [DF__m_receive__quant__1CBC4616]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_receivedetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__m_adjustd__quant__625A9A57]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD  DEFAULT ((0)) FOR [quantity_from]
GO
/****** Object:  Default [DF__m_adjustd__quant__634EBE90]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_adjustdetails] ADD  DEFAULT ((0)) FOR [quantity_to]
GO
/****** Object:  Default [DF__m_movedet__quant__58D1301D]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails] ADD  DEFAULT ((0)) FOR [quantity_from]
GO
/****** Object:  Default [DF__m_movedet__quant__59C55456]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails] ADD  DEFAULT ((0)) FOR [quantity_to]
GO
/****** Object:  Default [DF__m_invento__quant__6BE40491]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_picklistdetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__m_invento__quant__74794A92]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_pickingdetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__m_inbound__quant__10566F31]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_inbounddetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  Default [DF__m_invento__quant__7D0E9093]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_shipmentdetails] ADD  DEFAULT ((0)) FOR [quantity]
GO
/****** Object:  ForeignKey [sys_accesscontrols_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_accesscontrols]  WITH CHECK ADD  CONSTRAINT [sys_accesscontrols_fk01] FOREIGN KEY([sys_usergroup_id])
REFERENCES [dbo].[sys_usergroups] ([id])
GO
ALTER TABLE [dbo].[sys_accesscontrols] CHECK CONSTRAINT [sys_accesscontrols_fk01]
GO
/****** Object:  ForeignKey [sys_accesscontrols_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_accesscontrols]  WITH CHECK ADD  CONSTRAINT [sys_accesscontrols_fk02] FOREIGN KEY([sys_control_id])
REFERENCES [dbo].[sys_controls] ([id])
GO
ALTER TABLE [dbo].[sys_accesscontrols] CHECK CONSTRAINT [sys_accesscontrols_fk02]
GO
/****** Object:  ForeignKey [sys_accesscontrols_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_accesscontrols]  WITH CHECK ADD  CONSTRAINT [sys_accesscontrols_fk03] FOREIGN KEY([sys_action_id])
REFERENCES [dbo].[sys_actions] ([id])
GO
ALTER TABLE [dbo].[sys_accesscontrols] CHECK CONSTRAINT [sys_accesscontrols_fk03]
GO
/****** Object:  ForeignKey [sys_usergroup_users_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_usergroup_users]  WITH CHECK ADD  CONSTRAINT [sys_usergroup_users_fk01] FOREIGN KEY([sys_usergroup_id])
REFERENCES [dbo].[sys_usergroups] ([id])
GO
ALTER TABLE [dbo].[sys_usergroup_users] CHECK CONSTRAINT [sys_usergroup_users_fk01]
GO
/****** Object:  ForeignKey [sys_usergroup_users_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_usergroup_users]  WITH CHECK ADD  CONSTRAINT [sys_usergroup_users_fk02] FOREIGN KEY([sys_user_id])
REFERENCES [dbo].[sys_users] ([id])
GO
ALTER TABLE [dbo].[sys_usergroup_users] CHECK CONSTRAINT [sys_usergroup_users_fk02]
GO
/****** Object:  ForeignKey [sys_menus_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_menus]  WITH CHECK ADD  CONSTRAINT [sys_menus_fk01] FOREIGN KEY([parent_id])
REFERENCES [dbo].[sys_menus] ([id])
GO
ALTER TABLE [dbo].[sys_menus] CHECK CONSTRAINT [sys_menus_fk01]
GO
/****** Object:  ForeignKey [sys_menus_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_menus]  WITH CHECK ADD  CONSTRAINT [sys_menus_fk02] FOREIGN KEY([sys_control_id])
REFERENCES [dbo].[sys_controls] ([id])
GO
ALTER TABLE [dbo].[sys_menus] CHECK CONSTRAINT [sys_menus_fk02]
GO
/****** Object:  ForeignKey [sys_menus_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[sys_menus]  WITH CHECK ADD  CONSTRAINT [sys_menus_fk03] FOREIGN KEY([sys_action_id])
REFERENCES [dbo].[sys_actions] ([id])
GO
ALTER TABLE [dbo].[sys_menus] CHECK CONSTRAINT [sys_menus_fk03]
GO
/****** Object:  ForeignKey [m_products_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_products]  WITH CHECK ADD  CONSTRAINT [m_products_fk01] FOREIGN KEY([m_productgroup_id])
REFERENCES [dbo].[m_productgroups] ([id])
GO
ALTER TABLE [dbo].[m_products] CHECK CONSTRAINT [m_products_fk01]
GO
/****** Object:  ForeignKey [c_orderins_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderins]  WITH CHECK ADD  CONSTRAINT [c_orderins_fk01] FOREIGN KEY([c_businesspartner_id])
REFERENCES [dbo].[c_businesspartners] ([id])
GO
ALTER TABLE [dbo].[c_orderins] CHECK CONSTRAINT [c_orderins_fk01]
GO
/****** Object:  ForeignKey [cus_m_inventory_cyclecounts_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts]  WITH CHECK ADD  CONSTRAINT [cus_m_inventory_cyclecounts_fk01] FOREIGN KEY([cus_m_inventory_product_id])
REFERENCES [dbo].[cus_m_inventory_products] ([id])
GO
ALTER TABLE [dbo].[cus_m_inventory_cyclecounts] CHECK CONSTRAINT [cus_m_inventory_cyclecounts_fk01]
GO
/****** Object:  ForeignKey [c_orderouts_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderouts]  WITH CHECK ADD  CONSTRAINT [c_orderouts_fk01] FOREIGN KEY([c_businesspartner_id])
REFERENCES [dbo].[c_businesspartners] ([id])
GO
ALTER TABLE [dbo].[c_orderouts] CHECK CONSTRAINT [c_orderouts_fk01]
GO
/****** Object:  ForeignKey [m_grids_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_grids]  WITH CHECK ADD  CONSTRAINT [m_grids_fk01] FOREIGN KEY([m_warehouse_id])
REFERENCES [dbo].[m_warehouses] ([id])
GO
ALTER TABLE [dbo].[m_grids] CHECK CONSTRAINT [m_grids_fk01]
GO
/****** Object:  ForeignKey [m_grids_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_grids]  WITH CHECK ADD  CONSTRAINT [m_grids_fk02] FOREIGN KEY([m_productgroup_id])
REFERENCES [dbo].[m_productgroups] ([id])
GO
ALTER TABLE [dbo].[m_grids] CHECK CONSTRAINT [m_grids_fk02]
GO
/****** Object:  ForeignKey [m_inventories_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventories]  WITH CHECK ADD  CONSTRAINT [m_inventories_fk01] FOREIGN KEY([m_product_id])
REFERENCES [dbo].[m_products] ([id])
GO
ALTER TABLE [dbo].[m_inventories] CHECK CONSTRAINT [m_inventories_fk01]
GO
/****** Object:  ForeignKey [m_inventories_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventories]  WITH CHECK ADD  CONSTRAINT [m_inventories_fk02] FOREIGN KEY([m_grid_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventories] CHECK CONSTRAINT [m_inventories_fk02]
GO
/****** Object:  ForeignKey [c_orderoutdetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderoutdetails]  WITH CHECK ADD  CONSTRAINT [c_orderoutdetails_fk01] FOREIGN KEY([c_orderout_id])
REFERENCES [dbo].[c_orderouts] ([id])
GO
ALTER TABLE [dbo].[c_orderoutdetails] CHECK CONSTRAINT [c_orderoutdetails_fk01]
GO
/****** Object:  ForeignKey [c_orderoutdetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderoutdetails]  WITH CHECK ADD  CONSTRAINT [c_orderoutdetails_fk02] FOREIGN KEY([m_product_id])
REFERENCES [dbo].[m_products] ([id])
GO
ALTER TABLE [dbo].[c_orderoutdetails] CHECK CONSTRAINT [c_orderoutdetails_fk02]
GO
/****** Object:  ForeignKey [c_orderindetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderindetails]  WITH CHECK ADD  CONSTRAINT [c_orderindetails_fk01] FOREIGN KEY([c_orderin_id])
REFERENCES [dbo].[c_orderins] ([id])
GO
ALTER TABLE [dbo].[c_orderindetails] CHECK CONSTRAINT [c_orderindetails_fk01]
GO
/****** Object:  ForeignKey [c_orderindetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[c_orderindetails]  WITH CHECK ADD  CONSTRAINT [c_orderindetails_fk02] FOREIGN KEY([m_product_id])
REFERENCES [dbo].[m_products] ([id])
GO
ALTER TABLE [dbo].[c_orderindetails] CHECK CONSTRAINT [c_orderindetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_putawaydetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_putawaydetails_fk01] FOREIGN KEY([m_inventory_putaway_id])
REFERENCES [dbo].[m_inventory_putaways] ([id])
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] CHECK CONSTRAINT [m_inventory_putawaydetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_putawaydetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_putawaydetails_fk02] FOREIGN KEY([m_inventory_id])
REFERENCES [dbo].[m_inventories] ([id])
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] CHECK CONSTRAINT [m_inventory_putawaydetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_putawaydetails_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_putawaydetails_fk03] FOREIGN KEY([m_gridfrom_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] CHECK CONSTRAINT [m_inventory_putawaydetails_fk03]
GO
/****** Object:  ForeignKey [m_inventory_putawaydetails_fk04]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_putawaydetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_putawaydetails_fk04] FOREIGN KEY([m_gridto_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventory_putawaydetails] CHECK CONSTRAINT [m_inventory_putawaydetails_fk04]
GO
/****** Object:  ForeignKey [m_inventory_receivedetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_receivedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_receivedetails_fk01] FOREIGN KEY([m_inventory_receive_id])
REFERENCES [dbo].[m_inventory_receives] ([id])
GO
ALTER TABLE [dbo].[m_inventory_receivedetails] CHECK CONSTRAINT [m_inventory_receivedetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_receivedetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_receivedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_receivedetails_fk02] FOREIGN KEY([c_orderindetail_id])
REFERENCES [dbo].[c_orderindetails] ([id])
GO
ALTER TABLE [dbo].[m_inventory_receivedetails] CHECK CONSTRAINT [m_inventory_receivedetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_adjustdetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_adjustdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_adjustdetails_fk01] FOREIGN KEY([m_inventory_adjust_id])
REFERENCES [dbo].[m_inventory_adjusts] ([id])
GO
ALTER TABLE [dbo].[m_inventory_adjustdetails] CHECK CONSTRAINT [m_inventory_adjustdetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_adjustdetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_adjustdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_adjustdetails_fk02] FOREIGN KEY([m_inventory_id])
REFERENCES [dbo].[m_inventories] ([id])
GO
ALTER TABLE [dbo].[m_inventory_adjustdetails] CHECK CONSTRAINT [m_inventory_adjustdetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_movedetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_movedetails_fk01] FOREIGN KEY([m_inventory_move_id])
REFERENCES [dbo].[m_inventory_moves] ([id])
GO
ALTER TABLE [dbo].[m_inventory_movedetails] CHECK CONSTRAINT [m_inventory_movedetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_movedetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_movedetails_fk02] FOREIGN KEY([m_inventory_id])
REFERENCES [dbo].[m_inventories] ([id])
GO
ALTER TABLE [dbo].[m_inventory_movedetails] CHECK CONSTRAINT [m_inventory_movedetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_movedetails_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_movedetails_fk03] FOREIGN KEY([m_gridfrom_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventory_movedetails] CHECK CONSTRAINT [m_inventory_movedetails_fk03]
GO
/****** Object:  ForeignKey [m_inventory_movedetails_fk04]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_movedetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_movedetails_fk04] FOREIGN KEY([m_gridto_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventory_movedetails] CHECK CONSTRAINT [m_inventory_movedetails_fk04]
GO
/****** Object:  ForeignKey [m_inventory_picklistdetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_picklistdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_picklistdetails_fk01] FOREIGN KEY([m_inventory_picklist_id])
REFERENCES [dbo].[m_inventory_picklists] ([id])
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] CHECK CONSTRAINT [m_inventory_picklistdetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_picklistdetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_picklistdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_picklistdetails_fk02] FOREIGN KEY([c_orderoutdetail_id])
REFERENCES [dbo].[c_orderoutdetails] ([id])
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] CHECK CONSTRAINT [m_inventory_picklistdetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_picklistdetails_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_picklistdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_picklistdetails_fk03] FOREIGN KEY([m_inventory_id])
REFERENCES [dbo].[m_inventories] ([id])
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] CHECK CONSTRAINT [m_inventory_picklistdetails_fk03]
GO
/****** Object:  ForeignKey [m_inventory_picklistdetails_fk04]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_picklistdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_picklistdetails_fk04] FOREIGN KEY([m_grid_id])
REFERENCES [dbo].[m_grids] ([id])
GO
ALTER TABLE [dbo].[m_inventory_picklistdetails] CHECK CONSTRAINT [m_inventory_picklistdetails_fk04]
GO
/****** Object:  ForeignKey [m_inventory_pickingdetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_pickingdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_pickingdetails_fk01] FOREIGN KEY([m_inventory_picking_id])
REFERENCES [dbo].[m_inventory_pickings] ([id])
GO
ALTER TABLE [dbo].[m_inventory_pickingdetails] CHECK CONSTRAINT [m_inventory_pickingdetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_pickingdetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_pickingdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_pickingdetails_fk02] FOREIGN KEY([m_inventory_picklistdetail_id])
REFERENCES [dbo].[m_inventory_picklistdetails] ([id])
GO
ALTER TABLE [dbo].[m_inventory_pickingdetails] CHECK CONSTRAINT [m_inventory_pickingdetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_inbounddetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_inbounddetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_inbounddetails_fk01] FOREIGN KEY([m_inventory_inbound_id])
REFERENCES [dbo].[m_inventory_inbounds] ([id])
GO
ALTER TABLE [dbo].[m_inventory_inbounddetails] CHECK CONSTRAINT [m_inventory_inbounddetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_inbounddetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_inbounddetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_inbounddetails_fk02] FOREIGN KEY([m_inventory_receivedetail_id])
REFERENCES [dbo].[m_inventory_receivedetails] ([id])
GO
ALTER TABLE [dbo].[m_inventory_inbounddetails] CHECK CONSTRAINT [m_inventory_inbounddetails_fk02]
GO
/****** Object:  ForeignKey [m_inventory_inbounddetails_fk03]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_inbounddetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_inbounddetails_fk03] FOREIGN KEY([m_inventory_id])
REFERENCES [dbo].[m_inventories] ([id])
GO
ALTER TABLE [dbo].[m_inventory_inbounddetails] CHECK CONSTRAINT [m_inventory_inbounddetails_fk03]
GO
/****** Object:  ForeignKey [m_inventory_shipmentdetails_fk01]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_shipmentdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_shipmentdetails_fk01] FOREIGN KEY([m_inventory_shipment_id])
REFERENCES [dbo].[m_inventory_shipments] ([id])
GO
ALTER TABLE [dbo].[m_inventory_shipmentdetails] CHECK CONSTRAINT [m_inventory_shipmentdetails_fk01]
GO
/****** Object:  ForeignKey [m_inventory_shipmentdetails_fk02]    Script Date: 01/06/2015 07:42:52 ******/
ALTER TABLE [dbo].[m_inventory_shipmentdetails]  WITH CHECK ADD  CONSTRAINT [m_inventory_shipmentdetails_fk02] FOREIGN KEY([m_inventory_pickingdetail_id])
REFERENCES [dbo].[m_inventory_pickingdetails] ([id])
GO
ALTER TABLE [dbo].[m_inventory_shipmentdetails] CHECK CONSTRAINT [m_inventory_shipmentdetails_fk02]
GO
