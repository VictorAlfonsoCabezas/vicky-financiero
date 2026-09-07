<?php

namespace App\Http\Livewire\CreditFolderAudits;

use App\Models\CreditFolderAudit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CreditFolderAuditsComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $headerId = '';
    public $detailId = '';
    public $source = '';
    public $event = '';
    public $action = '';
    public $userId = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;
    public $selectedCredit;
    public $creditHistory = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'headerId' => ['except' => ''],
        'detailId' => ['except' => ''],
        'source' => ['except' => ''],
        'event' => ['except' => ''],
        'action' => ['except' => ''],
        'userId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if (!in_array($propertyName, ['selectedCredit', 'creditHistory'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'headerId',
            'detailId',
            'source',
            'event',
            'action',
            'userId',
            'dateFrom',
            'dateTo',
        ]);
        $this->resetPage();
    }

    public function showCreditHistory($id)
    {
        $audit = CreditFolderAudit::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        $this->selectedCredit = [
            'header_id' => $audit->credit_folder_header_id,
            'code' => $audit->code_folder_header,
        ];

        $historyQuery = CreditFolderAudit::where('company_id', Auth::user()->company_id);

        if ($audit->credit_folder_header_id) {
            $historyQuery->where('credit_folder_header_id', $audit->credit_folder_header_id);
        } else {
            $historyQuery->whereNull('credit_folder_header_id')
                ->where('code_folder_header', $audit->code_folder_header);
        }

        $this->creditHistory = $historyQuery->orderByDesc('id')->get()->map(function ($item) {
            $oldValues = $item->old_values ?: [];
            $newValues = $item->new_values ?: [];
            $attributes = array_values(array_unique(array_merge(
                array_keys($oldValues),
                array_keys($newValues)
            )));
            $changes = [];

            foreach ($attributes as $attribute) {
                $changes[] = [
                    'attribute' => $attribute,
                    'old' => array_key_exists($attribute, $oldValues) ? $oldValues[$attribute] : null,
                    'new' => array_key_exists($attribute, $newValues) ? $newValues[$attribute] : null,
                    'has_old' => array_key_exists($attribute, $oldValues),
                    'has_new' => array_key_exists($attribute, $newValues),
                ];
            }

            return [
                'id' => $item->id,
                'detail_id' => $item->credit_folder_detail_id,
                'source' => $item->source,
                'event' => $item->event,
                'action' => $item->action,
                'description' => $item->description,
                'user_name' => $item->user_name,
                'ip_address' => $item->ip_address,
                'created_at' => optional($item->created_at)->format('d/m/Y H:i:s'),
                'changes' => $changes,
            ];
        })->all();
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;

        $query = CreditFolderAudit::where('company_id', $companyId)
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->where(function ($query) use ($search) {
                    $query->where('code_folder_header', 'like', '%' . $search . '%')
                        ->orWhere('user_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($this->headerId, function ($query) {
                $query->where('credit_folder_header_id', $this->headerId);
            })
            ->when($this->detailId, function ($query) {
                $query->where('credit_folder_detail_id', $this->detailId);
            })
            ->when($this->source, function ($query) {
                $query->where('source', $this->source);
            })
            ->when($this->event, function ($query) {
                $query->where('event', $this->event);
            })
            ->when($this->action, function ($query) {
                $query->where('action', $this->action);
            })
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        $perPage = in_array((int) $this->perPage, [15, 30, 50, 100], true)
            ? (int) $this->perPage
            : 15;

        $audits = $query
            ->selectRaw('MAX(id) as id')
            ->selectRaw('credit_folder_header_id, code_folder_header')
            ->selectRaw('COUNT(*) as events_count')
            ->selectRaw('MAX(created_at) as last_activity')
            ->groupBy('credit_folder_header_id', 'code_folder_header')
            ->orderByDesc('last_activity')
            ->paginate($perPage);

        return view(
            'livewire.credit-folder-audits.credit-folder-audits-component',
            compact('audits')
        );
    }
}
