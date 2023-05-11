<?php

namespace App\Http\Livewire\Traits;

use App\Models\IctLog;

trait TransectionLogs
{
    public function storeLog($data)
    {
        return IctLog::create($data);
    }
}
