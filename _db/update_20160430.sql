ALTER TABLE c_orderindetails ALTER COLUMN discount DECIMAL(5,2) NOT NULL 
GO

ALTER TABLE m_inventory_picklists
	ADD m_grid_id INT NULL 
GO

CREATE INDEX m_inventory_picklists_idx02 ON m_inventory_picklists (m_grid_id ASC) 
GO

ALTER TABLE m_inventory_picklists ADD CONSTRAINT m_inventory_picklists_fk01 FOREIGN KEY (m_grid_id) REFERENCES m_grids (id)
GO

CREATE TABLE m_inventory_samples (
	id bigint IDENTITY(1,1) NOT NULL ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	sampling_date date NOT NULL ,
	supervisor varchar(150) NULL ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX m_inventory_samples_idx01 ON m_inventory_samples(code ASC) 
GO

CREATE TABLE m_inventory_sampledetails (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_sample_id bigint NOT NULL ,
	m_grid_id int NOT NULL ,
	doc int NOT NULL DEFAULT 0 ,
	adg decimal(12,4) NOT NULL DEFAULT 0 ,
	biomass decimal(12,4) NOT NULL DEFAULT 0 ,
	sr decimal(5,2) NOT NULL DEFAULT 0 ,
	fcr decimal(12,4) NOT NULL DEFAULT 0 ,
	abw decimal(12,4) NOT NULL DEFAULT 0 ,
	fd decimal(12,4) NOT NULL DEFAULT 0 ,
	population decimal(12,4) NOT NULL DEFAULT 0 ,
	fr decimal(5,2) NOT NULL DEFAULT 0 ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id) ,
	CONSTRAINT m_inventory_sampledetails_fk01 FOREIGN KEY (m_inventory_sample_id) REFERENCES m_inventory_samples(id) ,
	CONSTRAINT m_inventory_sampledetails_fk02 FOREIGN KEY (m_grid_id) REFERENCES m_grids(id)
)
GO

CREATE INDEX m_inventory_sampledetail_idx01 ON m_inventory_sampledetails(m_inventory_sample_id ASC) 
GO

CREATE INDEX m_inventory_sampledetail_idx02 ON m_inventory_sampledetails(m_grid_id ASC) 
GO

CREATE TABLE m_inventory_sampleinventories (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_sampledetail_id bigint NOT NULL ,
	m_inventory_id bigint NOT NULL ,
	m_product_id int NULL ,
	m_grid_id int NULL ,
	c_project_id bigint NULL ,
	quantity_box int NOT NULL DEFAULT 0 ,
	quantity decimal(12,4) NOT NULL DEFAULT 0 ,
	pallet varchar(255) NULL ,
	barcode varchar(255) NULL ,
	carton_no varchar(25) NULL ,
	lot_no varchar(50) NULL ,
	PRIMARY KEY (id) ,
	CONSTRAINT m_inventory_sampleinventories_fk01 FOREIGN KEY (m_inventory_sampledetail_id) REFERENCES m_inventory_sampledetails(id) ,
	CONSTRAINT m_inventory_sampleinventories_fk02 FOREIGN KEY (m_inventory_id) REFERENCES m_inventories(id) ,
	CONSTRAINT m_inventory_sampleinventories_fk03 FOREIGN KEY (m_product_id) REFERENCES m_products(id) ,
	CONSTRAINT m_inventory_sampleinventories_fk04 FOREIGN KEY (m_grid_id) REFERENCES m_grids(id) ,
	CONSTRAINT m_inventory_sampleinventories_fk05 FOREIGN KEY (c_project_id) REFERENCES c_projects(id)
)
GO

CREATE INDEX m_inventory_sampleinvent_idx01 ON m_inventory_sampleinventories(m_inventory_sampledetail_id ASC) 
GO

CREATE INDEX m_inventory_sampleinvent_idx02 ON m_inventory_sampleinventories(m_inventory_id ASC) 
GO

CREATE INDEX m_inventory_sampleinvent_idx03 ON m_inventory_sampleinventories(m_product_id ASC) 
GO

CREATE INDEX m_inventory_sampleinvent_idx04 ON m_inventory_sampleinventories(m_grid_id ASC) 
GO

CREATE INDEX m_inventory_sampleinvent_idx05 ON m_inventory_sampleinventories(c_project_id ASC) 
GO

CREATE TABLE m_inventory_waters (
	id bigint IDENTITY(1,1) NOT NULL ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	water_date date NOT NULL ,
	supervisor varchar(150) NULL ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX m_inventory_waters_idx01 ON m_inventory_waters(code ASC) 
GO

CREATE TABLE m_inventory_waterdetails (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_water_id bigint NOT NULL ,
	m_grid_id int NOT NULL ,
	doc int NOT NULL DEFAULT 0 ,
	suhu decimal(12,4) NOT NULL DEFAULT 0 ,
	disolved_oksigen decimal(12,4) NOT NULL DEFAULT 0 ,
	ph decimal(12,4) NOT NULL DEFAULT 0 ,
	salinitas decimal(12,4) NOT NULL DEFAULT 0 ,
	kecerahan decimal(12,4) NOT NULL DEFAULT 0 ,
	total_ammonia decimal(12,4) NOT NULL DEFAULT 0 ,
	total_nitrite decimal(12,4) NOT NULL DEFAULT 0 ,
	total_nitrate decimal(12,4) NOT NULL DEFAULT 0 ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id) ,
	CONSTRAINT m_inventory_waterdetails_fk01 FOREIGN KEY (m_inventory_water_id) REFERENCES m_inventory_waters(id) ,
	CONSTRAINT m_inventory_waterdetails_fk02 FOREIGN KEY (m_grid_id) REFERENCES m_grids(id)
)
GO

CREATE INDEX m_inventory_waterdetail_idx01 ON m_inventory_waterdetails(m_inventory_water_id ASC) 
GO

CREATE INDEX m_inventory_waterdetail_idx02 ON m_inventory_waterdetails(m_grid_id ASC) 
GO

CREATE TABLE m_inventory_waterinventories (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_waterdetail_id bigint NOT NULL ,
	m_inventory_id bigint NOT NULL ,
	m_product_id int NULL ,
	m_grid_id int NULL ,
	c_project_id bigint NULL ,
	quantity_box int NOT NULL DEFAULT 0 ,
	quantity decimal(12,4) NOT NULL DEFAULT 0 ,
	pallet varchar(255) NULL ,
	barcode varchar(255) NULL ,
	carton_no varchar(25) NULL ,
	lot_no varchar(50) NULL ,
	PRIMARY KEY (id) ,
	CONSTRAINT m_inventory_waterinventories_fk01 FOREIGN KEY (m_inventory_waterdetail_id) REFERENCES m_inventory_waterdetails(id) ,
	CONSTRAINT m_inventory_waterinventories_fk02 FOREIGN KEY (m_inventory_id) REFERENCES m_inventories(id) ,
	CONSTRAINT m_inventory_waterinventories_fk03 FOREIGN KEY (m_product_id) REFERENCES m_products(id) ,
	CONSTRAINT m_inventory_waterinventories_fk04 FOREIGN KEY (m_grid_id) REFERENCES m_grids(id) ,
	CONSTRAINT m_inventory_waterinventories_fk05 FOREIGN KEY (c_project_id) REFERENCES c_projects(id)
)
GO

CREATE INDEX m_inventory_waterinvent_idx01 ON m_inventory_waterinventories(m_inventory_waterdetail_id ASC) 
GO

CREATE INDEX m_inventory_waterinvent_idx02 ON m_inventory_waterinventories(m_inventory_id ASC) 
GO

CREATE INDEX m_inventory_waterinvent_idx03 ON m_inventory_waterinventories(m_product_id ASC) 
GO

CREATE INDEX m_inventory_waterinvent_idx04 ON m_inventory_waterinventories(m_grid_id ASC) 
GO

CREATE INDEX m_inventory_waterinvent_idx05 ON m_inventory_waterinventories(c_project_id ASC) 
GO
