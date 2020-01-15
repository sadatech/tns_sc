<?php
namespace App\Traits;

use App\JobTrace;

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
        $label = "<label style='cursor: default;' class='btn btn-sm btn-".$btn."'>".$status."</label>";
        $label2 = "<label class='btn btn-sm btn-".$btn."'>".$status."</label>";
        return $this->getUploadedFiles($rowId, $label, $label2);
    }

    public function getUploadedFiles($id, $label, $label2)
    {
        $upload = JobTrace::whereId($id)->first();
        if (!$upload) {
            return $label;
        }
        $pathArray  = explode('\\public\\', $upload->file_path);
        $pathArray  = (count($pathArray) < 2) ? explode('/public/', $upload->file_path) : $pathArray;
        $starMark   = "<span style='float: right;color: red;'>*</span>";

        $path_ = @$pathArray[1]."/".($upload->file_name??"");
        return file_exists($path_) ? "<a target='_blank' href='".asset($path_)."' >".$starMark.$label2."</a>" : $label;
    }

}