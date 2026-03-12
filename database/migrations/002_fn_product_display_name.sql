-- ================================================================
-- Migracion 002: Crear funcion fn_product_display_name
-- Fecha: 2026-03-02
-- Descripcion: Funcion que retorna el nombre del producto con sus
--              atributos de variante en formato:
--              "Nombre Producto (Valor1, Valor2)"
--              Si no tiene variante, retorna solo el nombre.
-- ================================================================

DELIMITER $$

DROP FUNCTION IF EXISTS fn_product_display_name$$

CREATE FUNCTION fn_product_display_name(p_product_id INT, p_variant_id INT)
RETURNS VARCHAR(500)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_name VARCHAR(255);
    DECLARE v_attrs VARCHAR(255);

    SELECT name INTO v_name FROM tec_products WHERE id = p_product_id;

    IF p_variant_id IS NOT NULL AND p_variant_id > 0 THEN
        SELECT GROUP_CONCAT(av.valor ORDER BY a.orden SEPARATOR ', ')
        INTO v_attrs
        FROM tec_variante_atributos va
        JOIN tec_atributos a ON va.atributo_id = a.id
        JOIN tec_atributo_valores av ON va.valor_id = av.id
        WHERE va.variante_id = p_variant_id;

        IF v_attrs IS NOT NULL AND v_attrs != '' THEN
            RETURN CONCAT(v_name, ' (', v_attrs, ')');
        END IF;
    END IF;

    RETURN v_name;
END$$

DELIMITER ;
