<?php

namespace Modules\FlowGround\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Modules\FlowGround\Models\FlowGround;
use Modules\FlowGround\Models\FlowGroundVersion;
use Modules\FlowGround\Services\MetaFlowService;
use Modules\FlowGround\Services\FlowJsonBuilder;
use Modules\FlowGround\Services\FlowAnalyticsService;

class FlowGroundController extends Controller
{
    protected FlowJsonBuilder $flowJsonBuilder;
    protected FlowAnalyticsService $flowAnalyticsService;

    public function __construct(
        FlowJsonBuilder $flowJsonBuilder,
        FlowAnalyticsService $flowAnalyticsService
    ) {
        $this->flowJsonBuilder = $flowJsonBuilder;
        $this->flowAnalyticsService = $flowAnalyticsService;
    }

    public function index(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company) {
            abort(403);
        }

        $query = FlowGround::where('company_id', $company->id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('meta_flow_id', 'like', '%' . $request->search . '%');
            });
        }

        $hasStatusColumn = Schema::hasColumn('flow_grounds', 'status');
        if ($hasStatusColumn) {
            $status = $request->input('status');
            if ($status === 'all') {
                // no filter
            } elseif ($status && in_array($status, ['draft', 'published', 'deprecated'], true)) {
                $query->where('status', $status);
            } else {
                // Default view: show active flows only (hide deprecated)
                $query->where('status', '!=', 'deprecated');
            }
        }

        $flows = $query->orderBy('updated_at', 'desc')->paginate(15);

        $total = FlowGround::where('company_id', $company->id)
            ->when($hasStatusColumn, fn ($q) => $q->where('status', '!=', 'deprecated'))
            ->count();
        $published = $hasStatusColumn ? FlowGround::where('company_id', $company->id)->where('status', 'published')->count() : 0;
        $draft = $hasStatusColumn ? FlowGround::where('company_id', $company->id)->where('status', 'draft')->count() : $total;
        $deprecated = $hasStatusColumn ? FlowGround::where('company_id', $company->id)->where('status', 'deprecated')->count() : 0;

        // Optional submissions table: flow_ground_submissions (flow_ground_id, payload, created_at, ...)
        $dataCounts = [];
        if (Schema::hasTable('flow_ground_submissions') && Schema::hasColumn('flow_ground_submissions', 'flow_ground_id')) {
            $dataCounts = DB::table('flow_ground_submissions')
                ->select('flow_ground_id', DB::raw('COUNT(*) as c'))
                ->groupBy('flow_ground_id')
                ->pluck('c', 'flow_ground_id')
                ->toArray();
        }

        return view('flow-ground::index', compact('flows', 'total', 'published', 'draft', 'deprecated', 'dataCounts'));
    }

    public function create()
    {
        $this->ownerAndStaffOnly();
        if (! $this->getCompany()) {
            abort(403);
        }
        return view('flow-ground::create');
    }

    /**
     * Get WhatsApp Business Account ID and access token for Meta API.
     *
     * @return array{0: string, 1: string}|null [wabaId, accessToken] or null if not configured
     */
    protected function getMetaCredentials(): ?array
    {
        $company = $this->getCompany();
        if (! $company) {
            return null;
        }
        $wabaId = $company->getConfig('whatsapp_business_account_id', '');
        $token = $company->getConfig('whatsapp_permanent_access_token', '');
        if (empty($wabaId) || empty($token)) {
            return null;
        }
        return [$wabaId, $token];
    }

    /**
     * Convert our internal flow_json to Meta's Flow JSON format.
     * @see https://developers.facebook.com/docs/whatsapp/flows/reference/flowjson
     *
     * @return array{version: string, screens: array}
     */
    /**
     * Meta recommended Flow JSON version (frozen versions block publishing).
     * @see https://developers.facebook.com/docs/whatsapp/flows/reference/flowjson
     */
    protected function getMetaFlowVersion(): string
    {
        return '7.3';
    }

    protected function toMetaFlowJson(array $flowJson): array
    {
        $screensIn = $flowJson['screens'] ?? [];
        if (! is_array($screensIn)) {
            $screensIn = [];
        }
        if (empty($screensIn)) {
            $screensIn = [['id' => 'main', 'title' => 'Main', 'unique_name' => 'MAIN', 'blocks' => []]];
        }

        $screenIds = [];
        foreach ($screensIn as $idx => $screen) {
            $sid = $this->metaScreenId($screen, $idx);
            $screenIds[] = $sid;
        }

        $metaScreens = [];
        foreach ($screensIn as $idx => $screen) {
            $blocks = $screen['blocks'] ?? [];
            $children = [];
            $formNames = [];

            foreach ($blocks as $block) {
                $type = $block['type'] ?? '';
                $config = $block['config'] ?? [];
                if (empty($config)) {
                    $config = [
                        'label'       => $block['label'] ?? '',
                        'content'     => $block['content'] ?? '',
                        'name'        => $block['name'] ?? '',
                        'placeholder' => $block['placeholder'] ?? '',
                        'required'    => $block['required'] ?? false,
                        'options'     => $block['options'] ?? [],
                        'help_text'   => $block['help_text'] ?? '',
                        'inline'      => $block['inline'] ?? false,
                        'class'       => $block['class'] ?? '',
                    ];
                }
                // If options are empty, mirror builder defaults so Meta sees multiple options
                if (in_array($type, ['select', 'radio_group', 'checkbox_group'], true)) {
                    $opts = $config['options'] ?? [];
                    if (is_string($opts)) {
                        $opts = array_filter(array_map('trim', explode(',', $opts)));
                    }
                    if (! is_array($opts)) {
                        $opts = [];
                    }
                    if (! count($opts)) {
                        if ($type === 'select') {
                            $opts = ['Option 1', 'Option 2', 'Option 3'];
                        } else { // radio_group, checkbox_group
                            $opts = ['Option 1', 'Option 2'];
                        }
                    }
                    $config['options'] = $opts;
                }

                $comp = $this->blockToMetaComponent($type, $config);
                if ($comp !== null) {
                    $children[] = $comp;
                    if (isset($comp['name'])) {
                        $formNames[] = $comp['name'];
                    }
                }
            }

            $isLast = ($idx === count($screensIn) - 1);
            $screenId = $screenIds[$idx];
            $nextScreenId = ! $isLast && isset($screenIds[$idx + 1]) ? $screenIds[$idx + 1] : null;

            $footer = $this->metaFooterComponent($formNames, $isLast, $screenId, $nextScreenId);
            $children[] = $footer;

            $metaScreen = [
                'id'     => $screenId,
                'title'  => $screen['title'] ?? ('Screen ' . ($idx + 1)),
                'data'   => new \stdClass(),
                'layout' => [
                    'type'     => 'SingleColumnLayout',
                    'children' => $children,
                ],
            ];
            if ($isLast) {
                $metaScreen['terminal'] = true;
                $metaScreen['success'] = true;
            }
            $metaScreens[] = $metaScreen;
        }

        return [
            'version' => $this->getMetaFlowVersion(),
            'screens'  => $metaScreens,
        ];
    }

    /**
     * @param array<int, mixed> $options
     * @return array<int, array{id: string, title: string}>
     */
    protected function metaOptionsList(array $options): array
    {
        $opts = [];
        foreach ($options as $i => $o) {
            if (is_string($o)) {
                $title = $o;
                $id = strtolower($title);
                $id = preg_replace('/[^a-z0-9_]/', '_', $id);
                $id = trim($id, '_');
                if ($id === '') {
                    $id = 'option_' . $i;
                }
            } else {
                $title = $o['title'] ?? ('Option ' . ($i + 1));
                $id = $o['id'] ?? null;
                if (! $id) {
                    $id = strtolower($title);
                    $id = preg_replace('/[^a-z0-9_]/', '_', $id);
                    $id = trim($id, '_') ?: ('option_' . $i);
                }
            }
            $opts[] = ['id' => (string) $id, 'title' => (string) $title];
        }
        return $opts;
    }

    /**
     * Sanitize a form field name for Meta Flow JSON.
     * Must be [A-Za-z0-9_], cannot start with a digit.
     */
    protected function metaFieldName(?string $name): string
    {
        $name = $name ?: ('field_' . uniqid());
        $name = preg_replace('/[^A-Za-z0-9_]/', '_', $name);
        $name = trim($name, '_');
        if ($name === '' || ctype_digit($name[0])) {
            $name = 'f_' . $name;
        }
        return $name;
    }

    protected function metaScreenId(array $screen, int $idx): string
    {
        $name = $screen['unique_name'] ?? $screen['title'] ?? $screen['id'] ?? ('screen_' . $idx);
        $id = preg_replace('/[^A-Za-z0-9_]/', '_', (string) $name);
        $id = trim($id, '_') ?: ('SCREEN_' . $idx);
        $id = strtoupper($id);
        if ($id === 'SUCCESS') {
            $id = 'SCREEN_' . $idx;
        }
        return $id;
    }

    /**
     * @return array<string, mixed>|null Meta component or null to skip
     */
    protected function blockToMetaComponent(string $type, array $config): ?array
    {
        $label = $config['label'] ?? '';
        $text = $config['content'] ?? $label;
        $name = $this->metaFieldName($config['name'] ?? '');
        $required = ! empty($config['required']);
        $placeholder = $config['placeholder'] ?? '';
        $options = $config['options'] ?? [];
        if (is_string($options)) {
            $options = array_filter(array_map('trim', explode(',', $options)));
        }
        $options = array_values($options);

        switch ($type) {
            case 'header':
                return ['type' => 'TextHeading', 'text' => (string) $text];
            case 'subheading':
                return ['type' => 'TextSubheading', 'text' => (string) $text];
            case 'body':
                return ['type' => 'TextBody', 'text' => (string) $text];
            case 'caption':
                return ['type' => 'TextCaption', 'text' => (string) $text];
            case 'footer':
                return null;
            case 'text_field':
                $c = ['type' => 'TextInput', 'required' => $required, 'label' => (string) $label, 'name' => $name];
                if ($placeholder !== '') {
                    $c['placeholder'] = $placeholder;
                }
                return $c;
            case 'text_area':
                $c = ['type' => 'TextArea', 'required' => $required, 'label' => (string) $label, 'name' => $name];
                if ($placeholder !== '') {
                    $c['placeholder'] = $placeholder;
                }
                return $c;
            case 'select':
                $opts = $this->metaOptionsList($options);
                if (empty($opts)) {
                    $opts = [['id' => 'option_1', 'title' => 'Option 1']];
                }
                return [
                    'type'        => 'Dropdown',
                    'required'    => $required,
                    'label'       => (string) $label,
                    'name'        => $name,
                    'data-source' => $opts,
                ];
            case 'radio_group':
                $opts = $this->metaOptionsList($options);
                if (empty($opts)) {
                    $opts = [['id' => 'option_1', 'title' => 'Option 1']];
                }
                return [
                    'type'        => 'RadioButtonsGroup',
                    'required'    => $required,
                    'label'       => (string) $label,
                    'name'        => $name,
                    'data-source' => $opts,
                ];
            case 'checkbox_group':
                $opts = $this->metaOptionsList($options);
                if (empty($opts)) {
                    $opts = [['id' => 'option_1', 'title' => 'Option 1']];
                }
                return [
                    'type'        => 'CheckboxGroup',
                    'required'    => $required,
                    'label'       => (string) $label,
                    'name'        => $name,
                    'data-source' => $opts,
                ];
            case 'date_field':
                return ['type' => 'DatePicker', 'name' => $name, 'label' => (string) $label, 'required' => $required];
            default:
                return null;
        }
    }

    /**
     * @param array<int, string> $formNames
     */
    protected function metaFooterComponent(array $formNames, bool $isTerminal, string $screenId, ?string $nextScreenId): array
    {
        $payload = [];
        foreach ($formNames as $n) {
            $key = $this->metaFieldName($n);
            $payload[$key] = '${form.' . $key . '}';
        }
        if ($isTerminal) {
            return [
                'type'            => 'Footer',
                'label'           => 'Submit data',
                'on-click-action' => [
                    'name'    => 'complete',
                    'payload' => $payload,
                ],
            ];
        }
        $action = [
            'name'    => 'navigate',
            'next'    => ['type' => 'screen', 'name' => (string) $nextScreenId],
            'payload' => $payload,
        ];
        return [
            'type'            => 'Footer',
            'label'           => 'Continue',
            'on-click-action' => $action,
        ];
    }

    /**
     * Push flow to Meta (create or update). Updates flow.meta_flow_id and version.synced_to_meta on success.
     *
     * @return array{success: bool, meta_flow_id?: string, error?: string}
     */
    protected function pushFlowToMeta(FlowGround $flow, array $flowJson, FlowGroundVersion $version): array
    {
        $creds = $this->getMetaCredentials();
        if (! $creds) {
            return ['success' => false, 'error' => __('WhatsApp Business Account ID and Access Token must be configured.')];
        }
        [$wabaId, $accessToken] = $creds;
        $baseUrl = 'https://graph.facebook.com/v21.0';
        $name = $flow->name;
        $categories = $flowJson['categories'] ?? [];
        if (empty($categories) || ! is_array($categories)) {
            $categories = ['OTHER'];
        }
        $metaFlowJson = $this->toMetaFlowJson($flowJson);
        $flowJsonString = json_encode($metaFlowJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($flowJsonString === false) {
            return ['success' => false, 'error' => 'Failed to encode Flow JSON: ' . json_last_error_msg()];
        }

        if ($flow->meta_flow_id) {
            $flowId = $flow->meta_flow_id;
            $updateUrl = $baseUrl . '/' . $flowId . '/assets';
            $response = Http::withToken($accessToken)
                ->attach('file', $flowJsonString, 'flow.json')
                ->post($updateUrl, [
                    'name'       => 'flow.json',
                    'asset_type' => 'FLOW_JSON',
                ]);
            if (! $response->successful()) {
                $body = $response->json();
                $msg = $body['error']['message'] ?? $body['validation_errors'][0]['message'] ?? $response->body();
                return ['success' => false, 'error' => $msg];
            }
            $version->update(['synced_to_meta' => true]);
            return ['success' => true, 'meta_flow_id' => $flowId];
        }

        $createUrl = $baseUrl . '/' . $wabaId . '/flows';
        $response = Http::withToken($accessToken)->post($createUrl, [
            'name'       => $name,
            'categories' => $categories,
            'flow_json'  => $flowJsonString,
            'publish'    => false,
        ]);
        if (! $response->successful()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? null;
            if (! $msg && ! empty($body['validation_errors'][0]['message'])) {
                $msg = $body['validation_errors'][0]['message'];
            }
            if (! $msg) {
                $msg = $response->body();
            }
            return ['success' => false, 'error' => $msg];
        }
        $data = $response->json();
        $metaFlowId = (string) ($data['id'] ?? '');
        if ($metaFlowId && Schema::hasColumn('flow_grounds', 'meta_flow_id')) {
            $flow->update(['meta_flow_id' => $metaFlowId]);
        }
        $version->update(['synced_to_meta' => true]);
        return ['success' => true, 'meta_flow_id' => $metaFlowId];
    }

    /**
     * Build flow_grounds attributes for create/update using only columns that exist.
     * All form design data (flow_title, categories, screens, blocks) is stored in flow_json (meta).
     */
    protected function flowGroundAttributes(Request $request, bool $forCreate = false): array
    {
        $attrs = [
            'name' => $request->input('name'),
        ];
        if (Schema::hasColumn('flow_grounds', 'description')) {
            $attrs['description'] = $request->input('description');
        }
        if (Schema::hasColumn('flow_grounds', 'status')) {
            $attrs['status'] = 'draft';
        }
        if (Schema::hasColumn('flow_grounds', 'created_by') && $forCreate) {
            $attrs['created_by'] = auth()->id();
        }
        if (Schema::hasColumn('flow_grounds', 'company_id') && $forCreate) {
            $attrs['company_id'] = $this->getCompany()?->id;
        }
        return $attrs;
    }

    public function store(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company) {
            abort(403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'categories'  => 'nullable|array',
            'categories.*'=> 'string|max:64',
        ]);

        $attrs = $this->flowGroundAttributes($request, true);
        $flow = FlowGround::create($attrs);

        $flowJson = $request->input('flow_json', ['screens' => []]);
        if (is_string($flowJson)) {
            $flowJson = json_decode($flowJson, true) ?: ['screens' => []];
        }

        $categories = $request->input('categories', $flowJson['categories'] ?? []);
        if (! is_array($categories)) {
            $categories = [];
        }
        $categories = array_values(array_filter(array_map('strval', $categories)));
        if (Schema::hasColumn('flow_grounds', 'categories')) {
            $flow->update(['categories' => $categories]);
        }
        // Convert builder JSON to Meta Flow JSON before storing, so DB matches Meta format
        $metaFlowJson = $this->toMetaFlowJson($flowJson);
        $version = $flow->versions()->create([
            'version'        => 1,
            'flow_json'      => $metaFlowJson,
            'synced_to_meta' => false,
        ]);
        // Push to Meta using builder JSON (categories, etc.) and version record
        $metaResult = $this->pushFlowToMeta($flow, $flowJson, $version);
        if ($metaResult['success']) {
            return redirect()->route('flow-ground.index')->with('success', __('Flow created and synced to Meta.'));
        }
        return redirect()->route('flow-ground.index')
            ->with('success', __('Flow created successfully.'))
            ->with('warning', __('Meta sync failed: ') . ($metaResult['error'] ?? ''));
    }

    public function edit(FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }
        $latestVersion = $flowGround->versions()->orderBy('version', 'desc')->first();
        return view('flow-ground::create', [
            'flow'   => $flowGround,
            'edit'   => true,
            'flowJson' => $latestVersion ? $latestVersion->flow_json : ['screens' => []],
        ]);
    }

    public function update(Request $request, FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'categories'  => 'nullable|array',
            'categories.*'=> 'string|max:64',
        ]);

        $attrs = $this->flowGroundAttributes($request, false);
        $flowGround->update($attrs);

        $flowJson = $request->input('flow_json', ['screens' => []]);
        if (is_string($flowJson)) {
            $flowJson = json_decode($flowJson, true) ?: ['screens' => []];
        }

        $categories = $request->input('categories', $flowJson['categories'] ?? []);
        if (! is_array($categories)) {
            $categories = [];
        }
        $categories = array_values(array_filter(array_map('strval', $categories)));
        if (Schema::hasColumn('flow_grounds', 'categories')) {
            $flowGround->update(['categories' => $categories]);
        }
        // Convert builder JSON to Meta Flow JSON before storing.
        // If a version row already exists for this flow, update its JSON instead of creating a new row.
        $metaFlowJson = $this->toMetaFlowJson($flowJson);
        $latestVersion = $flowGround->versions()->orderBy('version', 'desc')->first();
        if ($latestVersion) {
            $latestVersion->update([
                'flow_json'      => $metaFlowJson,
                'synced_to_meta' => false,
            ]);
            $version = $latestVersion;
        } else {
            $version = $flowGround->versions()->create([
                'version'        => 1,
                'flow_json'      => $metaFlowJson,
                'synced_to_meta' => false,
            ]);
        }
        $metaResult = $this->pushFlowToMeta($flowGround, $flowJson, $version);
        if ($metaResult['success']) {
            return redirect()->route('flow-ground.index')->with('success', __('Flow updated and synced to Meta.'));
        }
        return redirect()->route('flow-ground.index')
            ->with('success', __('Flow updated successfully.'))
            ->with('warning', __('Meta sync failed: ') . ($metaResult['error'] ?? ''));
    }

    // public function destroy(FlowGround $flowGround)
    // {
    //     $this->ownerAndStaffOnly();
    //     $company = $this->getCompany();
    //     if (! $company || $flowGround->company_id !== $company->id) {
    //         abort(403);
    //     }
    //     $flowGround->delete();
    //     return redirect()->route('flow-ground.index')->with('success', __('Flow deleted.'));
    // }

    public function destroy(FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();

        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }

        $metaFlowId = $flowGround->meta_flow_id ?? null;

        // If not synced to Meta, delete locally only
        if (empty($metaFlowId)) {
            $flowGround->delete();
            return redirect()->route('flow-ground.index')->with('success', __('Flow deleted.'));
        }

        $metaService = new MetaFlowService($company);
        $result = $metaService->deleteFlow((string) $metaFlowId);

        if (($result['status'] ?? 500) === 200) {
            $flowGround->delete();
            return redirect()->route('flow-ground.index')->with('success', __('Flow deleted from Meta and removed locally.'));
        }

        $content = $result['content'] ?? null;
        $err = is_array($content) ? ($content['error'] ?? null) : null;
        $subcode = is_array($err) ? ($err['error_subcode'] ?? null) : null;
        $userTitle = is_array($err) ? (string) ($err['error_user_title'] ?? '') : '';

        // Published flows cannot be deleted on Meta; they must be deprecated instead.
        if ($subcode === 4016026 || stripos($userTitle, 'Delete published flow') !== false) {
            $dep = $metaService->deprecateFlow((string) $metaFlowId);
            if (($dep['status'] ?? 500) === 200) {
                if (Schema::hasColumn('flow_grounds', 'status')) {
                    $flowGround->update(['status' => 'deprecated']);
                }
                return redirect()->route('flow-ground.index')
                    ->with('success', __('Flow is published and cannot be deleted on Meta. It was deprecated and archived.'));
            }
            $depContent = $dep['content'] ?? null;
            $depMsg = is_string($depContent) ? $depContent : (is_array($depContent) ? json_encode($depContent) : __('Unknown error'));
            return redirect()->route('flow-ground.index')
                ->with('error', __('Meta requires deprecation, but deprecate failed: ') . $depMsg);
        }

        $msg = is_string($content) ? $content : (is_array($content) ? json_encode($content) : __('Unknown error'));
        return redirect()->route('flow-ground.index')
            ->with('error', __('Delete failed on Meta. Nothing was removed. ') . $msg);
    }

    public function sync()
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company) {
            abort(403);
        }

        $wabaId = $company->getConfig('whatsapp_business_account_id', '');
        $accessToken = $company->getConfig('whatsapp_permanent_access_token', '');

        if (empty($wabaId) || empty($accessToken)) {
            return redirect()->route('flow-ground.index')->with('error', __('Please configure WhatsApp Business Account ID and Permanent Access Token in WhatsApp setup first.'));
        }

        if (! Schema::hasColumn('flow_grounds', 'meta_flow_id')) {
            return redirect()->route('flow-ground.index')->with('error', __('Database is missing meta_flow_id column. Please run: php artisan migrate'));
        }

        $url = 'https://graph.facebook.com/v21.0/' . $wabaId . '/flows';
        $response = Http::withToken($accessToken)->get($url);

        if (! $response->successful()) {
            $body = $response->json();
            $message = $body['error']['message'] ?? $response->body();
            return redirect()->route('flow-ground.index')->with('error', __('Meta API error: ') . $message);
        }

        $data = $response->json();
        $items = $data['data'] ?? [];
        $synced = 0;

        foreach ($items as $item) {
            $metaId = (string) ($item['id'] ?? '');
            $name = $item['name'] ?? 'Flow ' . $metaId;
            $metaStatus = strtoupper((string) ($item['status'] ?? 'DRAFT'));
            if ($metaStatus === 'PUBLISHED') {
                $status = 'published';
            } elseif ($metaStatus === 'DEPRECATED') {
                $status = 'deprecated';
            } else {
                $status = 'draft';
            }
            $categories = isset($item['categories']) ? json_encode($item['categories']) : null;

            $flow = FlowGround::where('company_id', $company->id)->where('meta_flow_id', $metaId)->first();

            if ($flow) {
                $flow->update([
                    'name'   => $name,
                    'status' => $status,
                ]);
            } else {
                FlowGround::create([
                    'company_id'    => $company->id,
                    'name'          => $name,
                    'meta_flow_id'  => $metaId,
                    'status'        => $status,
                    'created_by'    => auth()->id(),
                ]);
            }
            $synced++;
        }

        return redirect()->route('flow-ground.index')->with('success', __('Synced :count flow(s) from Meta.', ['count' => $synced]));
    }

    public function publish(FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }

        if (! Schema::hasColumn('flow_grounds', 'meta_flow_id') || empty($flowGround->meta_flow_id)) {
            return redirect()->route('flow-ground.index')
                ->with('error', __('Flow must be synced to Meta before publishing.'));
        }

        $creds = $this->getMetaCredentials();
        if (! $creds) {
            return redirect()->route('flow-ground.index')
                ->with('error', __('WhatsApp Business Account ID and Access Token must be configured.'));
        }
        [$wabaId, $accessToken] = $creds;
        $baseUrl = 'https://graph.facebook.com/v21.0';
        $url = $baseUrl . '/' . $flowGround->meta_flow_id . '/publish';

        $response = Http::withToken($accessToken)->post($url);
        if (! $response->successful()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? $response->body();
            return redirect()->route('flow-ground.index')
                ->with('error', __('Meta publish error: ') . $msg);
        }

        if (Schema::hasColumn('flow_grounds', 'status')) {
            $flowGround->update(['status' => 'published']);
        }

        return redirect()->route('flow-ground.index')->with('success', __('Flow published on Meta.'));
    }

    public function preview(FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }

        if (empty($flowGround->meta_flow_id)) {
            return redirect()->route('flow-ground.index')
                ->with('error', __('Flow must be synced to Meta to preview.'));
        }

        $creds = $this->getMetaCredentials();
        if (! $creds) {
            return redirect()->route('flow-ground.index')
                ->with('error', __('WhatsApp Business Account ID and Access Token must be configured.'));
        }
        [, $accessToken] = $creds;

        $baseUrl = 'https://graph.facebook.com/v21.0';
        $url = $baseUrl . '/' . $flowGround->meta_flow_id;

        // Generate (or reuse) a web preview link
        $response = Http::withToken($accessToken)->get($url, [
            'fields' => 'preview.invalidate(false)',
        ]);

        if (! $response->successful()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? $response->body();
            return redirect()->route('flow-ground.index')
                ->with('error', __('Meta preview error: ') . $msg);
        }

        $data = $response->json();
        $previewUrl = data_get($data, 'preview.preview_url');
        if (! $previewUrl) {
            return redirect()->route('flow-ground.index')
                ->with('error', __('Meta preview URL not available for this flow.'));
        }

        return redirect()->away($previewUrl);
    }

    public function viewData()
    {
        $this->ownerAndStaffOnly();
        return redirect()->route('flow-ground.index')->with('info', __('View Flow Data – coming soon.'));
    }

    public function showData(FlowGround $flowGround)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        if (! $company || $flowGround->company_id !== $company->id) {
            abort(403);
        }

        $rows = collect();
        if (Schema::hasTable('flow_ground_submissions') && Schema::hasColumn('flow_ground_submissions', 'flow_ground_id')) {
            $rows = DB::table('flow_ground_submissions')
                ->where('flow_ground_id', $flowGround->id)
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();
        }

        return view('flow-ground::data', [
            'flow' => $flowGround,
            'rows' => $rows,
        ]);
    }
}
