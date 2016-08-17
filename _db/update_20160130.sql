ALTER TABLE m_inventory_picklistdetails
	ADD c_project_id bigint NULL 
GO

CREATE INDEX m_inventory_picklistdeta_idx06 ON m_inventory_picklistdetails
	(c_project_id ASC) 
GO

ALTER TABLE m_inventory_picklistdetails
	ADD CONSTRAINT m_inventory_picklistdetails_fk06 FOREIGN KEY (c_project_id) REFERENCES c_projects (id)
GO
