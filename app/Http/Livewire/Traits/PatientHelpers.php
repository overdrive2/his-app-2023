<?php

namespace App\Http\Livewire\Traits;

use App\Ipt;
use Illuminate\Support\Facades\DB;

trait PatientHelpers
{
    public function getIpt($an)
    {
        return Ipt::selectRaw("ROW_NUMBER() OVER(order by bedno.bed_order asc) as no,bedno.bed_order,ipt.an,patient.pname,patient.fname,patient.lname,ipt.hn,ipt.pttype,ipt.regdate,ipt.regtime,replace(ipt.prediag, chr(x'90'::int), '') as prediag,roomno.name as roomname,iptadm.roomno,iptadm.bedno,concat(patient.pname,patient.fname,' ',patient.lname)::varchar(120) as fullname,
		pttype.name as pttype_name,doctor.name as incharge_doctor_name,
		date_part('year',age(ipt.regdate,patient.birthday))::int4 as y,date_part('month',age(ipt.regdate,patient.birthday))::int4 as m,
		date_part('day',age(ipt.regdate,patient.birthday))::int4 as d,
        case when ipt.dchdate is null then '-' else thaidate(ipt.dchdate,'DD-MMM-YYYY') end as dchthdate,
		thaidate(ipt.regdate,'DD-MMM-YYYY') as thdate,lab_status.lab_count,lab_status.report_count,
		ict_ipt_room_sys_main.doctor_confirm, ict_ipt_room_sys_main.ict_ipt_room_sys_main_id, ict_ipt_room_sys_main.ict_ipt_room_sys_status_id,
		ict_ipt_room_sys_type.ict_ipt_room_sys_type_name,ward.name as ward_name,ward.ward,patient.bloodgrp, patient.drugallergy")
		->join('patient','patient.hn','ipt.hn')
		->leftjoin('ict_ipt_room_sys_main','ipt.an','=','ict_ipt_room_sys_main.an')
		->leftjoin('ict_ipt_room_sys_status','ict_ipt_room_sys_main.ict_ipt_room_sys_status_id','=','ict_ipt_room_sys_status.ict_ipt_room_sys_status_id')
		->leftjoin('ict_ipt_room_sys_type','ict_ipt_room_sys_main.ict_ipt_room_sys_type_id','=','ict_ipt_room_sys_type.ict_ipt_room_sys_type_id')
		->leftjoin('lab_status','lab_status.vn','=','ipt.an')
		->leftjoin('iptadm','iptadm.an','=','ipt.an')
		->leftjoin('roomno','roomno.roomno','=','iptadm.roomno')
		->leftjoin('ward','ipt.ward','=','ward.ward')
		->leftjoin('bedno',function($join){
			$join->on('bedno.roomno','=','roomno.roomno');
			$join->on('bedno.bedno','=','iptadm.bedno');
		})
		->leftjoin('ipt_pttype',function($join){
			$join->on('ipt_pttype.an','=','ipt.an')->where('ipt_pttype.pttype_number','=',1);
		})
		->leftjoin('pttype','pttype.pttype','=','ipt_pttype.pttype')
		->leftjoin('ipt_doctor_list',function($join){
			$join->on('ipt_doctor_list.an','=','ipt.an')
					->where('ipt_doctor_list.ipt_doctor_type_id','=',1)
					->where('ipt_doctor_list.active_doctor','=','Y');
		})
		->leftjoin('doctor','doctor.code','=','ipt_doctor_list.doctor')
        ->where('ipt.an', $an)->first();

    }

	public function WardToDepcode($ward)
	{
		return DB::connection('his')->table('ict_kskdepartment')->where('ward', $ward)->value('depcode');
	}

    public function getDepartmentStockId($depcode)
    {
        return DB::connection('his')->table('kskdepartment')->where('depcode', $depcode)->value('stock_department_id');
    }

    public function getStockWardId($ward)
    {
        return DB::table('ward_stocks')
        ->selectRaw('req_stock_department_id')
        ->where('ward',$ward)->first()->req_stock_department_id;
    }
}
