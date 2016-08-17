ALTER TABLE [dbo].[m_inventories] ADD
	[c_project_id] bigint NULL ,
	[received_date] date NULL 
GO

CREATE INDEX [m_inventories_idx03] ON [dbo].[m_inventories]
	([c_project_id] ASC) 
GO

ALTER TABLE [dbo].[m_inventories] 
	ADD CONSTRAINT [m_inventories_fk03] FOREIGN KEY ([c_project_id]) REFERENCES [c_projects] ([id])
GO

ALTER TABLE [dbo].[m_inventorylogs] ADD
	[c_project_id] bigint NULL 
GO

CREATE INDEX [m_inventorylogs_idx04] ON [dbo].[m_inventorylogs]
	([c_project_id] ASC) 
GO

ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] ADD
	[c_project_id] bigint NULL ,
	[received_date] date NULL 
GO

CREATE INDEX [cus_m_inventory_inboundd_idx05] ON [dbo].[cus_m_inventory_inbounddetails]
	([c_project_id] ASC) 
GO

ALTER TABLE [dbo].[cus_m_inventory_inbounddetails] 
	ADD CONSTRAINT [cus_m_inventory_inbounddetails_fk04] FOREIGN KEY ([c_project_id]) REFERENCES [c_projects] ([id])
GO

UPDATE 	inv SET 
		inv.c_project_id = oi.c_project_id,
		inv.received_date = ISNULL(ir.receive_date, inv.created)
FROM	m_inventories inv
		LEFT JOIN m_inventory_inbounddetails iid	ON iid.m_inventory_id = inv.id
		LEFT JOIN m_inventory_receivedetails ird	ON ird.id = iid.m_inventory_receivedetail_id
		LEFT JOIN m_inventory_receives ir			ON ir.id = ird.m_inventory_receive_id
		LEFT JOIN c_orderindetails oid				ON oid.id = ird.c_orderindetail_id
		LEFT JOIN c_orderins oi						ON oi.id = oid.c_orderin_id
GO

UPDATE 	invl SET 
		invl.c_project_id = inv.c_project_id
FROM	m_inventorylogs invl
		INNER JOIN m_inventories inv	ON inv.id = invl.m_inventory_id
GO

ALTER TABLE [dbo].[m_products] ADD
	[brand] varchar(255) NULL 
GO

CREATE TABLE [dbo].[cus_c_project_sys_usergroups] (
	[id] bigint NOT NULL IDENTITY(1,1) ,
	[created] smalldatetime NULL ,
	[created_by] int NULL ,
	[updated] smalldatetime NULL ,
	[updated_by] int NULL ,
	[sys_usergroup_id] int NOT NULL ,
	[c_project_id] bigint NOT NULL ,
	PRIMARY KEY ([id]),
	CONSTRAINT [cus_c_project_sys_usergroups_fk01] FOREIGN KEY ([sys_usergroup_id]) REFERENCES [sys_usergroups] ([id]),
	CONSTRAINT [cus_c_project_sys_usergroups_fk02] FOREIGN KEY ([c_project_id]) REFERENCES [c_projects] ([id])
)
GO

CREATE INDEX [cus_c_project_sys_userg_idx01] ON [dbo].[cus_c_project_sys_usergroups]
	([sys_usergroup_id] ASC) 
GO

CREATE INDEX [cus_c_project_sys_userg_idx02] ON [dbo].[cus_c_project_sys_usergroups]
	([c_project_id] ASC) 
GO

CREATE UNIQUE INDEX [cus_c_project_sys_userg_idx03] ON [dbo].[cus_c_project_sys_usergroups]
	([sys_usergroup_id] ASC, [c_project_id] ASC) 
GO
