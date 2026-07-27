-- Adds "Шаблон комплектов" (template_category) as a third node_type value,
-- and reclassifies every leaf (childless) node under a 'set_category' root
-- to that new type — these are the shared item pages like "20-1579-blue".
ALTER TABLE jadmin_nav_tree MODIFY COLUMN node_type
    ENUM('text','set_category','template_category') NOT NULL DEFAULT 'text';

UPDATE jadmin_nav_tree t
SET node_type = 'template_category'
WHERE t.id IN (
    SELECT node_id FROM (
        SELECT DISTINCT h.node_id
        FROM jadmin_nav_hier h
        INNER JOIN jadmin_nav_tree cat ON (cat.id = h.pid AND cat.node_type = 'set_category')
    ) AS leaf_ids
)
AND t.id NOT IN (
    SELECT pid FROM (SELECT DISTINCT pid FROM jadmin_nav_tree) AS parent_ids
);
