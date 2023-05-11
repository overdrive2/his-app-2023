<?php

namespace App\Http\Livewire\Traits;

use App\Http\Livewire\DataTable\WithCachedRows;
use App\Models\DrugItem;
use App\Models\IctDoctorMedFusion;
use App\Models\IctDoctorOrder;
use App\Models\IctNondrug;
use App\Models\IptOrderNo;
use App\Models\NonDrugItem;
use App\Models\OpiFrequency;
use App\Models\OpiTimeCode;
use App\Models\OpiUsageCode;
use App\Models\StockDepartment;
use Illuminate\Support\Facades\DB;

trait LamedHelpers
{
    use WithCachedRows;

    public $search_icode;
    public $search_usage_code = '';
    public $search_frequency_code = '';
    public $search_time_code = '';
    public $showDrug = false;

    public $search_mode = [
        'drug'    => true,
        'nondrug' => false,
    ];

    public function usageLabel($med)
    {
        // L, I
        $str = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $freq = '';

        if($med && $med->fusion == 'P'){
            $med = IctDoctorMedFusion::where('master_id', $med->id)->first();
            $str .= ($med->usage_code_name ? ' '.$med->usage_code_name:'').($med->dose_text ? ' '.$med->dose_text.($med->unit_code_name != '' ? ' '.$med->unit_code_name.'/'.$med->unit_rate : ''):'');
            $freq = ($med->freq_code ? $med->freq_code_name : '');
        }
        else if($med && $med->med_type == 'L')
        {
            if($med->dose > 0)
                $str .= ($med->dose-(int)$med->dose!=0 ? ' '.(float)number_format($med->dose, 2) : ' '.number_format($med->dose, 0)).($med->usage_unit_code ? ' '.$med->usage_unit_name : ''); 
            $str .= ($med->usage_code_name ? ' '.$med->usage_code_name: '').($med->dose_text ? ' '.$med->dose_text.($med->unit_per_rate != '' ? ' '.$med->unit_perrate_name.'/'.$med->unit_rate : ''):''); // ปริมาณ
            $freq = ($med->frequency_code ? $med->freq_code_name : '');
        }
        else if($med) {
            $dose = '';
            if($med->dose > 0 && !$med->hide_dose)
                $dose = ($med->dose-(int)$med->dose!=0 ? (float)number_format($med->dose, 2) : number_format($med->dose, 0));    
            $unit = $med->usage_unit_code ? $med->usage_unit_name : '';//.($med->unit_rate ? '/'.$med->unit_rate : '');
            $usage = $med->usage_code_name;
            $str .= ($dose?' '.$dose.(($unit && $unit != '.')?' '.$unit:''):'').($usage ? ' '.$usage:'');
            $freq = ($med->frequency_code ? $med->freq_code_name : '');
        }
        return $str.($freq ? ' '.$freq:'').($med ? ($med->time_code ? ' '.$med->time_code_name : '').($med->note?' '.$med->note:'') : '');
    }

    public function usageLabelPrint($med)
    {
        // L, I
        $str = '';

        if($med && $med->fusion == 'P'){
            $med = IctDoctorMedFusion::where('master_id', $med->id)->first();
            $str .= ($med->dose_text ? ' '.$med->dose_text.($med->unit_code_name != '' ? ' '.$med->unit_code_name.'/'.$med->unit_rate : ''):'')
            .' '.($med->usage_code_name ? ' '.$med->usage_code_name:'').' '.$med->note;
        }
        else if($med && $med->dose > 0) {
            $dose = ($med->dose-(int)$med->dose!=0 ? (float)number_format($med->dose, 2) : number_format($med->dose, 0));
            $unit = strtolower($med->usage_unit_code);//.($med->unit_rate ? '/'.$med->unit_rate : '');
            $usage = $med->usage_code_name;
            $freq = $med->freq_code_name;
            $str = ($dose?' '.$dose:'').(($unit && $unit != '.')?' '.$unit:'').($usage ? ' '.$usage:'').($freq?' '.$freq:'');
            
        }

        return $str;
    }

    public function medLabel($med)
    {

        $str = '<div>';
        if($med){
            if($med->fusion == 'N'){
                $str .= '<input value="'.$med->id.'" type="checkbox" name="meds" x-model="selectedMeds" class="rounded mr-2 focus:ring-1 focus:ring-blue-400">';
            }

            $str .= (( $med->fusion == 'P')?$med->fusion_name:$med->name_for_doctor).'</div><div>&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        $str .= '<span>';

        $str .= $this->usageLabel($med).'</span></div>';

        return $str;
    }

    public function medLabelOld($med, $type)
    {
        $str = '';
        if($med) {
            $str .= '<div class="px-2"><span class="dark:text-white text-gray-800 font-medium">'.(($med->fusion == 'P')?$med->fusion_name:$med->name_for_doctor).'</span>';
            $str .= ($type == 'H') ? '<span class="text-gray-600 dark:text-white px-2">  <b class="px-2">'.number_format($med->qty, 0).'</b> '.$med->units . '</span>': '';
            $str .='</div>';
            $str .= '<div class="text-gray-600 dark:text-gray-50">&nbsp;&nbsp;&nbsp;&nbsp;';
            $str .= '<span class="text-gray-700 font-semibold dark:text-gray-50">';
            $str .= $this->usageLabel($med);
            $str .= '</span></div>';
            $str .= ($type == 'C') ? '</div><div class="flex-none px-2">' : '';
        }

        return $str;
    }

    public function medLabelOldPrint($med, $type)
    {
        $str = '';
        if(!$med)
            return $str;
        if($type == 'C')
        {
            $str .= '<div class="dark:text-white px-2">'.(($med->fusion == 'P')?$med->fusion_name:$med->name_for_doctor).'</div>';
            $str .= '<div class="text-gray-600 dark:text-gray-50">&nbsp;&nbsp;&nbsp;&nbsp;';
            $str .= '<span>';
            $str .= $this->usageLabelPrint($med);
            $str .= '</span></div></div><div class="flex-none px-2">';
        }
        else
        {
            $str .= '<div class="dark:text-white px-2">'.(($med->fusion == 'P')?$med->fusion_name:$med->name_for_doctor).'</div>';
            $str .= '<div class="text-gray-600 dark:text-gray-50 py-1">&nbsp;&nbsp;&nbsp;&nbsp;<span>';
            $str .= $this->usageLabelPrint($med);
            $str .= '</span></div>';
        }
        return $str;
    }

    public function getDrugItem($icode)
    {
        return DrugItem::selectRaw("drugitems.icode, drugitems.unitprice, drugitems.name, drugitems.units as unit,dispense_dose,
            usage_code, usage_unit_code, frequency_code, time_code, drugitems.strength, drugitems.drugusage, drugitems.drugnote, drugitems.therapeutic")
            ->leftJoin('s_drugitems', 's_drugitems.icode', 'drugitems.icode')
            ->where('drugitems.icode', $icode)
            ->where('drugitems.istatus', 'Y')
            ->first();
    }

    public function getNonDrugItem($icode)
    {
        return NonDrugItem::selectRaw("nondrugitems.icode, nondrugitems.price as unitprice, nondrugitems.name, nondrugitems.unit,'' as dispense_dose,
            '' as usage_code, '' as usage_unit_code, '' as frequency_code, '' as time_code, '' as strength, '' as drugusage, '' as drugnote, '' as therapeutic")
            ->where('nondrugitems.icode', $icode)
            ->where('nondrugitems.istatus', 'Y')
            ->first();
    }

    public function lamedHistory($icode)
    {
        $ssql = sprintf("select drugusage.drugusage as id,drugusage.name1,drugusage.name2,drugusage.name3,drugusage.code,drugusage.opi_usage_code,opi_dose,opi_frequency_code,opi_time_code
                from ict_doctor_usage
                inner join drugusage on drugusage.drugusage=ict_doctor_usage.drugusage
                where  icode='%s' and doctor='%s' order by cnt desc",$icode,  auth()->user()->officer_doctor_code);
        $data = DB::connection('his')->select($ssql);

        return collect($data);
    }

    public function getOpiusageRowsProperty()
    {
        return $this->cache(function () {
            $query = OpiUsageCode::selectRaw("usage_code,usage_line1")
                ->where('active_status', 'Y')
                ->orderByRaw('case when display_order is null then 1 else display_order end  asc')
                ->when($this->search_usage_code,
                    function($query, $kw){
                        return $query->whereRaw("(usage_code ilike '".$kw."%')or(usage_line1 like '%".$kw."%')");
                });
            return $query->get();
        });
    }

    public function getFreqRowsProperty()
    {
        return $this->cache(function () {
            $query = OpiFrequency::selectRaw("frequency_code, frequency_name")
                ->where('active_status', 'Y')
                ->orderByRaw('case when display_order is null then 1 else display_order end  asc')
                ->when($this->search_frequency_code,
                    function($query, $kw){
                        return $query->whereRaw("(frequency_code ilike '".$kw."%')or(frequency_name ilike '%".$kw."%')");
                });

            return $query->limit(20)->get();
        });

    }

    public function getTimeRowsProperty()
    {
        return $this->cache(function () {
            $query = OpiTimeCode::selectRaw("time_code, time_name")
                ->where('active_status', 'Y')
                ->where('doctor_use', 'Y')
                ->orderByRaw('case when display_order is null then 1 else display_order end  asc')
                ->when($this->search_time_code,
                    function($query, $kw){
                        return $query->whereRaw("(time_code ilike '".$kw."%')or(time_name like '%".$kw."%')");
                });

            return $query->orderBy('time_name', 'asc')->limit(20)->get();
        });

    }

    public function getSearchRowsQueryProperty()
    {
        $query = null;

        if($this->search_icode)
        {
            if($this->search_mode['drug'])
            {
                $query = DB::connection('his')
                ->table('drugitems')
                ->selectRaw("drugitems.icode, ict_drug_items.iname as name, units as unit, strength, 'Y'::char(1) as is_drug")
                ->join("ict_drug_items", "ict_drug_items.icode", "drugitems.icode")
                ->where('drugitems.istatus', 'Y')
                ->where('ict_drug_items.active', 'Y')
                ->where( function ($query) {
                    return $query->where('ict_drug_items.iname', 'ilike', '%'.$this->search_icode.'%');
                   /* $icode_keyword = DB::connection('his')->table('drugitems_search')->where('search_keyword', 'ilike', '%'.$this->search_icode.'%')->pluck('icode');
                    $query->whereIn('ict_drug_items.icode', $icode_keyword);
                    if(strlen($this->search_icode) >= 3) $query->orWhere('ict_drug_items.iname', 'ilike', '%'.$this->search_icode.'%');
                    if(strlen($this->search_icode) == 7) $query->orWhere('ict_drug_items.icode', '=', $this->search_icode);*/
                })->orderBy('ict_drug_items.iname', 'asc');
            }

            if($this->search_mode['nondrug'])
            {
                /*$nd = DB::connection('his')
                    ->table('nondrugitems')
                    ->selectRaw("icode, name, unit, ''::varchar(5) as strength, 'N'::char(1) as is_drug")
                    ->where('istatus', 'Y')
                    ->whereRaw("income not in('07', '08')")
                    ->where('name', 'ilike', '%'.$this->search_icode.'%')->orderBy('name', 'asc')->limit(50);*/
                $nd = IctNondrug::selectRaw("icode, doctor_name as name, unit, ''::varchar(5) as strength, 'N'::char(1) as is_drug")
                    ->where('active', true)
                    ->where('doctor_name', 'ilike', '%'.$this->search_icode.'%')
                    ->orderBy('doctor_name', 'asc')->limit(50);

                if($query) $query->union($nd);
                else $query= $nd;
            }
        }

        return $query;
    }

    public function getSearchRowsProperty()
    {
        return $this->cache(function () {
            $query =  $this->searchRowsQuery;
            if($query) return  $query->get();
            else return [];
        });
    }

    public function getStockdeptRowsProperty()
    {
        return StockDepartment::where('status_active', 'Y')->where('stock_authorize_type_id', '4')->orderBy('department_name', 'asc')->get();
    }

    public function medPrice($icode, $an){
        return collect(DB::connection('his')->select(sprintf("select get_price_ipd('%s','%s')::numeric(11,2) as price",$an, $icode)))->first()->price;
    }

    public function getQueueNumber($rxDate)
    {
        $que_number = IptOrderNo::select("day_queue_number")->where('rxdate',$rxDate)->whereRaw('day_queue_number is not null')->orderBy('day_queue_number','desc')->first();

        if($que_number)
            return $que_number->day_queue_number+1;
        else
            return 1;
    }

    public function getMedList($ids)
    {
        $orders = IctDoctorOrder::whereIn('id', $ids)->get();
        $result = '';
        foreach($orders as $key => $order)
        {
            $med = $order->medication;
            $result = $result . '<div>'.($key+1).'. '. (( $med->fusion == 'P' ) ? $med->fusion_name : $med->name_for_doctor).'</div>'; 
        }
        return $result;
    }
}
