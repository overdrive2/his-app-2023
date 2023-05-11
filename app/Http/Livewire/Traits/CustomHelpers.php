<?php

namespace App\Http\Livewire\Traits;

use App\Doctor;

trait CustomHelpers
{

    public function getDoctorName($code)
    {
        return Doctor::select('name')->where('code', $code)->value('name');
    }
}
