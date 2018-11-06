<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewSellInSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW `sell_in_summary` AS
                SELECT
                    detail_ins.id AS id,
                    stores.id AS store_id,
                    regions.id AS region_id,
                    areas.id AS area_id,
                    sub_areas.id AS subarea_id,
                    employees.id AS employee_id,
                    sell_ins.week,
                    regions.name AS region,
                    areas.name AS area,
                    sub_areas.name AS sub_area,
                    accounts.name AS account,
                    channels.name AS channel,
                    stores.name1 AS store_name_1,
                    stores.name2 AS store_name_2,
                    employees.nik,
                    employees.name AS employee_name,
                    sell_ins.date,
                    products.name AS product_name,
                    categories.name AS category,
                    detail_ins.qty AS actual_qty,
                    measurement_units.name AS measure_name,
                    (detail_ins.qty * measurement_units.size) AS qty,
                    IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND `sell_ins`.`date` >= `prices`.`rilis`
                             AND `prices`.`deleted_at` IS NULL
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                    , 0) AS unit_price,
                    IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND `sell_ins`.`date` >= `prices`.`rilis`
                             AND `prices`.`deleted_at` IS NULL
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                    , 0)
                    *
                    (detail_ins.qty * measurement_units.size)
                    *
                    (CASE WHEN
                     (IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `detail_ins`.id_product
                             AND `targets`.id_store = `sell_ins`.`id_store`
                             AND `targets`.`id_employee` = `sell_ins`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`sell_ins`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`sell_ins`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                      , 0) = 0)
                      THEN 0
                      ELSE 1
                      END
                     ) AS `value`,
                     IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND `sell_ins`.`date` >= `prices`.`rilis`
                             AND `prices`.`deleted_at` IS NULL
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                    , 0)
                    *
                    (detail_ins.qty * measurement_units.size)
                    *
                    (CASE WHEN
                     (IFNULL(
                            (
                             SELECT `targets`.`quantity` FROM `targets` 
                             WHERE `targets`.id_product = `detail_ins`.id_product
                             AND `targets`.id_store = `sell_ins`.`id_store`
                             AND `targets`.`id_employee` = `sell_ins`.id_employee
                             AND MONTH(`targets`.`rilis`) = MONTH(`sell_ins`.date)
                             AND YEAR(`targets`.`rilis`) = YEAR(`sell_ins`.date)
                             AND `targets`.`deleted_at` IS NULL
                             LIMIT 1
                            )
                      , 0) = 0)
                      THEN 0
                      ELSE 1
                      END
                     )
                     *
                     (CASE WHEN
                     (IFNULL(
                            (
                             SELECT `product_fokuses`.`id` FROM `product_fokuses` 
                             WHERE `product_fokuses`.id_product = `detail_ins`.id_product
                             AND `product_fokuses`.id_area = `areas`.`id`
                             AND DATE(`product_fokuses`.`from`) <= DATE(`sell_ins`.date)
                             AND DATE(`product_fokuses`.`to`) >= DATE(`sell_ins`.date)
                             LIMIT 1
                            )
                      , 0) = 0)
                      THEN 0
                      ELSE 1
                      END
                     ) AS `value_pf`
                FROM `detail_ins`
                JOIN `sell_ins` ON detail_ins.id_sellin = sell_ins.id
                JOIN `stores` ON sell_ins.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                LEFT JOIN `measurement_units` ON detail_ins.id_measure = measurement_units.id
                JOIN `employees` ON sell_ins.id_employee = employees.id
                JOIN `products` ON detail_ins.id_product = products.id
                JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                JOIN `categories` ON sub_categories.id_category = categories.id
                 
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS sell_in_summary');
    }
}
