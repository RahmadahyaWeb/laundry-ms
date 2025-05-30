<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\WithDynamicPermission;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BaseComponent extends Component
{
    use WithDynamicPermission, WithPagination, WithoutUrlPagination, WithFileUploads;

    public $editing;
    public $modalTitle = 'Form';
    public $modalMethod = 'save';

    public function editRecord($id, array $config)
    {
        if (!$this->authorizePermission('edit')) {
            $this->showAlert(config('alert.permission'), 'danger', 'Error');
            return;
        }

        $model = $config['model']::query();

        if (isset($config['with'])) {
            $model->with($config['with']);
        }

        $data = $model->findOrFail($id);

        $this->editing = $config['map']($data);
        $this->toggleCrudModal();
    }

    public function executeSave(callable $callback)
    {
        if (!$this->authorizePermission('save')) {
            $this->showAlert(config('alert.permission'), 'danger', 'Error');
            return;
        }

        DB::beginTransaction();

        try {
            $callback();

            DB::commit();

            $this->showAlert(config('alert.save'));
            $this->toggleCrudModal();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->showAlert($e->getMessage(), 'danger', 'Error');
        }
    }

    public function executeDelete(callable $callback)
    {
        if (!$this->authorizePermission('delete')) {
            $this->showAlert(config('alert.permission'), 'danger', 'Error');
            return;
        }

        DB::beginTransaction();

        try {
            $callback();

            DB::commit();

            $this->showAlert(config('alert.delete'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            $this->showAlert($e->getMessage(), 'danger', 'Error');
        }
    }

    public function toggleCrudModal($modalMethod = 'save', $reset = false)
    {
        $this->modalMethod = $modalMethod;

        if ($reset) {
            $this->reset('editing');
        }

        $this->dispatch('toggle-modal', [
            'id' => '#crud-modal',
        ]);
    }

    public function showAlert($message = '', $type = 'success', $title = 'Sukses')
    {
        $this->dispatch('showAlert', type: $type, title: $title, message: $message);
    }

    public function format_rupiah($angka, $withPrefix = true, $decimal = 0)
    {
        if ($angka === null) return '-';

        $formatted = number_format($angka, $decimal, ',', '.');

        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}
