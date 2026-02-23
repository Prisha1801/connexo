<?php

namespace Modules\LeadManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Group;
use Modules\LeadManager\Models\Lead;
use Modules\LeadManager\Models\LeadSource;
use Modules\LeadManager\Models\LeadStage;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->route('admin.companies.index')
                ->with('error', __('Please select a company first.'));
        }

        $query = Lead::with(['contact', 'user'])
            ->where('company_id', $companyId)
            ->orderByDesc('created_at');

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }
        if ($request->filled('assigned')) {
            if ($request->assigned === 'unassigned') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $request->assigned);
            }
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('contact', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $agents = User::where('company_id', $companyId)->get();
        $sources = LeadSource::optionsForCompany($companyId);

        $base = Lead::where('company_id', $companyId);
        $stats = [
            'total' => (clone $base)->count(),
            'won' => (clone $base)->where('stage', Lead::STAGE_WON)->count(),
            'active' => (clone $base)->whereNotIn('stage', [Lead::STAGE_WON, Lead::STAGE_LOST])->count(),
            'lost' => (clone $base)->where('stage', Lead::STAGE_LOST)->count(),
        ];

        $leads = $query->paginate(15)->withQueryString();
        $stages = LeadStage::forCompany($companyId);
        return view('lead-manager::index', compact('leads', 'agents', 'stats', 'sources', 'stages'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->route('admin.companies.index')
                ->with('error', __('Please select a company first.'));
        }

        $contacts = Contact::where('company_id', $companyId)->orderBy('name')->get();
        $agents = User::where('company_id', $companyId)->get();
        $groups = Group::where('company_id', $companyId)->orderBy('name')->get();
        $sources = LeadSource::optionsForCompany($companyId);
        $stages = LeadStage::forCompany($companyId);

        return view('lead-manager::create', compact('contacts', 'agents', 'groups', 'sources', 'stages'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->back()->with('error', __('Please select a company first.'));
        }

        $validStages = array_keys(LeadStage::forCompany($companyId));
        $rules = [
            'source' => ['required', 'string', 'max:100'],
            'stage' => ['required', Rule::in($validStages)],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:groups,id'],
        ];

        if ($request->contact_id) {
            $rules['contact_id'][] = function ($attr, $val, $fail) use ($companyId) {
                $c = Contact::withoutGlobalScopes()->where('id', $val)->where('company_id', $companyId)->first();
                if (!$c) $fail(__('Invalid contact.'));
            };
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:50'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        $source = $data['source'];
        $knownKeys = array_keys(Lead::sources());
        if (!in_array($source, $knownKeys)) {
            $source = LeadSource::findOrCreateCustom($companyId, $source);
        }

        DB::beginTransaction();
        try {
            if (!empty($data['contact_id'])) {
                $contact = Contact::withoutGlobalScopes()->where('id', $data['contact_id'])->where('company_id', $companyId)->firstOrFail();
            } else {
                $contact = Contact::withoutGlobalScopes()->firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'phone' => $data['phone'],
                    ],
                    [
                        'name' => $data['name'],
                        'email' => $data['email'] ?? null,
                    ]
                );
            }

            if (!empty($data['groups'])) {
                $contact->groups()->sync(array_filter($data['groups']));
            }

            $lead = Lead::withoutGlobalScopes()->create([
                'company_id' => $companyId,
                'contact_id' => $contact->id,
                'source' => $source,
                'stage' => $data['stage'],
                'user_id' => $data['user_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'tags' => $data['tags'] ?? null,
                'location' => $data['location'] ?? null,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('lead-manager.index')->with('success', __('Lead created successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        $lead->load(['contact.groups', 'user']);
        if ($lead->company_id != $companyId) {
            abort(404);
        }

        $base = Lead::where('company_id', $companyId)->orderBy('id');
        $prevLead = (clone $base)->where('id', '<', $lead->id)->latest('id')->first();
        $nextLead = (clone $base)->where('id', '>', $lead->id)->first();
        $agents = User::where('company_id', $companyId)->get();
        $stages = LeadStage::forCompany($companyId);
        $groups = \Modules\Contacts\Models\Group::where('company_id', $companyId)->orderBy('name')->get();

        return view('lead-manager::show', compact('lead', 'prevLead', 'nextLead', 'agents', 'stages', 'groups'));
    }

    public function storeNote(Request $request, Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if ($lead->company_id != $companyId) {
            abort(404);
        }
        $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $sep = "\n---\n";
        $timestamp = now()->format('Y-m-d H:i');
        $newNote = "[{$timestamp}] " . trim($request->body);
        $lead->notes = $lead->notes ? $lead->notes . $sep . $newNote : $newNote;
        $lead->save();
        return redirect()->route('lead-manager.show', $lead)->with('success', __('Note added.'));
    }

    public function edit(Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        $lead->load('contact', 'user');
        if ($lead->company_id != $companyId) {
            abort(404);
        }

        $agents = User::where('company_id', $companyId)->get();
        $stages = LeadStage::forCompany($companyId);

        return view('lead-manager::edit', compact('lead', 'agents', 'stages'));
    }

    public function update(Request $request, Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if ($lead->company_id != $companyId) {
            abort(404);
        }

        $validStages = array_keys(LeadStage::forCompany($companyId));
        $data = $request->validate([
            'stage' => ['sometimes', 'required', Rule::in($validStages)],
            'qualified' => ['nullable', 'boolean'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:500'],
            'next_follow_up_at' => ['nullable', 'date'],
            'schedule_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if (!empty($data['schedule_note'])) {
            $sep = "\n---\n";
            $newLine = '[' . now()->format('Y-m-d H:i') . '] Follow-up: ' . trim($data['schedule_note']);
            $lead->notes = $lead->notes ? $lead->notes . $sep . $newLine : $newLine;
            unset($data['schedule_note']);
        }
        $lead->update($data);

        if (!empty($request->schedule_note) || $request->has('schedule_note')) {
            return redirect()->route('lead-manager.show', $lead)->with('success', __('Follow-up scheduled.'));
        }
        return redirect()->route('lead-manager.index')->with('success', __('Lead updated successfully.'));
    }

    public function kanbanData(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Lead::with(['contact', 'user'])
            ->where('company_id', $companyId)
            ->orderByDesc('created_at');

        if ($request->filled('source')) $query->where('source', $request->source);
        if ($request->filled('assigned')) {
            if ($request->assigned === 'unassigned') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $request->assigned);
            }
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('contact', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);

        $allLeads = $query->get();
        $leadsByStage = $allLeads->groupBy('stage');
        $stages = LeadStage::allForCompany($companyId);
        $leadStages = $leadsByStage->keys()->diff($stages->pluck('name'));
        foreach ($leadStages as $name) {
            $stages->push((object)['name' => $name, 'sort_order' => 999]);
        }
        $stages = $stages->sortBy('sort_order')->values();

        $data = [
            'stages' => $stages->map(function ($s) { return $s->name; })->toArray(),
            'leadsByStage' => $leadsByStage->map(function ($leads) {
                return $leads->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'stage' => $lead->stage,
                        'name' => $lead->contact?->name ?? '—',
                        'phone' => $lead->contact?->phone ?? '—',
                        'source_label' => $lead->source_label,
                        'assigned_to' => $lead->user?->name,
                        'show_url' => route('lead-manager.show', $lead),
                        'edit_url' => route('lead-manager.edit', $lead),
                        'destroy_url' => route('lead-manager.destroy', $lead),
                    ];
                })->values()->toArray();
            })->toArray(),
            'csrf_token' => csrf_token(),
            'update_stage_url' => url('lead-manager'),
        ];

        return response()->json($data);
    }

    public function kanban(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->route('admin.companies.index')->with('error', __('Please select a company first.'));
        }

        $query = Lead::with(['contact', 'user'])
            ->where('company_id', $companyId)
            ->orderByDesc('created_at');

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('assigned')) {
            if ($request->assigned === 'unassigned') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $request->assigned);
            }
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('contact', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $allLeads = $query->get();
        $leadsByStage = $allLeads->groupBy('stage');
        $stages = LeadStage::allForCompany($companyId);
        // Include stages that have leads but may not be in lead_stages (e.g. legacy data)
        $leadStages = $leadsByStage->keys()->diff($stages->pluck('name'));
        foreach ($leadStages as $name) {
            $stages->push((object)['name' => $name, 'sort_order' => 999]);
        }
        $stages = $stages->sortBy('sort_order')->values();
        $agents = User::where('company_id', $companyId)->get();
        $sources = LeadSource::optionsForCompany($companyId);

        $base = Lead::where('company_id', $companyId);
        $stats = [
            'total' => (clone $base)->count(),
            'won' => (clone $base)->where('stage', Lead::STAGE_WON)->count(),
            'active' => (clone $base)->whereNotIn('stage', [Lead::STAGE_WON, Lead::STAGE_LOST])->count(),
            'lost' => (clone $base)->where('stage', Lead::STAGE_LOST)->count(),
        ];

        return view('lead-manager::kanban', compact('leadsByStage', 'stages', 'agents', 'stats', 'sources'));
    }

    public function storeStage(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->back()->with('error', __('Please select a company first.'));
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $maxOrder = LeadStage::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->max('sort_order');

        LeadStage::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'name' => trim($request->name),
            'sort_order' => ($maxOrder ?? 0) + 10,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('lead-manager.kanban')->with('success', __('Stage created.'));
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if ($lead->company_id != $companyId) {
            abort(404);
        }

        $validStages = array_keys(LeadStage::forCompany($companyId));
        $request->validate([
            'stage' => ['required', Rule::in($validStages)],
        ]);

        $lead->update(['stage' => $request->stage]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'stage' => $lead->stage]);
        }
        return redirect()->back()->with('success', __('Lead stage updated.'));
    }

    public function destroy(Lead $lead)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if ($lead->company_id != $companyId) {
            abort(404);
        }

        $lead->delete();
        return redirect()->route('lead-manager.index')->with('success', __('Lead deleted.'));
    }

    public function assign(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['exists:leads,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $updated = Lead::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereIn('id', $request->lead_ids)
            ->update(['user_id' => $request->user_id]);

        return redirect()->back()->with('success', __(':count lead(s) assigned.', ['count' => $updated]));
    }

    public function importForm()
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        if (!$companyId) {
            return redirect()->route('admin.companies.index')->with('error', __('Please select a company first.'));
        }

        $stages = LeadStage::forCompany($companyId);
        return view('lead-manager::import', compact('stages'));
    }

    public function importProcess(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id;
        $validStages = array_keys(LeadStage::forCompany($companyId));
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'source' => ['required', Rule::in(array_keys(Lead::sources()))],
            'stage' => ['required', Rule::in($validStages)],
        ]);

        $path = $request->file('file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);
        $header = array_map('trim', $header);

        $nameCol = $this->findColumn($header, ['name', 'full name', 'fullname']);
        $phoneCol = $this->findColumn($header, ['phone', 'mobile', 'tel', 'number']);
        $emailCol = $this->findColumn($header, ['email', 'e-mail']);

        if ($phoneCol === null) {
            return redirect()->back()->with('error', __('CSV must have a phone/mobile column.'));
        }

        $imported = 0;
        foreach ($rows as $row) {
            $data = array_combine($header, array_pad($row, count($header), ''));
            $phone = trim($data[$phoneCol] ?? '');
            if (empty($phone)) continue;

            $name = $nameCol !== null ? trim($data[$nameCol] ?? '') : '';
            $email = $emailCol !== null ? trim($data[$emailCol] ?? '') : null;

            $contact = Contact::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $companyId, 'phone' => $phone],
                ['name' => $name ?: 'Unknown', 'email' => $email]
            );

            Lead::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $companyId, 'contact_id' => $contact->id],
                ['source' => $request->source, 'stage' => $request->stage]
            );
            $imported++;
        }

        return redirect()->route('lead-manager.index')->with('success', __(':count lead(s) imported.', ['count' => $imported]));
    }

    private function findColumn(array $header, array $names): ?int
    {
        foreach ($header as $i => $h) {
            $h = strtolower(trim($h));
            foreach ($names as $n) {
                if (strpos($h, $n) !== false) return $i;
            }
        }
        return null;
    }
}
