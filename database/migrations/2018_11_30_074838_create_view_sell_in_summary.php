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
                    sell_ins.week,
                    regions.name as region,
                    areas.name as area,
                    sub_areas.name as sub_area,
                    accounts.name as account,
                    channels.name as channel,
                    stores.name1 as store_name_1,
                    stores.name2 as store_name_2,
                    employees.nik,
                    employees.name as employee_name,
                    sell_ins.date,
                    products.name as product_name,
                    categories.name as category,
                    detail_ins.qty,
                    IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND `sell_ins`.`date` >= `prices`.`rilis`
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                    , 0) AS unit_price,
                    IFNULL(
                            (
                             SELECT `prices`.price FROM `prices` 
                             WHERE `prices`.id_product = `products`.id
                             AND `sell_ins`.`date` >= `prices`.`rilis`
                             ORDER BY `prices`.`rilis` DESC
                             LIMIT 1
                            )
                    , 0) * detail_ins.qty AS `value`                   
                FROM `detail_ins`
                JOIN `sell_ins` ON detail_ins.id_sellin = sell_ins.id
                JOIN `stores` ON sell_ins.id_store = stores.id
                LEFT JOIN `sub_areas` ON stores.id_subarea = sub_areas.id
                LEFT JOIN `areas` ON sub_areas.id_area = areas.id
                LEFT JOIN `regions` ON areas.id_region = regions.id
                LEFT JOIN `accounts` ON stores.id_account = accounts.id
                LEFT JOIN `channels` ON accounts.id_channel = channels.id
                JOIN `employees` ON sell_ins.id_employee = employees.id
                JOIN `products` ON detail_ins.id_product = products.id
                JOIN `sub_categories` ON products.id_subcategory = sub_categories.id
                JOIN `categories` ON sub_categories.id_category = categories.id
                    -- invt.warehouse_id,

                    # TOTAL MATERIAL QUANTITY TRANSACTION
                    -- IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) AS total_transaction_quantity,

                    # TOTAL MATERIAL QUANTITY RESERVED
                    -- IFNULL((SELECT SUM(ir.quantity) FROM inventory_reserved ir WHERE ir.inventory_id = invt.id), 0) AS total_reserved_quantity,

                    # MATERIAL QUANTITY SUMMARY
                    -- IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) - IFNULL((SELECT SUM(ir.quantity) FROM inventory_reserved ir WHERE ir.inventory_id = invt.id), 0) AS quantity,

                    # AMOUNT TRANSACTION
                    -- IFNULL((SELECT SUM((it.price * it.quantity) + it.ekp_price) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) AS transaction_amount,

                    # AMOUNT TRANSACTION / TOTAL MATERIAL QUANTITY TRANSACTION => PRICE AVERAGE
                    -- IFNULL((SELECT SUM((it.price * it.quantity) + it.ekp_price) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) / IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) AS price_avg,

                    # AMOUNT TRANSACTION - ((TOTAL MATERIAL QUANTITY RESERVED * PRICE AVERAGE) => AMOUNT RESERVED) => TOTAL AMOUNT AVAILABLE
                    -- IFNULL((SELECT SUM((it.price * it.quantity) + it.ekp_price) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) - (IFNULL((SELECT SUM(ir.quantity) FROM inventory_reserved ir WHERE ir.inventory_id = invt.id), 0) * (IFNULL((SELECT SUM((it.price * it.quantity) + it.ekp_price) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) / IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0))) AS amount_after_reserved,

                    # MATERIAL QUANTITY SUMMARY * PRICE AVERAGE => AMOUNT SUMMARY
                    -- (IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) - IFNULL((SELECT SUM(ir.quantity) FROM inventory_reserved ir WHERE ir.inventory_id = invt.id), 0)) * (IFNULL((SELECT SUM((it.price * it.quantity) + it.ekp_price) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0) / IFNULL((SELECT SUM(it.quantity) FROM inventory_transaction it WHERE it.inventory_id = invt.id), 0)) AS amount,
                    -- invt.max_quantity,
                    -- invt.min_quantity                
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
