ALTER TABLE [dbo].[m_inventories]
ADD [quantity_per_box] decimal(12,4) NOT NULL DEFAULT 0 
GO

UPDATE	i SET
		i.quantity_per_box = p.netto
FROM	m_inventories i
		INNER JOIN m_products p ON p.id = i.m_product_id
WHERE		i.quantity_box > 1
		AND p.netto = i.quantity / i.quantity_box
		AND	p.netto > 0
GO
