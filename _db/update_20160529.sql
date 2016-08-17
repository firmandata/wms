ALTER TABLE m_inventory_picklistdetails
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX m_inventory_picklistdeta_idx07 ON dbo.m_inventory_picklistdetails
	(c_businesspartner_id ASC) 
GO

ALTER TABLE m_inventory_picklistdetails
	ADD CONSTRAINT m_inventory_picklistdetails_fk07 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_inventory_inbounddetails
	ADD c_project_id bigint NULL ,
		c_businesspartner_id int NULL ,
		m_product_id int NULL 
GO

CREATE INDEX m_inventory_inbounddetai_idx05 ON m_inventory_inbounddetails
	(c_project_id ASC) 
GO

CREATE INDEX m_inventory_inbounddetai_idx06 ON m_inventory_inbounddetails
	(c_businesspartner_id ASC) 
GO

CREATE INDEX m_inventory_inbounddetai_idx07 ON m_inventory_inbounddetails
	(m_product_id ASC) 
GO

ALTER TABLE m_inventory_inbounddetails
	ADD CONSTRAINT m_inventory_inbounddetails_fk05 FOREIGN KEY (c_project_id) REFERENCES c_projects (id)
GO

ALTER TABLE m_inventory_inbounddetails
	ADD CONSTRAINT m_inventory_inbounddetails_fk06 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_inventory_inbounddetails
	ADD CONSTRAINT m_inventory_inbounddetails_fk07 FOREIGN KEY (m_product_id) REFERENCES m_products (id)
GO

ALTER TABLE m_inventory_invoicedetails
	ALTER COLUMN amount decimal(14,4) NOT NULL
GO

UPDATE 	ipld SET
		ipld.c_businesspartner_id = oo.c_businesspartner_id
FROM 	m_inventory_picklistdetails ipld
		INNER JOIN c_orderoutdetails ood	ON ood.id = ipld.c_orderoutdetail_id
		INNER JOIN c_orderouts oo			ON oo.id = ood.c_orderout_id
GO

UPDATE 	iid SET
		iid.c_project_id = oi.c_project_id,
		iid.c_businesspartner_id = oi.c_businesspartner_id,
		iid.m_product_id = oid.m_product_id
FROM	m_inventory_inbounddetails iid
		INNER JOIN m_inventory_receivedetails ird	ON ird.id = iid.m_inventory_receivedetail_id
		INNER JOIN c_orderindetails oid				ON oid.id = ird.c_orderindetail_id
		INNER JOIN c_orderins oi					ON oi.id = oid.c_orderin_id
GO

UPDATE 	invl SET
		invl.volume_length = inv.volume_length,
		invl.volume_width = inv.volume_width,
		invl.volume_height = inv.volume_height
FROM	m_inventorylogs invl
		INNER JOIN m_inventories inv	ON inv.id = invl.m_inventory_id
GO

UPDATE 	iid SET
		iid.volume_length = inv.volume_length,
		iid.volume_width = inv.volume_width,
		iid.volume_height = inv.volume_height
FROM	m_inventory_inbounddetails iid
		INNER JOIN m_inventories inv	ON inv.id = iid.m_inventory_id
GO

UPDATE 	inv SET
		inv.c_businesspartner_id = iid.c_businesspartner_id
FROM	m_inventories inv
		INNER JOIN m_inventory_inbounddetails iid	ON iid.m_inventory_id = inv.id
WHERE	inv.c_businesspartner_id IS NULL
GO

UPDATE 	invl SET
		invl.c_businesspartner_id = inv.c_businesspartner_id
FROM	m_inventorylogs invl
		INNER JOIN m_inventories inv	ON inv.id = invl.m_inventory_id
WHERE	invl.c_businesspartner_id IS NULL
GO