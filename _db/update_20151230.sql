CREATE TABLE m_inventory_assemblies (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	assembly_date date NOT NULL ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX m_inventory_assembly_idx01 ON m_inventory_assemblies
(code ASC) 
GO


CREATE TABLE m_inventory_assemblysources (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_assembly_id bigint NOT NULL ,
	m_inventory_id bigint NOT NULL ,
	c_project_id bigint NULL ,
	quantity_from decimal(12,4) NOT NULL DEFAULT 0 ,
	quantity_to decimal(12,4) NOT NULL DEFAULT 0 ,
	quantity_box_from int NOT NULL DEFAULT 0 ,
	quantity_box_to int NOT NULL DEFAULT 0 ,
	m_product_id int NULL ,
	m_grid_id int NULL ,
	pallet varchar(255) NULL ,
	barcode varchar(255) NULL ,
	carton_no varchar(25) NULL ,
	lot_no varchar(50) NULL ,
	PRIMARY KEY (id),
	CONSTRAINT m_inventory_assemblysour_fk01 FOREIGN KEY (m_inventory_assembly_id) REFERENCES m_inventory_assemblies (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblysour_fk02 FOREIGN KEY (m_inventory_id) REFERENCES m_inventories (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblysour_fk03 FOREIGN KEY (m_product_id) REFERENCES m_products (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblysour_fk04 FOREIGN KEY (m_grid_id) REFERENCES m_grids (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblysour_fk05 FOREIGN KEY (c_project_id) REFERENCES c_projects (id) ON DELETE NO ACTION ON UPDATE NO ACTION
)
GO

CREATE INDEX m_inventory_assemblysour_idx01 ON m_inventory_assemblysources
(m_inventory_assembly_id ASC) 
GO

CREATE INDEX m_inventory_assemblysour_idx02 ON m_inventory_assemblysources
(m_inventory_id ASC) 
GO

CREATE INDEX m_inventory_assemblysour_idx03 ON m_inventory_assemblysources
(m_product_id ASC) 
GO

CREATE INDEX m_inventory_assemblysour_idx04 ON m_inventory_assemblysources
(m_grid_id ASC) 
GO

CREATE INDEX m_inventory_assemblysour_idx05 ON m_inventory_assemblysources
(c_project_id ASC) 
GO

CREATE TABLE m_inventory_assemblytargets (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_assembly_id bigint NOT NULL ,
	m_inventory_id bigint NOT NULL ,
	c_project_id bigint NULL ,
	quantity decimal(12,4) NOT NULL DEFAULT 0 ,
	quantity_box int NOT NULL DEFAULT 0 ,
	m_product_id int NULL ,
	m_grid_id int NULL ,
	pallet varchar(255) NULL ,
	barcode varchar(255) NULL ,
	carton_no varchar(25) NULL ,
	lot_no varchar(50) NULL ,
	packed_date date NULL ,
	expired_date date NULL ,
	condition varchar(100) NULL ,
	PRIMARY KEY (id),
	CONSTRAINT m_inventory_assemblytarg_fk01 FOREIGN KEY (m_inventory_assembly_id) REFERENCES m_inventory_assemblies (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblytarg_fk02 FOREIGN KEY (m_inventory_id) REFERENCES m_inventories (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblytarg_fk03 FOREIGN KEY (m_product_id) REFERENCES m_products (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblytarg_fk04 FOREIGN KEY (m_grid_id) REFERENCES m_grids (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT m_inventory_assemblytarg_fk05 FOREIGN KEY (c_project_id) REFERENCES c_projects (id) ON DELETE NO ACTION ON UPDATE NO ACTION
)
GO

CREATE INDEX m_inventory_assemblytarg_idx01 ON m_inventory_assemblytargets
(m_inventory_assembly_id ASC) 
GO

CREATE INDEX m_inventory_assemblytarg_idx02 ON m_inventory_assemblytargets
(m_inventory_id ASC) 
GO

CREATE INDEX m_inventory_assemblytarg_idx03 ON m_inventory_assemblytargets
(m_product_id ASC) 
GO

CREATE INDEX m_inventory_assemblytarg_idx04 ON m_inventory_assemblytargets
(m_grid_id ASC) 
GO

CREATE INDEX m_inventory_assemblytarg_idx05 ON m_inventory_assemblytargets
(c_project_id ASC) 
GO