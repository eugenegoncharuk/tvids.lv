-- Adds the "Тип" (node_type) classification to menu-tree nodes.
-- Every existing node defaults to 'text'; the three "sets" category roots
-- (whose subcategories all share the same leaf item pages, e.g. "20-1579-blue")
-- are reclassified as 'set_category'.
ALTER TABLE jadmin_nav_tree ADD COLUMN node_type ENUM('text','set_category') NOT NULL DEFAULT 'text' AFTER level;

UPDATE jadmin_nav_tree SET node_type = 'set_category'
WHERE name IN ('Бязь - Комплекты', 'Сатин - Комплекты', 'Детские - Комплекты');
