<?php
namespace App\Traits;

use App\Agency;
use App\SubArea;
use App\Area;
use App\Region;
use App\Pasar;
use App\Store;

trait FirstOrCreateTrait
{
	public function findStore($data, $subarea, $area, $region, $account, $channel, $timezonestore, $salestier)
    {
        $dataPasar = Store::whereRaw("TRIM(UPPER(name1)) = '". trim(strtoupper($data))."'");
        if ($dataPasar->count() == 0) {

			$dataSub['subarea_name']   	= $subarea;
			$dataSub['area_name']   	= $area;
			$dataSub['region_name']   	= $region;
			$id_subarea = $this->findSub($dataSub);

			$dataAcc['account']   	= $account;
			$dataAcc['channel']   	= $channel;
			$id_account = $this->findAcc($dataAcc);

			$id_sales = $this->findSales($salestier);

			$getTimezone = Timezone::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($timezonestore))."'")->first()->id;
            $pasar = Store::create([
                'name1'       	=> $data,
				'name2'       	=> "-",
				'address'		=> "-",
				'longitude'		=> "",
				'latitude'		=> "",
				'delivery'		=> "default delivery",
				'is_jawa'		=> "default is_jawa",
				'is_vito'		=> "default is_vito",
				'coverage'		=> "default coverage",
				'store_panel'	=> "default store_panel",
				'id_subarea'	=> $id_subarea,
				'id_account'	=> $id_account,
				'id_salestier'	=> $id_sales,
				'id_timezone'	=> ($getTimezone ? $getTimezone : 1)

            ]);
            if ($pasar) {
                $id_pasar = $pasar->id;
            }
        } else {
            $id_pasar = $dataPasar->first()->id;
        }
        return $id_pasar;
    }

    public function findSales($data)
    {
        $dataAgency = SalesTiers::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'")->get();
        if ($dataAgency != null) {
            $agency = SalesTiers::create([
              'name'        => $data
          ]);
            $id_sales = $agency->id;
        } else {
            $id_sales = $dataAgency->first()->id;
		}
		return $id_sales;
	}

	public function findAcc($data)
    {
        $dataArea = Account::where('name','like','%'.trim($data['account']).'%');
        if ($dataArea->count() == 0) {

			$dataAccount = $data;
			$id_channel = $this->findChannel($dataAccount);
            $area = Account::create([
              'name'        => $data['account'],
              'id_channel'   => $id_channel,
          ]);
            $id_account = $area->id;
        }else{
            $id_account = $dataArea->first()->id;
        }
        return $id_account;
    }

    public function findChannel($data)
    {
        $dataRegion = Channel::where('name','like','%'.trim($data['channel']).'%');
        if ($dataRegion->count() == 0) {

            $region = Channel::create([
              'name'        => $data['channel'],
          ]);
            $id_region = $region->id;
        }else{
            $id_region = $dataRegion->first()->id;
        }
        return $id_region;
    }

	public function findPasar($data, $subarea, $area, $region)
    {
        $dataPasar = Pasar::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'");
        if ($dataPasar->count() == 0) {

			$dataSub['subarea_name']   	= $subarea;
			$dataSub['area_name']   	= $area;
			$dataSub['region_name']   	= $region;
			$id_subarea 				= $this->findSub($dataSub);
            $pasar = Pasar::create([
                'name'       	=> $data,
				'address'       => "-",
				'latitude'		=> "",
				'longitude'		=> "",
				'id_subarea'	=> $id_subarea

            ]);
            if ($pasar) {
                $id_pasar = $pasar->id;
            }
        } else {
            $id_pasar = $dataPasar->first()->id;
        }
        return $id_pasar;
    }

    public function findAgen($data)
    {
        $dataAgency = Agency::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'");
        if ($dataAgency->count() == 0) {
            $agency = Agency::create([
              'name'        => $data
          ]);
            $id_agency = $agency->id;
        } else {
            $id_agency = $dataAgency->first()->id;
        }
        return $id_agency;
	}
	
	public function findSub($data)
    {
        $dataSub = SubArea::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['subarea_name']))."'");
        if ($dataSub->count() == 0) {

			$dataArea = $data;
			$id_area = $this->findArea($dataArea);
            $subarea = SubArea::create([
              'name'        => $data['subarea_name'],
              'id_area'     => $id_area
          ]);
            $id_subarea = $subarea->id;
        }else{
            $id_subarea = $dataSub->first()->id;
        }
        return $id_subarea;
    }

    public function findArea($data)
    {
        $dataArea = Area::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['area_name']))."'");
        if ($dataArea->count() == 0) {

			$dataRegion = $data;
			$id_region = $this->findRegion($dataRegion);
            $area = Area::create([
              'name'        => $data['area_name'],
              'id_region'   => $id_region,
          ]);
            $id_area = $area->id;
        }else{
            $id_area = $dataArea->first()->id;
        }
        return $id_area;
    }

    public function findRegion($data)
    {
        $dataRegion = Region::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['region_name']))."'");
        if ($dataRegion->count() == 0) {

            $region = Region::create([
              'name'        => $data['region_name'],
          ]);
            $id_region = $region->id;
        }else{
            $id_region = $dataRegion->first()->id;
        }
        return $id_region;
    }

}