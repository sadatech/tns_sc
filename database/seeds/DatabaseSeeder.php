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
<<<<<<< HEAD
        $this->call(EmployeeTableSeeder::class);
        $this->call(EmployeeStoreTableSeeder::class);
=======
        // $this->call(EmployeeTableSeeder::class);//ini
        // $this->call(EmployeeStoreTableSeeder::class);
>>>>>>> ed450911fa1fd5b0cb037b160326e6470abe56d0
        $this->call(PlaceTableSeeder::class);
        $this->call(BrandTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(SubCategoryTableSeeder::class);
<<<<<<< HEAD
        $this->call(ProductTableSeeder::class);

        // $this->call(MeasurementUnitTableSeeder::class);
        // $this->call(ProductMeasureTableSeeder::class);

        $this->call(PriceTableSeeder::class);
        // $this->call(SalesTableSeeder::class);

=======
        // $this->call(ProductTableSeeder::class);//ini
        // $this->call(MeasurementUnitTableSeeder::class);
        // $this->call(ProductMeasureTableSeeder::class);

        $this->call(NewTestAchievementTableSeeder::class);
>>>>>>> ed450911fa1fd5b0cb037b160326e6470abe56d0
    }
}
