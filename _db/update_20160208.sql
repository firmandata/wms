ALTER TABLE m_products
	ADD price decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE c_orderouts
	ADD status_manufacture_schedule varchar(50) NULL 
GO

ALTER TABLE c_orderoutdetails
	ADD status_manufacture_schedule varchar(50) NULL 
GO

CREATE TABLE m_boms (
	id int NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	name varchar(150) NOT NULL ,
	loss_rate decimal(3,2) NOT NULL DEFAULT 0 ,
	labor_cost decimal(14,4) NOT NULL DEFAULT 0 ,
	manufacturing_expenses decimal(3,2) NOT NULL DEFAULT 0 ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX m_boms_idx01 ON m_boms
	(code ASC) 
GO

CREATE TABLE m_bomdetails (
	id int NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_bom_id int NOT NULL ,
	m_product_id int NOT NULL ,
	quantity decimal(12,4) NOT NULL DEFAULT 0 ,
	PRIMARY KEY (id),
	CONSTRAINT m_bomdetails_fk01 FOREIGN KEY (m_bom_id) REFERENCES m_boms (id),
	CONSTRAINT m_bomdetails_fk02 FOREIGN KEY (m_product_id) REFERENCES m_products (id)
)
GO

CREATE INDEX m_bomdetails_idx01 ON m_bomdetails
	(m_bom_id ASC) 
GO

CREATE INDEX m_bomdetails_idx02 ON m_bomdetails
	(m_product_id ASC) 
GO

CREATE TABLE m_manufacture_schedules (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	schedule_date date NULL ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX m_manufacture_schedules_idx01 ON m_manufacture_schedules
	(code ASC) 
GO

CREATE TABLE m_manufacture_scheduledetails (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_manufacture_schedule_id bigint NOT NULL ,
	c_orderoutdetail_id bigint NOT NULL ,
	production_date date NOT NULL ,
	release_date date NULL ,
	quantity_box int NOT NULL DEFAULT 0 ,
	quantity decimal(12,4) NOT NULL DEFAULT 0 ,
	notes nvarchar(500) NULL ,
	PRIMARY KEY (id),
	CONSTRAINT m_manufacture_scheduledetails_fk01 FOREIGN KEY (m_manufacture_schedule_id) REFERENCES m_manufacture_schedules (id),
	CONSTRAINT m_manufacture_scheduledetails_fk02 FOREIGN KEY (c_orderoutdetail_id) REFERENCES c_orderoutdetails (id)
)
GO

CREATE INDEX m_manufacture_schedulede_idx01 ON m_manufacture_scheduledetails
	(m_manufacture_schedule_id ASC) 
GO

CREATE INDEX m_manufacture_schedulede_idx02 ON m_manufacture_scheduledetails
	(c_orderoutdetail_id ASC) 
GO
