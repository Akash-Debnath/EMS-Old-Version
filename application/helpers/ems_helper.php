<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function convertDate($date,$format){
    return date_format(date_create($date),$format);
}
function dateDiff($stime,$etime)
{
    return date_diff(date_create($stime),date_create($etime))->format("%a days");
}
function paginationAttribute($data=array(),$total_row,$page,$req_dept)
{
    
    $data["total_rows"] = $total_row;
    $data["per_page"] = ROWS_PER_PAGE;
    // $data["offset"] = ($page - 1) * ROWS_PER_PAGE;
    $data['use_page_numbers'] = TRUE;
    $data['num_links'] = $total_row;
    $data['cur_tag_open'] = '&nbsp;<a class="current">';
    $data['cur_tag_close'] = '</a>';
    $data['next_link'] = 'Next';
    $data['prev_link'] = 'Prev';
    $data["uri_segment"] = 4;

    return $data;
}

function checkRamadanDate($compareDate,$ramadan_dates)
{
    $datesArr = array_map(function($date) use ($compareDate) {
        
        return range(strtotime($date['stime']), strtotime($date['etime']),86400);
        
    }, $ramadan_dates);
    
    foreach($datesArr as $dates){
        if(in_array(strtotime($compareDate), $dates)){
            return true;
        }
    }
    return false;
}