<?php

namespace App\Http\Livewire\Traits;

use App\Ipt;
use App\Models\IctBloodTemplate;
use App\Models\IctDoctorMedFusion;
use App\Models\IctDoctorOrder;
use App\Models\IctDoctorOrderBlood;
use App\Models\IctDoctorOrderLab;
use App\Models\IctDoctorOrderMaster;
use App\Models\IctDoctorOrderMedication;
use App\Models\IctDoctorOrderRad;
use App\Models\IctDoctorOrderTypes;
use App\Models\IctDoctorProgressNote;
use App\Models\IctDoctorTemplateDetail;
use App\Models\IctIpt;
use App\Models\IctLabItem;
use App\Models\IctMedTemplate;
use App\Models\IctXraySet;
use App\Models\MedplanIpd;

trait DoctorOrderHelpers
{
    public function makeBlankPreDoctorOrderMaster()
    {
        return IctDoctorOrderMaster::make([
            'hn'          => $this->hn,
            'order_date'  => $this->getCurrentDate(),
            'order_time'  => $this->getCurrentTime(),
            'admit_date'  => $this->current_date,
            'order_type'  => 'P',
            'doctor_code' => $this->user['doctor_code'],
            'created_by'  => $this->user['officer_id'],
            'updated_by'  => $this->user['officer_id'],
            'checked'     => false,
            'saved'       => false,
        ]);
    }

    public function doMedCheck($an)
    {
        $master_ids = IctDoctorOrderMaster::where('an', $an)->where('saved', true)
            ->where('checked', false)
            ->where('confirm', true)
            ->pluck('id');

        $count_order = IctDoctorOrder::whereIn('order_masters_id', $master_ids)->count();

        if($count_order > 0)
            $status = 'W';
        else 
            $status = IctDoctorOrderMaster::where('an', $an)->where('checked', true)->count() > 0 ? 'Y' : null;

        if(IctIpt::where('an', $this->an)->count() == 0)
            IctIpt::make([
                'an' => $this->an,
                'staff' =>  $this->user['usercode'],
                'is_do_med' => $status,
            ])->save();
        else 
            IctIpt::where('an', $an)->update(['is_do_med' => $status]);
    }
    
    public function makeBlankDischargeDoctorOrderMaster()
    {
        $ipt = Ipt::selectRaw("ipt.hn, ipt.an, ipt.ward, bedno.bedno, ipt.regdate")
            ->leftJoin('iptadm', 'iptadm.an', 'ipt.an')
            ->leftJoin('bedno', 'bedno.bedno', 'iptadm.bedno')
            ->where('ipt.an', $this->an)
            ->where('ipt.confirm_discharge', 'N')
            ->first();

        return IctDoctorOrderMaster::make([
            'an'          => $ipt->an ?? null,
            'hn'          => $ipt->hn ?? null,
            'order_date'  => $this->getCurrentDate(),
            'order_time'  => $this->getCurrentTime(),
            'admit_date'  => $ipt->regdate,
            'ward'        => $ipt->ward,
            'order_type'  => 'D',
            'bedno'       => $ipt->bedno ?? null,
            'doctor_code' => $this->user['doctor_code'],
            'created_by'  => $this->user['officer_id'],
            'updated_by'  => $this->user['officer_id'],
            'checked'     => false,
            'saved'       => false,
            'confirm'     => true,
            'no_followup' => false,
        ]);
    }

    public function offMedplan($id, $offdate, $offtime) {
        return MedplanIpd::where('med_plan_number', $id)
                ->update([
                    'offdate' => $offdate,
                    'off_time' => $offtime,
                    'off_staff' => $this->user['usercode'],
                ]);
    }

    public function makeBlankDoctorOrderMaster()
    {
        if($this->user['is_staff']){
            $order_type = 'I';
            $confirm = true;
        }
        else {
            $order_type = 'N';
            $confirm = false;
        }   
        $ipt = Ipt::selectRaw("ipt.hn, ipt.an, ipt.ward, bedno.bedno")
            ->leftJoin('iptadm', 'iptadm.an', 'ipt.an')
            ->leftJoin('bedno', 'bedno.bedno', 'iptadm.bedno')
            ->where('ipt.an', $this->an)
            ->where('ipt.confirm_discharge', 'N')
            ->first();

        return IctDoctorOrderMaster::make([
            'an'          => $ipt->an ?? null,
            'hn'          => $ipt->hn ?? null,
            'order_date'  => $this->getCurrentDate(),
            'order_time'  => $this->getCurrentTime(),
            'ward'        => $ipt->ward ?? null,
            'order_type'  => $order_type,
            'doctor_code' => $this->user['doctor_code'],
            'created_by'  => $this->user['officer_id'],
            'updated_by'  => $this->user['officer_id'],
            'checked'     => false,
            'bedno'       => $ipt->bedno ?? null,
            'saved'       => false,
            'confirm'     => $confirm,
        ]);
    }

    public function makeMedicationOrder($order, $order_id)
    {
        $med = IctMedTemplate::where('ict_template_detail_id', $order->id)->first();

        return IctDoctorOrderMedication::make([
            'icode' => $med->icode,
            'iname' => $med->name_for_doctor,
            'itype' => $med->itype,
            'drugusage' => $med->drugusage,
            'usage_code' => $med->usage_code,
            'dose' => $med->dose,
            'frequency_code' => $med->frequency_code,
            'usage_unit_code' => $med->usage_unit_code,
            'time_code' => $med->time_code,
            'usage_line1' => $med->usage_line1,
            'usage_line2' => $med->usage_line2,
            'usage_line3' => $med->usage_line3,
            'ict_doctor_orders_id' => $order_id,
            'usage_shortlist' => $med->usage_shortlist,
            'drug_hint_text' => $med->drug_hint_text,
            'unit_rate' => $med->unit_rate,
            'dose_text' => $med->dose_text,
            'unit_per_rate' => $med->unit_per_rate,
            'note' => $med->note,
            'doctor_code' => $this->user['doctor_code'],
            'created_by' => $this->user['officer_id'],
            'updated_by' => $this->user['officer_id']
        ]);
    }

    public function putOrder($order)
    {
        $neworder = null;

        $type = IctDoctorOrderTypes::where('id', $order->type_id)->first();
        $neworder = $this->makeGeneralOrder($order);
        $neworder->ict_doctor_template_details_id = $order->id;
        if(!$neworder->save())
            return $this->dispatchBrowserEvent('swal:alert', [
                'title' => 'ดำเนินการไม่สำเร็จ',
                'text' => 'รายการถูกยืนยัน, ไม่สามารถลบได้ !',
                'icon' => 'warning',
            ]);

        if($type->component == 'doctor-lab-orders')
        {
            $laborder = $this->makeLabOrder($order, $neworder->id);
            $laborder->save();
        }
        else if($type->component == 'doctor-rad-orders')
        {
            $radorder = $this->makeRadOrder($order, $neworder->id);
            $radorder->save();
        }
        else if($type->component == 'medication-order')
        {
            $medorder = $this->makeMedicationOrder($order, $neworder->id);
            $medorder->save();
        }
        else if($type->component == 'doctor-blood-orders')
        {
            $bldorder = $this->makeBloodOrder($order, $neworder->id);
            $bldorder->save();
        }
    }

    public function setTemplate($id)
    {
        $orders = IctDoctorTemplateDetail::where('template_id', $id)->orderByRaw('case when display_order=0 then id else display_order end asc')->get();

        if(!$this->editing->id)
        {
            $this->validate();
            $this->editing->save();
        }

        foreach($orders as $order)
        {
            $this->putOrder($order);
        }
        $this->showTemplateOrderModal = false;
        $this->emit('refresh:child');
    }

    public function makeGeneralOrder($order)
    {
        return IctDoctorOrder::make([
            'ict_doctor_order_type_id'    => $order->type_id,
            'order_type'                  => $this->type,
            'other'                       => $order->other,
            'multi_subtype_id'            => $order->multi_subtype_id,
            'created_by'                  => $this->user['officer_id'],
            'updated_by'                  => $this->user['officer_id'],
            'order_masters_id'            => $this->editing->id,
            'ict_doctor_order_subtype_id' => $order->order_item_id
        ]);
    }

    public function makeLabOrder($order, $order_id)
    {
        return IctDoctorOrderLab::make([
            'ict_lab_items_id'     => $order->order_item_id,
            'ict_doctor_orders_id' => $order_id,
            'lab_item_name'        => IctLabItem::where('id', $order->order_item_id)->value('lab_name')
        ]);
    }

    public function makeRadOrder($order, $order_id)
    {
        return IctDoctorOrderRad::make([
            'xray_set_id'          => $order->order_item_id,
            'xray_set_name'        => IctXraySet::where('xray_set_id', $order->order_item_id)->value('xray_set_name'),
            'ict_doctor_orders_id' => $order_id
        ]);
    }

    public function makeBloodOrder($order, $order_id)
    {
        $blood = IctBloodTemplate::where('ict_template_detail_id', $order->id)->first();

        return IctDoctorOrderBlood::make([
            'blb_blood_items_id' => $blood->blb_blood_items_id, 
            'blb_blood_items_name_for_doctor' => $blood->blb_blood_items_name_for_doctor, 
            'blb_request_list_qty' => $blood->blb_request_list_qty, 
            'blb_request_type_id' => $blood->blb_request_type_id, 
            'ict_doctor_orders_id' => $order_id,
            'cc' => $blood->cc,
            'operative_date' => $blood->operative_date,
        ]);
    }

    public function deleteMaster($id)
    {
      
        $orderIds = IctDoctorOrder::where('order_masters_id', $id)->pluck('id');

        if(count($orderIds) > 0)
        {
            IctDoctorOrder::whereIn('id', $orderIds)->delete();
            IctDoctorOrderLab::whereIn('ict_doctor_orders_id', $orderIds)->delete();
            IctDoctorOrderRad::whereIn('ict_doctor_orders_id', $orderIds)->delete();
            IctDoctorOrderBlood::whereIn('ict_doctor_orders_id', $orderIds)->delete();
            IctDoctorMedFusion::whereIn('master_id', 
                IctDoctorOrderMedication::whereIn('ict_doctor_orders_id', $orderIds)
                    ->where('fusion', 'P')
                    ->pluck('id')
            )->delete();
            IctDoctorOrderMedication::whereIn('ict_doctor_orders_id', $orderIds)->delete();
        }

        IctDoctorProgressNote::where('order_masters_id', $id)->delete();

        return IctDoctorOrderMaster::where('id', $id)->delete();    
    }

    public function UndoOffMed($orderIds)
    {
        foreach($orderIds as $oId)
        {
            $order = IctDoctorOrder::where('id', $oId)->first();
            if($order->fusion == 'P') {
                $meds = IctDoctorOrderMedication::whereIn('id', json_decode(IctDoctorMedFusion::where('master_id', $order->medication->id)->value('med_order_id')))
                    ->whereNotIn('fusion', ['P'])->get();

                foreach($meds as $med) {
                    IctDoctorOrder::where('id', $med->ict_doctor_orders_id)->update([
                        'closed' => false,
                        'off_by' => $this->user['officer_id'],
                        'off_date' => null,
                        'off_time' => null,
                    ]);
                    
                    //if($med->med_plan_number)
                    //    $this->offMedplan($med->med_plan_number, $order->off_date, $order->off_time);
                }
            }
            //else if($order->medication->med_plan_number) 
            //    $this->offMedplan($order->medication->med_plan_number, $order->off_date, $this->off_time);
                
            $order->off_date = null;
            $order->off_time = null;    
            $order->closed = false;
            $order->save();    
        }
    }
}
