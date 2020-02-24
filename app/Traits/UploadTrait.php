<?php
namespace App\Traits;

use Illuminate\Http\Request;
use App\JobTrace;
use App\Brand;
use App\Category;
use App\SubCategory;
use App\Product;
use App\Region;
use App\Area;
use App\SubArea;

trait UploadTrait
{

    public function convertTraceStatus($status, $rowId)
    {
        $btn = '';
        if ($status == 'PROCESSING') {
            $btn = 'primary';
        }else if ($status == 'DONE') {
            $btn = 'success';
        }else if ($status == 'FAILED') {
            $btn = 'danger';
        }
        return $label = "<label style='cursor: default;' class='btn btn-sm btn-".$btn."'>".$status."</label>";
        $label2 = "<label class='btn btn-sm btn-".$btn."'>".$status."</label>";
        return $this->getUploadedFiles($rowId, $label, $label2);
    }

    public function checkFileExist($id)
    {
        $upload     = JobTrace::whereId($id)->first();
        $starMark   = "<label style=';color: red;'>*</label>";

        if (!$upload) {
            return $starMark;
        }

        $path_ = $upload->file_path."/".$upload->file_name;
        return file_exists($path_) ? "<a target='_blank' href='".asset($path_)."' class='btn btn-outline-primary' ><i class='si si-cloud-download mr-2'></i></a>" : $starMark;
    }

    public function getFilterText($value)
    {
        $filterText = [];

        $request    = new Request($value);

        if(isset($request->filter_date)){
            $filterText[] = $request->filter_date;
        }
        if(isset($request->filter_start_date)){
            $filterText[] = isset($request->filter_end_date) ? $request->filter_start_date.' to '.$request->filter_end_date : 'start from '.$request->filter_start_date;
        }
        if(isset($request->filter_end_date)){
            $filterText[] = 'until:'.$request->filter_end_date;
        }
        if(isset($request->filter_month)){
            $filterText[] = $request->filter_month;
        }
        if(isset($request->filter_start_month)){
            $filterText[] = isset($request->filter_end_month) ? $request->filter_start_month.' to '.$request->filter_end_month : 'start from '.$request->filter_start_month;
        }
        if(isset($request->filter_end_month)){
            $filterText[] = 'until:'.$request->filter_end_month;
        }
        if(isset($request->filter_brand)){
            $filterText[] = implode(', ',Brand::whereIn('id',$request->filter_brand)->pluck('name')->toArray());
        }
        if(isset($request->filter_category)){
            $filterText[] = implode(', ',Category::whereIn('id',$request->filter_category)->pluck('name')->toArray());
        }
        if(isset($request->filter_sub_category)){
            $filterText[] = implode(', ',SubCategory::whereIn('id',$request->filter_sub_category)->pluck('name')->toArray());
        }
        if(isset($request->filter_product)){
            $filterText[] = implode(', ',Product::whereIn('id',$request->filter_product)->pluck('name')->toArray());
        }
        if(isset($request->filter_region)){
            $filterText[] = implode(', ',Region::whereIn('id',$request->filter_region)->pluck('name')->toArray());
        }
        if(isset($request->filter_area)){
            $filterText[] = implode(', ',Area::whereIn('id',$request->filter_area)->pluck('name')->toArray());
        }
        if(isset($request->filter_sub_area)){
            $filterText[] = implode(', ',SubArea::whereIn('id',$request->filter_sub_area)->pluck('name')->toArray());
        }
        if(isset($request->filter_check_all_region)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "region"));
        }
        if(isset($request->filter_check_all_area)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "area"));
        }
        if(isset($request->filter_check_all_sub_area)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "sub_area"));
        }
        if(isset($request->filter_check_all_brand)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "brand"));
        }
        if(isset($request->filter_check_all_category)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "category"));
        }
        if(isset($request->filter_check_all_sub_category)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "sub_category"));
        }
        if(isset($request->filter_check_all_product)){
            $filterText[] = "ALL ".ucwords(str_replace("_", " ", "product"));
        }

        return ( count($filterText) > 0 ? ' [' : '' ).implode(' ', $filterText).( count($filterText) > 0 ? ']' : '' );
    }

}