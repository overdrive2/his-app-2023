<div x-data="{
    wardSelect: @entangle('ward_id'),
    an: @entangle('an'),
    ipd: @entangle('ipd'),
    modal: (val) => {
        $wire.an = val
        modal1.show();
    }
}"
x-init = "
    modal1 = new Modal($refs.modal1);
">
    <!--Verically centered scrollable modal-->
    <div class="mb-2">
        <div class="max-w-xs" wire:ignore>
            <!--Select default-->
            <x-input.select wire:model="ward_id" :label="__('หอผู้ป่วย')">
                <option>-- ทั้งหมด --</option>
                <option value="1">Ward1</option>
                <option value="2">Ward2</option>
                <option value="3">Ward3</option>
            </x-input.select>
            <div x-text="wardSelect"></div>
        </div>
    </div>
    <table class="min-w-full text-left text-sm font-light dark:text-gray-50">
        <thead class="border-b bg-white font-medium dark:border-gray-500 dark:bg-gray-600">
            <tr>
                <th scope="col" class="px-6 py-4">#</th>
                <th scope="col" class="px-6 py-4">Admit Date</th>
                <th scope="col" class="px-6 py-4">Time</th>
                <th scope="col" class="px-6 py-4">AN</th>
                <th scope="col" class="px-6 py-4">HN</th>
                <th scope="col" class="px-6 py-4">Patient name</th>
                <th scope="col" class="px-6 py-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $key => $row)
                <tr
                    class="border-b {{ $key % 2 == 0 ? 'bg-gray-100 dark:border-gray-500 dark:bg-gray-700' : 'bg-white dark:border-gray-500 dark:bg-gray-600' }}">
                    <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $key + 1 }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $row->reg_date_thai }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $row->regtime }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $row->an }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $row->hn }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $row->fullname }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <x-button.primary wire:click="new('{{ $row->an }}')">รับใหม่
                        </x-button.primary>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="whitespace-nowrap px-6 py-4 text-center font-medium">
                        -- Empty --
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($rows)
        <div class="py-4">
            {{ $rows->links() }}
        </div>
    @endif
    <x-dialog-modal wire:model.defer="showEditModal" maxWidth="2xl">
        <x-slot name="title">เพิ่ม/แก้ไข</x-slot>
        <x-slot name="content">
            @livewire('nurse-module.newcase-entry', ['an'=> $an], key('an'.$an.$showEditModal))
        </x-slot>
        <x-slot name="footer">
            <button wire:click="$toggle('showEditModal')" wire:loading.attr="disabled">
                {{ __('ยกเลิก') }}
            </button>

            <x-button.primary class="ml-2" wire:click="save" wire:loading.attr="disabled">
                {{ __('บันทึก') }}
            </x-button.primary>
        </x-slot>
    </x-dialog-modal>
</div>
