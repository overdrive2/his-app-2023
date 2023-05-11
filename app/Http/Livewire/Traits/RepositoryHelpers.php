<?php

namespace App\Http\Livewire\Traits;

use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithGetSerial;
use App\Models\IctClinic;
use App\Models\IctDepartment;
use App\Models\IctOappCause;
use Illuminate\Support\Facades\DB;

trait RepositoryHelpers
{
    use WithCachedRows;

    public function getClinicRowsProperty()
    {
        return  $this->cache(function()
        {
            return  IctClinic::selectRaw("clinic_code as clinic, clinic_name as name")
            ->where('active', true)
            ->orderBy('clinic_name')
            ->get();
        });
    }

    public function getHisClinicRowsProperty()
    {
        return  $this->cache(function()
        {
            return  DB::connection('his')
                ->table('clinic')->select('clinic', 'name')
                ->where('active_status', 'Y')
                ->orderBy('name', 'asc')
                ->get();
        });
    }

    public function getDeptRowsProperty()
    {
        return  $this->cache(function()
        {
            return  IctDepartment::selectRaw('deptcode as depcode, deptname as department')
            ->where('active', true)
            ->orderBy('deptname')
            ->get();
        });
    }

    public function getHisDeptRowsProperty()
    {
        return  $this->cache(function()
        {
            return  DB::connection('his')
            ->table('kskdepartment')
            ->select('depcode', 'department')
            ->where('department_active', 'Y')
            ->where('hospital_department_id', 1)
            ->orderBy('department')
            ->get();
        });
    }    

    public function getCauseRowsProperty()
    {
        return  $this->cache(function()
        {
            return  IctOappCause::selectRaw('id, name_for_doctor as name')
            ->where('active', true)
            ->orderBy('name_for_doctor')
            ->get();
        });
    }

    public function getSpcltyRowsProperty()
    {
        return  $this->cache(function()
        {
            return  DB::connection('his')
            ->table('spclty')
            ->select('spclty', 'name')
            ->orderBy('name')
            ->get();
        });
    }

    public function getIncomeRowsProperty()
    {
        return  $this->cache(function()
        {
            return DB::connection('his')
            ->table('income')
            ->orderBy('income', 'asc')
            ->get();
        });
    }
}
