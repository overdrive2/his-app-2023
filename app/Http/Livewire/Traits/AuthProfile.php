<?php

namespace App\Http\Livewire\Traits;

trait AuthProfile
{
    public $user;

    public function initUser()
    {
        $user = auth()->user();

        $this->user = [
            'doctor_code' => $user->officer_doctor_code,
            'usercode' => $user->officer_login_name,
            'officer_id' => $user->officer_id,
            'position' => $user->position,
            'is_staff' => $user->is_staff,
            'is_doctor' => $user->is_doctor,
            'position_name' => $user->position_name,
            'officer_name' => $user->officer_name
        ];
    }
}
