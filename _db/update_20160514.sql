CREATE TABLE sys_code_number (
	id int NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(50) NOT NULL ,
	current_number bigint NOT NULL DEFAULT 1 ,
	format varchar(100) NOT NULL ,
	number_length int NOT NULL DEFAULT 1 ,
	PRIMARY KEY (id)
)
GO

CREATE UNIQUE INDEX sys_code_number_idx01 ON sys_code_number
	(code ASC) 
GO

ALTER TABLE m_inventories
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX m_inventories_idx04 ON m_inventories
	(c_businesspartner_id ASC) 
GO

ALTER TABLE m_inventories
	ADD CONSTRAINT m_inventories_fk04 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_inventorylogs
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX m_inventorylogs_idx05 ON m_inventorylogs
	(c_businesspartner_id ASC) 
GO

ALTER TABLE cus_m_inventory_inbounddetails
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX cus_m_inventory_inboundd_idx06 ON cus_m_inventory_inbounddetails
	(c_businesspartner_id ASC) 
GO

ALTER TABLE cus_m_inventory_inbounddetails
	ADD CONSTRAINT cus_m_inventory_inbounddetails_fk05 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_inventory_assemblysources
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX m_inventory_assemblysour_idx06 ON m_inventory_assemblysources
	(c_businesspartner_id ASC) 
GO

ALTER TABLE m_inventory_assemblysources
	ADD CONSTRAINT m_inventory_assemblysour_fk06 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_inventory_assemblytargets
	ADD c_businesspartner_id int NULL 
GO

CREATE INDEX m_inventory_assemblytarg_idx06 ON m_inventory_assemblytargets
	(c_businesspartner_id ASC) 
GO

ALTER TABLE m_inventory_assemblytargets
	ADD CONSTRAINT m_inventory_assemblytarg_fk06 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
GO

ALTER TABLE m_products
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventories
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventorylogs
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventory_inbounddetails
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE cus_m_inventory_inbounddetails
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventory_assemblysources
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventory_assemblytargets
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

ALTER TABLE m_inventory_picklistdetails
	ADD volume_length decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_width decimal(12,4) NOT NULL DEFAULT 0 ,
		volume_height decimal(12,4) NOT NULL DEFAULT 0 
GO

CREATE TABLE m_inventory_invoices (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	code varchar(30) NOT NULL ,
	invoice_date date NOT NULL ,
	period_from datetime NOT NULL ,
	period_to datetime NOT NULL ,
	c_businesspartner_id int NOT NULL ,
	invoice_handling_in bit NOT NULL DEFAULT 0 ,
	invoice_handling_in_price decimal(14,4) NOT NULL DEFAULT 0 ,
	invoice_handling_out bit NOT NULL DEFAULT 0 ,
	invoice_handling_out_price decimal(14,4) NOT NULL DEFAULT 0 ,
	invoice_handling_storage bit NOT NULL DEFAULT 0 ,
	invoice_handling_storage_price decimal(14,4) NOT NULL DEFAULT 0 ,
	invoice_calculate varchar(20) NOT NULL ,
	tax decimal(5,2) NOT NULL DEFAULT 0 ,
	jo_no varchar(100) NULL ,
	term_of_payment varchar(20) NULL ,
	flight_no varchar(100) NULL ,
	hawb_hbl_no varchar(100) NULL ,
	mawb_mbl_no varchar(100) NULL ,
	from_to varchar(255) NULL ,
	reference varchar(255) NULL ,
	bank_ac_name varchar(100) NULL ,
	bank_ac_no varchar(100) NULL ,
	bank_name varchar(100) NULL ,
	bank_branch varchar(100) NULL ,
	bank_swift_code varchar(50) NULL ,
	PRIMARY KEY (id),
	CONSTRAINT m_inventory_invoices_fk01 FOREIGN KEY (c_businesspartner_id) REFERENCES c_businesspartners (id)
)
GO

CREATE UNIQUE INDEX m_inventory_invoices_idx01 ON m_inventory_invoices
	(code ASC) 
GO

CREATE INDEX m_inventory_invoices_idx02 ON dbo.m_inventory_invoices
	(c_businesspartner_id ASC) 
GO

CREATE TABLE m_inventory_invoicedetails (
	id bigint NOT NULL IDENTITY(1,1) ,
	created smalldatetime NULL ,
	created_by int NULL ,
	updated smalldatetime NULL ,
	updated_by int NULL ,
	m_inventory_invoice_id bigint NOT NULL ,
	invoice_type varchar(20) NOT NULL ,
	description varchar(255) NULL ,
	parts_num decimal(12,4) NOT NULL DEFAULT 0 ,
	weight decimal(12,4) NOT NULL DEFAULT 0 ,
	amount decimal(12,4) NOT NULL DEFAULT 0 ,
	PRIMARY KEY (id),
	CONSTRAINT m_inventory_invoicedetails_fk01 FOREIGN KEY (m_inventory_invoice_id) REFERENCES m_inventory_invoices (id)
)
GO

CREATE INDEX m_inventory_invoicedetai_idx01 ON m_inventory_invoicedetails
	(m_inventory_invoice_id ASC) 
GO
