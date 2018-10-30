<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(TimeZoneTableSeeder::class);
        $this->call(SalesTierTableSeeder::class);
        $this->call(RegionTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(SubAreaTableSeeder::class);
        $this->call(PositionTableSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(DistributorTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(PasarTableSeeder::class);
        $this->call(StoreTableSeeder::class);
        $this->call(AgencyTableSeeder::class);
        $this->call(EmployeeTableSeeder::class);
        $this->call(EmployeeStoreTableSeeder::class);
        $this->call(PlaceTableSeeder::class);
        $this->call(BrandTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(SubCategoryTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(SkuUnitTableSeeder::class);
        $this->call(ProductUnitTableSeeder::class);
    }
}
