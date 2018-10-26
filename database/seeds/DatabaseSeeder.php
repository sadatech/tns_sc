<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(RegionTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(SubAreaTableSeeder::class);
        $this->call(PositionTableSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(DistributorTableSeeder::class);
        // $this->call(StoreClassificationTableSeeder::class);
        $this->call(StoreTableSeeder::class);
        // $this->call(PlaceTableSeeder::class);
        // $this->call(BrandTableSeeder::class);
        // $this->call(AgencyTableSeeder::class);
        // $this->call(CategoryTableSeeder::class);
        // $this->call(ProductTableSeeder::class);
        
        // $this->call(AgencyTableSeeder::class);
    }
}
