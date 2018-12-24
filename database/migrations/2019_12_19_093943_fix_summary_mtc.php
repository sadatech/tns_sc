<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSummaryMtc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS sales_mtc_summary');

         /* SUMMARY SALES V.2 */
        DB::statement("
            CREATE VIEW `sales_mtc_summary` AS
                SELECT
                    stores.id AS id_store,
                    employees.id AS id_employee,
                    areas.id AS id_area,
                    products.id AS id_product,
                    channels.id AS id_channel,
                    categories.id AS id_category,
                    sub_categories.id AS id_subcategory,
                    sub_areas.id AS id_sub_area,
                    channels.name AS channel,
                    mtc_report_templates.date,
                    DATE_FORMAT(mtc_report_templates.date, '%M %Y') AS periode,
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
                             AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND `mtc_report_templates`.`date` >= `prices`.`rilis`
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
                            AND sales.type = 'Sell Out'
                        )
                    , 0) AS actual_out_qty,
                    IFNULL(
                        (
                            SELECT SUM(detail_sales.qty)
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                                    WHERE sales.id_store = mtc_report_templates.id_store
                                    AND sales.id_employee = mtc_report_templates.id_employee
                                    AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                                    AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                                    AND detail_sales.id_product = mtc_report_templates.id_product
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
                                    WHERE sales.id_store = mtc_report_templates.id_store
                                    AND sales.id_employee = mtc_report_templates.id_employee
                                    AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                                    AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                                    AND detail_sales.id_product = mtc_report_templates.id_product
                                    AND sales.type = 'Sell Out'
                                )
                            , 0)                      
                    ) AS total_actual,
                    IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `mtc_report_templates`.id_product
                             AND `targets`.id_store = `mtc_report_templates`.`id_store`
                             AND `targets`.`id_employee` = `mtc_report_templates`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`mtc_report_templates`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`mtc_report_templates`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                     , 0) AS target_qty,
                     (CASE WHEN
                        (IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND `mtc_report_templates`.`date` >= `prices`.`rilis`
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        END
                    ) *
                    IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `mtc_report_templates`.id_product
                             AND `targets`.id_store = `mtc_report_templates`.`id_store`
                             AND `targets`.`id_employee` = `mtc_report_templates`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`mtc_report_templates`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`mtc_report_templates`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                     , 0) AS target_value
                FROM `mtc_report_templates`
                LEFT JOIN `stores` ON mtc_report_templates.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                LEFT JOIN `employees` ON mtc_report_templates.id_employee = employees.id
                LEFT JOIN `positions` ON employees.id_position = positions.id
                LEFT JOIN `products` ON mtc_report_templates.id_product = products.id
                LEFT JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                LEFT JOIN `categories` ON sub_categories.id_category = categories.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS sales_mtc_summary');

         /* SUMMARY SALES V.2 */
        DB::statement("
            CREATE VIEW `sales_mtc_summary` AS
                SELECT
                    stores.id AS id_store,
                    employees.id AS id_employee,
                    areas.id AS id_area,
                    products.id AS id_product,
                    channels.id AS id_channel,
                    categories.id AS id_category,
                    sub_areas.id AS id_sub_area,
                    channels.name AS channel,
                    mtc_report_templates.date,
                    DATE_FORMAT(mtc_report_templates.date, '%M %Y') AS periode,
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
                             AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND `mtc_report_templates`.`date` >= `prices`.`rilis`
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
                            AND sales.type = 'Sell Out'
                        )
                    , 0) AS actual_out_qty,
                    IFNULL(
                        (
                            SELECT SUM(detail_sales.qty)
                            FROM detail_sales
                            JOIN sales ON sales.id = detail_sales.id_sales
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                            WHERE sales.id_store = mtc_report_templates.id_store
                            AND sales.id_employee = mtc_report_templates.id_employee
                            AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                            AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                            AND detail_sales.id_product = mtc_report_templates.id_product
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
                                    WHERE sales.id_store = mtc_report_templates.id_store
                                    AND sales.id_employee = mtc_report_templates.id_employee
                                    AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                                    AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                                    AND detail_sales.id_product = mtc_report_templates.id_product
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
                                    WHERE sales.id_store = mtc_report_templates.id_store
                                    AND sales.id_employee = mtc_report_templates.id_employee
                                    AND MONTH(sales.date) = MONTH(mtc_report_templates.date)
                                    AND YEAR(sales.date) = YEAR(mtc_report_templates.date)
                                    AND detail_sales.id_product = mtc_report_templates.id_product
                                    AND sales.type = 'Sell Out'
                                )
                            , 0)                      
                    ) AS total_actual,
                    IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `mtc_report_templates`.id_product
                             AND `targets`.id_store = `mtc_report_templates`.`id_store`
                             AND `targets`.`id_employee` = `mtc_report_templates`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`mtc_report_templates`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`mtc_report_templates`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                     , 0) AS target_qty,
                     (CASE WHEN
                        (IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                             AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND MONTH(`mtc_report_templates`.`date`) = MONTH(`prices`.`rilis`)
                                 AND YEAR(`mtc_report_templates`.`date`) = YEAR(`prices`.`rilis`)
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
                                 AND `mtc_report_templates`.`date` >= `prices`.`rilis`
                                 AND `prices`.`deleted_at` IS NULL
                                 ORDER BY `prices`.`rilis` DESC
                                 LIMIT 1
                                )
                            , 0)
                        END
                    ) *
                    IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `mtc_report_templates`.id_product
                             AND `targets`.id_store = `mtc_report_templates`.`id_store`
                             AND `targets`.`id_employee` = `mtc_report_templates`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`mtc_report_templates`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`mtc_report_templates`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                     , 0) AS target_value
                FROM `mtc_report_templates`
                LEFT JOIN `stores` ON mtc_report_templates.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                LEFT JOIN `employees` ON mtc_report_templates.id_employee = employees.id
                LEFT JOIN `positions` ON employees.id_position = positions.id
                LEFT JOIN `products` ON mtc_report_templates.id_product = products.id
                LEFT JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                LEFT JOIN `categories` ON sub_categories.id_category = categories.id
        ");
    }
}
