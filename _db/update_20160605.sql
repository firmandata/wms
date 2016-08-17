ALTER TABLE m_inventory_invoices
	DROP COLUMN from_to
GO

EXEC sp_rename 'm_inventory_invoices.flight_no', 'plate_no', 'COLUMN'
GO

EXEC sp_rename 'm_inventory_invoices.hawb_hbl_no', 'si_sj_so_no', 'COLUMN'
GO

EXEC sp_rename 'm_inventory_invoices.mawb_mbl_no', 'spk_po_no', 'COLUMN'
GO

