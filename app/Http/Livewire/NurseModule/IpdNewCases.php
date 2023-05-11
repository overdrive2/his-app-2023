<?php

namespace App\Http\Livewire\NurseModule;

use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\His\IpdNewCase;
use Livewire\Component;

class IpdNewCases extends Component
{
    use WithCachedRows, WithPerPagePagination;

    public $showEditModal = false;
    public $an;
    public $ward_id;
    public $ipd;

    public function new($an)
    {
        $this->an = $an;
        $this->showEditModal = true;
    }

    public function show()
    {
        $this->dispatchBrowserEvent('show-clients');
    }

    public function getRowsQueryProperty()
    {
        return IpdNewCase::query();
    }

    public function getRowsProperty()
    {
        return $this->cache( function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function updatedIpd($value)
    {
        //dd($value);
    }

    public function render()
    {
        return view('livewire.nurse-module.ipd-new-cases', [
            'rows' => $this->rows
        ]);
    }
}
