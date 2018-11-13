<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewSalesMtcSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* SUMMARY BY SALES */
        DB::statement("
            CREATE VIEW `sales_mtc_summary_by_sales` AS
                SELECT
                    stores.id AS store_id,
                    regions.id AS region_id,
                    areas.id AS area_id,
                    sub_areas.id AS subarea_id,
                    employees.id AS employee_id,
                    DATE_FORMAT(sales.date, '%M %Y') AS periode,
                    UPPER(regions.name) AS region,
                    UPPER(stores.is_jawa) AS is_jawa,
                    UPPER((IF(positions.`level` = 'mdmtc', 'md', employees.`status`))) AS jabatan,
                    UPPER(employees.name) AS employee_name,
                    UPPER(areas.name) AS area,
                    UPPER(sub_areas.name) AS sub_area,
                    UPPER(CONCAT(stores.name1, ' (', stores.name2, ')')) AS store_name,
                    UPPER(accounts.name) AS account,
                    UPPER(categories.name) AS category,
                    UPPER(sub_categories.name) AS product_line,
                    UPPER(products.name) AS product_name,
                    -- (CASE WHEN
                    --     (IFNULL(
                    --         (
                    --          SELECT `prices`.price FROM `prices` 
                    --          WHERE `prices`.id_product = `products`.id
                    --          AND MONTH(`sales`.`date`) = MONTH(`prices`.`rilis`)
                    --          AND YEAR(`sales`.`date`) = YEAR(`prices`.`rilis`)
                    --          AND `prices`.`deleted_at` IS NULL
                    --          ORDER BY `prices`.`rilis` DESC
                    --          LIMIT 1
                    --         )
                    --     , 0) > 0)
                    --     THEN
                    --         IFNULL(
                    --             (
                    --              SELECT `prices`.price FROM `prices` 
                    --              WHERE `prices`.id_product = `products`.id
                    --              AND MONTH(`sales`.`date`) = MONTH(`prices`.`rilis`)
                    --              AND YEAR(`sales`.`date`) = YEAR(`prices`.`rilis`)
                    --              AND `prices`.`deleted_at` IS NULL
                    --              ORDER BY `prices`.`rilis` DESC
                    --              LIMIT 1
                    --             )
                    --         , 0)
                    --     ELSE
                    --         IFNULL(
                    --             (
                    --              SELECT `prices`.price FROM `prices` 
                    --              WHERE `prices`.id_product = `products`.id
                    --              AND `sales`.`date` >= `prices`.`rilis`
                    --              AND `prices`.`deleted_at` IS NULL
                    --              ORDER BY `prices`.`rilis` DESC
                    --              LIMIT 1
                    --             )
                    --         , 0)
                    --     END
                    -- ) AS price,
                    -- IF(sales.`type` = 'Sell Out', detail_sales.qty, 0) AS actual_out_qty,
                    -- IF(sales.`type` = 'Sell In', detail_sales.qty, 0) AS actual_in_qty,
                    -- (
                    --     IFNULL(
                    --         (
                    --          SELECT `prices`.price FROM `prices` 
                    --          WHERE `prices`.id_product = `products`.id
                    --          AND `sales`.`date` >= `prices`.`rilis`
                    --          AND `prices`.`deleted_at` IS NULL
                    --          ORDER BY `prices`.`rilis` DESC
                    --          LIMIT 1
                    --         )
                    --     , 0) * IF(sales.`type` = 'Sell Out', detail_sales.qty, 0)
                    -- ) AS `actual_out_value`,
                    -- (
                    --     IFNULL(
                    --         (
                    --          SELECT `prices`.price FROM `prices` 
                    --          WHERE `prices`.id_product = `products`.id
                    --          AND `sales`.`date` >= `prices`.`rilis`
                    --          AND `prices`.`deleted_at` IS NULL
                    --          ORDER BY `prices`.`rilis` DESC
                    --          LIMIT 1
                    --         )
                    --     , 0) * IF(sales.`type` = 'Sell In', detail_sales.qty, 0)
                    -- ) AS `actual_in_value`,
                    -- IF(
                    --     positions.`level` = 'mdmtc',
                    --         (
                    --             IFNULL(
                    --                 (
                    --                  SELECT `prices`.price FROM `prices` 
                    --                  WHERE `prices`.id_product = `products`.id
                    --                  AND `sales`.`date` >= `prices`.`rilis`
                    --                  AND `prices`.`deleted_at` IS NULL
                    --                  ORDER BY `prices`.`rilis` DESC
                    --                  LIMIT 1
                    --                 )
                    --             , 0) * IF(sales.`type` = 'Sell In', detail_sales.qty, 0)
                    --         )
                    --     ,
                    --         (
                    --             IFNULL(
                    --                 (
                    --                  SELECT `prices`.price FROM `prices` 
                    --                  WHERE `prices`.id_product = `products`.id
                    --                  AND `sales`.`date` >= `prices`.`rilis`
                    --                  AND `prices`.`deleted_at` IS NULL
                    --                  ORDER BY `prices`.`rilis` DESC
                    --                  LIMIT 1
                    --                 )
                    --             , 0) * IF(sales.`type` = 'Sell Out', detail_sales.qty, 0)
                    --         )                        
                    -- ) AS total_actual,
                    -- IFNULL(
                    --         (
                    --          SELECT `targets`.`quantity` FROM `targets` 
                    --          WHERE `targets`.id_product = `detail_sales`.id_product
                    --          AND `targets`.id_store = `sales`.`id_store`
                    --          AND `targets`.`id_employee` = `sales`.id_employee
                    --          AND MONTH(`targets`.`rilis`) = MONTH(`sales`.date)
                    --          AND YEAR(`targets`.`rilis`) = YEAR(`sales`.date)
                    --          AND `targets`.`deleted_at` IS NULL
                    --          LIMIT 1
                    --         )
                    --  , 0) AS target_qty,
                    --  (CASE WHEN
                    --     (IFNULL(
                    --         (
                    --          SELECT `prices`.price FROM `prices` 
                    --          WHERE `prices`.id_product = `products`.id
                    --          AND MONTH(`sales`.`date`) = MONTH(`prices`.`rilis`)
                    --          AND YEAR(`sales`.`date`) = YEAR(`prices`.`rilis`)
                    --          AND `prices`.`deleted_at` IS NULL
                    --          ORDER BY `prices`.`rilis` DESC
                    --          LIMIT 1
                    --         )
                    --     , 0) > 0)
                    --     THEN
                    --         IFNULL(
                    --             (
                    --              SELECT `prices`.price FROM `prices` 
                    --              WHERE `prices`.id_product = `products`.id
                    --              AND MONTH(`sales`.`date`) = MONTH(`prices`.`rilis`)
                    --              AND YEAR(`sales`.`date`) = YEAR(`prices`.`rilis`)
                    --              AND `prices`.`deleted_at` IS NULL
                    --              ORDER BY `prices`.`rilis` DESC
                    --              LIMIT 1
                    --             )
                    --         , 0)
                    --     ELSE
                    --         IFNULL(
                    --             (
                    --              SELECT `prices`.price FROM `prices` 
                    --              WHERE `prices`.id_product = `products`.id
                    --              AND `sales`.`date` >= `prices`.`rilis`
                    --              AND `prices`.`deleted_at` IS NULL
                    --              ORDER BY `prices`.`rilis` DESC
                    --              LIMIT 1
                    --             )
                    --         , 0)
                    --     END
                    -- ) *
                    -- IFNULL(
                    --         (
                    --          SELECT `targets`.`quantity` FROM `targets` 
                    --          WHERE `targets`.id_product = `detail_sales`.id_product
                    --          AND `targets`.id_store = `sales`.`id_store`
                    --          AND `targets`.`id_employee` = `sales`.id_employee
                    --          AND MONTH(`targets`.`rilis`) = MONTH(`sales`.date)
                    --          AND YEAR(`targets`.`rilis`) = YEAR(`sales`.date)
                    --          AND `targets`.`deleted_at` IS NULL
                    --          LIMIT 1
                    --         )
                    --  , 0) AS target_value
                FROM `detail_sales`
                JOIN `sales` ON detail_sales.id_sales = sales.id
                LEFT JOIN `stores` ON sales.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                LEFT JOIN `employees` ON sales.id_employee = employees.id
                LEFT JOIN `positions` ON employees.id_position = positions.id
                LEFT JOIN `products` ON detail_sales.id_product = products.id
                LEFT JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                LEFT JOIN `categories` ON sub_categories.id_category = categories.id
                WHERE
                IFNULL(
                        (
                         SELECT `targets`.`quantity` FROM `targets` 
                         WHERE `targets`.id_product = `detail_sales`.id_product
                         AND `targets`.id_store = `sales`.`id_store`
                         AND `targets`.`id_employee` = `sales`.id_employee
                         AND MONTH(`targets`.`rilis`) = MONTH(`sales`.date)
                         AND YEAR(`targets`.`rilis`) = YEAR(`sales`.date)
                         AND `targets`.`deleted_at` IS NULL
                         LIMIT 1
                        )
                 , -1) = -1
        ");

        /* SUMMARY BY TARGET */
        DB::statement("
            CREATE VIEW `sales_mtc_summary_by_target` AS
                SELECT
                    stores.id AS store_id,
                    regions.id AS region_id,
                    areas.id AS area_id,
                    sub_areas.id AS subarea_id,
                    employees.id AS employee_id,
                    DATE_FORMAT(targets.rilis, '%M %Y') AS periode,
                    UPPER(regions.name) AS region,
                    UPPER(stores.is_jawa) AS is_jawa,
                    UPPER((IF(positions.`level` = 'mdmtc', 'md', employees.`status`))) AS jabatan,
                    UPPER(employees.name) AS employee_name,
                    UPPER(areas.name) AS area,
                    UPPER(sub_areas.name) AS sub_area,
                    UPPER(CONCAT(stores.name1, ' (', stores.name2, ')')) AS store_name,
                    UPPER(accounts.name) AS account,
                    UPPER(categories.name) AS category,
                    UPPER(sub_categories.name) AS product_line,
                    UPPER(products.name) AS product_name,
                    (CASE WHEN
                        (IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND MONTH(`targets`.`rilis`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`targets`.`rilis`) = YEAR(`prices`.`rilis`)
                             AND `prices`.`deleted_at` IS NULL
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                        , 0) > 0)
                        THEN
                            IFNULL(
                                (
                                 SELECT `prices`.price FROM `prices` 
                                 WHERE `prices`.id_product = `products`.id
                                 AND MONTH(`targets`.`rilis`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`targets`.`rilis`) = YEAR(`prices`.`rilis`)
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        ELSE
                            IFNULL(
                                (
                                 SELECT `prices`.price FROM `prices` 
                                 WHERE `prices`.id_product = `products`.id
                                 AND `targets`.`rilis` >= `prices`.`rilis`
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        END
                    ) AS price,
                    IFNULL(
                        (
                            SELECT SUM(detail_sales.qty)
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = targets.id_store
                            AND sales.id_employee = targets.id_employee
                            AND MONTH(sales.date) = MONTH(targets.rilis)
                            AND YEAR(sales.date) = YEAR(targets.rilis)
                            AND detail_sales.id_product = targets.id_product
                            AND sales.type = 'Sell Out'
                        )
                    , 0) AS actual_out_qty, 
                    IFNULL(
                        (
                            SELECT SUM(detail_sales.qty)
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = targets.id_store
                            AND sales.id_employee = targets.id_employee
                            AND MONTH(sales.date) = MONTH(targets.rilis)
                            AND YEAR(sales.date) = YEAR(targets.rilis)
                            AND detail_sales.id_product = targets.id_product
                            AND sales.type = 'Sell In'
                        )
                    , 0) AS actual_in_qty,
                    IFNULL(
                        (
                            SELECT SUM(
                                IF(sales.`type` = 'Sell Out', detail_sales.qty, 0) *
                                IFNULL(
                                    (
                                     SELECT `prices`.price FROM `prices` 
                                     WHERE `prices`.id_product = `products`.id
                                     AND `sales`.`date` >= `prices`.`rilis`
                                     AND `prices`.`deleted_at` IS NULL
                                     ORDER BY `prices`.`rilis` DESC
                                     LIMIT 1
                                    )
                                , 0)
                            )
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = targets.id_store
                            AND sales.id_employee = targets.id_employee
                            AND MONTH(sales.date) = MONTH(targets.rilis)
                            AND YEAR(sales.date) = YEAR(targets.rilis)
                            AND detail_sales.id_product = targets.id_product
                            AND sales.type = 'Sell Out'
                        )
                    , 0) AS actual_out_value,
                    IFNULL(
                        (
                            SELECT SUM(
                                IF(sales.`type` = 'Sell In', detail_sales.qty, 0) *
                                IFNULL(
                                    (
                                     SELECT `prices`.price FROM `prices` 
                                     WHERE `prices`.id_product = `products`.id
                                     AND `sales`.`date` >= `prices`.`rilis`
                                     AND `prices`.`deleted_at` IS NULL
                                     ORDER BY `prices`.`rilis` DESC
                                     LIMIT 1
                                    )
                                , 0)
                            )
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = targets.id_store
                            AND sales.id_employee = targets.id_employee
                            AND MONTH(sales.date) = MONTH(targets.rilis)
                            AND YEAR(sales.date) = YEAR(targets.rilis)
                            AND detail_sales.id_product = targets.id_product
                            AND sales.type = 'Sell In'
                        )
                    , 0) AS actual_in_value,
                    IF(
                        positions.`level` = 'mdmtc',
                            IFNULL(
                                (
                                    SELECT SUM(
                                        IF(sales.`type` = 'Sell In', detail_sales.qty, 0) *
                                        IFNULL(
                                            (
                                             SELECT `prices`.price FROM `prices` 
                                             WHERE `prices`.id_product = `products`.id
                                             AND `sales`.`date` >= `prices`.`rilis`
                                             AND `prices`.`deleted_at` IS NULL
                                             ORDER BY `prices`.`rilis` DESC
                                             LIMIT 1
                                            )
                                        , 0)
                                    )
                                    FROM detail_sales
                                    JOIN sales ON sales.id = detail_sales.id_sales
                                    WHERE sales.id_store = targets.id_store
                                    AND sales.id_employee = targets.id_employee
                                    AND MONTH(sales.date) = MONTH(targets.rilis)
                                    AND YEAR(sales.date) = YEAR(targets.rilis)
                                    AND detail_sales.id_product = targets.id_product
                                    AND sales.type = 'Sell In'
                                )
                            , 0)
                        ,
                            IFNULL(
                                (
                                    SELECT SUM(
                                        IF(sales.`type` = 'Sell Out', detail_sales.qty, 0) *
                                        IFNULL(
                                            (
                                             SELECT `prices`.price FROM `prices` 
                                             WHERE `prices`.id_product = `products`.id
                                             AND `sales`.`date` >= `prices`.`rilis`
                                             AND `prices`.`deleted_at` IS NULL
                                             ORDER BY `prices`.`rilis` DESC
                                             LIMIT 1
                                            )
                                        , 0)
                                    )
                                    FROM detail_sales
                                    JOIN sales ON sales.id = detail_sales.id_sales
                                    WHERE sales.id_store = targets.id_store
                                    AND sales.id_employee = targets.id_employee
                                    AND MONTH(sales.date) = MONTH(targets.rilis)
                                    AND YEAR(sales.date) = YEAR(targets.rilis)
                                    AND detail_sales.id_product = targets.id_product
                                    AND sales.type = 'Sell Out'
                                )
                            , 0)                      
                    ) AS total_actual,
                    targets.quantity as target_qty,
                    (CASE WHEN
                        (IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND MONTH(`targets`.`rilis`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`targets`.`rilis`) = YEAR(`prices`.`rilis`)
                             AND `prices`.`deleted_at` IS NULL
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                        , 0) > 0)
                        THEN
                            IFNULL(
                                (
                                 SELECT `prices`.price FROM `prices` 
                                 WHERE `prices`.id_product = `products`.id
                                 AND MONTH(`targets`.`rilis`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`targets`.`rilis`) = YEAR(`prices`.`rilis`)
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        ELSE
                            IFNULL(
                                (
                                 SELECT `prices`.price FROM `prices` 
                                 WHERE `prices`.id_product = `products`.id
                                 AND `targets`.`rilis` >= `prices`.`rilis`
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        END
                    ) * targets.quantity AS target_value
                FROM `targets`
                JOIN `stores` ON targets.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                LEFT JOIN `employees` ON targets.id_employee = employees.id
                LEFT JOIN `positions` ON employees.id_position = positions.id
                LEFT JOIN `products` ON targets.id_product = products.id
                LEFT JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                LEFT JOIN `categories` ON sub_categories.id_category = categories.id
                -- WHERE 
                -- IF(
                --     positions.`level` = 'mdmtc',
                --     IFNULL(
                --         (
                --             SELECT SUM(
                --                 IF(sales.`type` = 'Sell In', detail_sales.qty, 0) *
                --                 IFNULL(
                --                     (
                --                         SELECT `prices`.price FROM `prices` 
                --                         WHERE `prices`.id_product = `products`.id
                --                         AND `sales`.`date` >= `prices`.`rilis`
                --                         AND `prices`.`deleted_at` IS NULL
                --                         ORDER BY `prices`.`rilis` DESC
                --                         LIMIT 1
                --                     )
                --                     , 0)
                --             )
                --             FROM detail_sales
                --             JOIN sales ON sales.id = detail_sales.id_sales
                --             WHERE sales.id_store = targets.id_store
                --             AND sales.id_employee = targets.id_employee
                --             AND MONTH(sales.date) = MONTH(targets.rilis)
                --             AND YEAR(sales.date) = YEAR(targets.rilis)
                --             AND detail_sales.id_product = targets.id_product
                --             AND sales.type = 'Sell In'
                --         )
                --         , 0)
                --     ,
                --     IFNULL(
                --         (
                --             SELECT SUM(
                --                 IF(sales.`type` = 'Sell Out', detail_sales.qty, 0) *
                --                 IFNULL(
                --                     (
                --                         SELECT `prices`.price FROM `prices` 
                --                         WHERE `prices`.id_product = `products`.id
                --                         AND `sales`.`date` >= `prices`.`rilis`
                --                         AND `prices`.`deleted_at` IS NULL
                --                         ORDER BY `prices`.`rilis` DESC
                --                         LIMIT 1
                --                     )
                --                     , 0)
                --             )
                --             FROM detail_sales
                --             JOIN sales ON sales.id = detail_sales.id_sales
                --             WHERE sales.id_store = targets.id_store
                --             AND sales.id_employee = targets.id_employee
                --             AND MONTH(sales.date) = MONTH(targets.rilis)
                --             AND YEAR(sales.date) = YEAR(targets.rilis)
                --             AND detail_sales.id_product = targets.id_product
                --             AND sales.type = 'Sell Out'
                --         )
                --         , 0)                      
                -- ) = 0          
        ");

        /* SUMMARY SALES */
        DB::statement("
            CREATE VIEW `sales_mtc_summary` AS
                SELECT * FROM `sales_mtc_summary_by_sales`
                UNION ALL
                SELECT * FROM `sales_mtc_summary_by_target`
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS sales_mtc_summary_by_sales');
        DB::statement('DROP VIEW IF EXISTS sales_mtc_summary_by_target');
        DB::statement('DROP VIEW IF EXISTS sales_mtc_summary');
    }
}
