<?php

namespace Modules\Catalogs\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Exception;
use App\Models\User;
use App\Models\Config;
use App\Models\Catalog;
use App\Models\CatalogProduct;
use Illuminate\Support\Facades\Http;
use Modules\Contacts\Models\Contact;
use Modules\Catalogs\Models\OrderItem;
use App\Models\Paymenttemplate;
use Modules\Catalogs\Models\ProductCategory;
use Modules\Catalogs\Models\Order;
use Modules\Catalogs\Models\DeliveryAgent;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Models\Template;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Modules\Catalogs\Models\OrderAddress;

class Main extends Controller
{
    /**
     * Provide class.
     */
    private $provider = User::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'catalog.';

    /**
     * View path.
     */
    private $view_path = 'Catalogs::';

    /**
     * Parameter name.
     */
    private $parameter_name = 'Catalog';

    /**
     * Title of this crud.
     */
    private $title = 'Catalog';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'Catalog';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        if (!auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getFields()
    {
        return [['class' => 'col-md-4', 'ftype' => 'input', 'name' => 'Name', 'id' => 'name', 'placeholder' => 'First and Last name', 'required' => true], ['class' => 'col-md-4', 'ftype' => 'input', 'name' => 'Email', 'id' => 'email', 'placeholder' => 'Enter email', 'required' => true], ['class' => 'col-md-4', 'ftype' => 'input', 'type' => 'password', 'name' => 'Password', 'id' => 'password', 'placeholder' => 'Enter password', 'required' => true]];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }

        $this->authChecker();
        
        // Validate input
        $validated = $request->validate([
            'catalog_id' => 'nullable|string|max:255',
        ]);
        
        $fields = $this->getFields();
        unset($fields[2]);
        $catalog_id = $validated['catalog_id'] ?? null;
        $company = $this->getCompany();
        $company_id = $company->id;

        $products = $catalog_id 
            ? CatalogProduct::where('catalog_id', $catalog_id)->where('company_id', $company_id)->get()
            : collect();

        $catalogs = Catalog::where('company_id', $company_id)->get();
        return view($this->view_path . 'index', [
            'setup' => [
                'title' => __('crud.item_managment', ['item' => __($this->titlePlural)]),
                'action_name' => __('crud.add_new_item', ['item' => __($this->title)]),
                'items' => $catalogs, // Use catalogs as items for pagination structure
                'item_names' => $this->titlePlural,
                'webroute_path' => $this->webroute_path,
                'fields' => $fields,
                'parameter_name' => $this->parameter_name,
                'hidePaging' => true,
            ],
            'catalogs' => $catalogs,
            'products' => $products,
            'catalog_id' => $catalog_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authChecker();

        return view('general.form-client', [
            'setup' => [
                'inrow' => true,
                'title' => __('crud.new_item', ['item' => __($this->title)]),
                'action_link' => route($this->webroute_path . 'index'),
                'action_name' => __('crud.back'),
                'action_icon' => '',
                'iscontent' => true,
                'action' => route($this->webroute_path . 'store'),
            ],
            'fields' => $this->getFields(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authChecker();

        $email_exist = $this->provider::where('email', $request->email)->first();

        if (!$email_exist) {
            $item = $this->provider::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(80),
                'company_id' => $this->getCompany()->id,
            ]);
            $item->save();

            $item->assignRole('staff');

            return redirect()
                ->route($this->webroute_path . 'index')
                ->withStatus(__('crud.item_has_been_added', ['item' => __($this->title)]));
        } else {
            return redirect()
                ->route($this->webroute_path . 'index')
                ->withStatus(__('Error: This email address is already registered. Please use a different email address.', ['item' => __($this->title)]));
        }
    }

    public function loginas($id)
    {
        $this->authChecker();
        if (config('settings.is_demo', false)) {
            return redirect()->back()->withStatus('Not allowed in demo');
        }

        $agent = User::findOrFail($id);

        if ($agent->company->user->id != auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        Auth::login($agent, true);

        //Set the company
        Session::put('company_id', $agent->company->id);

        //Login as owner
        Session::put('impersonate', $agent->id);

        return redirect(route('home'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authChecker();

        $item = $this->provider::findOrFail($id);
        if (!$this->getCompany()->id == $item->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $fields = $this->getFields();
        $fields[0]['value'] = $item->name;
        $fields[1]['value'] = $item->email;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return $catalogs;
        return view('general.form-client', [
            'setup' => [
                'inrow' => true,
                'title' => __('crud.edit_item_name', ['item' => __($this->title), 'name' => $item->name]),
                'action_link' => route($this->webroute_path . 'index'),
                'action_name' => __('crud.back'),
                'action_icon' => '',
                'iscontent' => true,
                'isupdate' => true,
                'action' => route($this->webroute_path . 'update', $parameter),
            ],
            'fields' => $fields,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->email = $request->email;
        if ($request->password && strlen($request->password) > 2) {
            $item->password = Hash::make($request->password);
        }
        $item->update();

        return redirect()
            ->route($this->webroute_path . 'index')
            ->withStatus(__('crud.item_has_been_updated', ['item' => __($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->delete();
        return redirect()
            ->route($this->webroute_path . 'index')
            ->withStatus(__('crud.item_has_been_removed', ['item' => __($this->title)]));
    }

    // public function productsCatalog(Request $request)
    // {
    //     $this->authChecker();
    //     $company = $this->getCompany();
    //     $fields = $this->getFields();
    //     unset($fields[2]);
    //     $company_id = $company->id;
    //     $categories = ProductCategory::where('company_id', $company_id)->get(); // Fetch all categories
    //     $catalogs = Catalog::where('company_id', $company_id)->where('status', 1)->pluck('catalog_id')->toArray();
    //     // $products = CatalogProduct::whereIn('catalog_id',$catalogs)->where('company_id',$company_id)->get();
    //     $products = CatalogProduct::whereIn('catalog_id', $catalogs)->where('company_id', $company_id)->orderBy('created_at', 'asc')->paginate(10);

    //     // $products = CatalogProduct::;
    //     return view($this->view_path . 'catalog-product', [
    //         'setup' => [
    //             'title' => __('crud.item_managment', ['item' => __($this->titlePlural)]),
    //             // 'action_link'=>route($this->webroute_path.'create'),
    //             'action_name' => __('crud.add_new_item', ['item' => __($this->title)]),
    //             'items' => $this->getCompany()->staff()->paginate(config('settings.paginate')),
    //             'item_names' => $this->titlePlural,
    //             'webroute_path' => $this->webroute_path,
    //             'fields' => $fields,
    //             'parameter_name' => $this->parameter_name,
    //         ],
    //         'catalogs' => $catalogs,
    //         'categories' => $categories,
    //         'products' => $products,
    //     ]);
    // }

    public function productsCatalog(Request $request)
    {
        $this->authChecker();

        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }
        
        // Validate input
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|integer|exists:product_category,id',
            'stock_status' => 'nullable|in:0,1',
        ]);
        
        $company = $this->getCompany();
        $company_id = $company->id;

        try {
            // Get base query
            $productQuery = CatalogProduct::where('company_id', $company_id)->whereIn('catalog_id', function ($query) use ($company_id) {
                $query->select('catalog_id')->from('catalogs')->where('company_id', $company_id)->where('status', 1);
            });

            // Apply search filter
            if (!empty($validated['search'])) {
                $searchTerm = $validated['search'];
                $productQuery->where(function ($query) use ($searchTerm) {
                    $query->where('product_name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }

            // Apply category filter
            if (!empty($validated['category'])) {
                $category = ProductCategory::where('id', $validated['category'])
                    ->where('company_id', $company_id)
                    ->first();
                if ($category && !empty($category->retailer_id)) {
                    $retailerIds = explode(',', $category->retailer_id);
                    $productQuery->whereIn('retailer_id', $retailerIds);
                }
            }

            // Apply stock status filter (only if column exists)
            if (isset($validated['stock_status']) && $validated['stock_status'] !== '' && Schema::hasColumn('catalog_products', 'stock_status')) {
                $productQuery->where('stock_status', (int) $validated['stock_status']);
            }

            // Get paginated results
            $products = $productQuery->orderBy('created_at', 'asc')->paginate(10)->withQueryString();

            // Get all categories for filter dropdown
            $categories = ProductCategory::where('company_id', $company_id)->get();

            // Get fields for view
            $fields = $this->getFields();
            unset($fields[2]);

            return view($this->view_path . 'catalog-product', [
                'setup' => [
                    'title' => __('Product Management'),
                    'subtitle' => __('Manage your product catalog'),
                    'items' => $products, // Use products for pagination
                    'item_names' => __('Products'),
                    'webroute_path' => $this->webroute_path,
                    'fields' => $fields,
                    'parameter_name' => $this->parameter_name,
                ],
                'categories' => $categories,
                'products' => $products,
                'company_id' => $company_id,
                'search_term' => $validated['search'] ?? '',
                'selected_category' => $validated['category'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Catalog products error: ' . $e->getMessage());
            return redirect()->route('catalog.index')
                ->with('error', 'An error occurred while loading products. Please try again.');
        }
    }

    public function fetchCatalog()
    {
        try {
            if (
                $this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes'
                || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes'
            ) {
                return response()->json(['status' => 'error', 'message' => 'Setup not completed'], 400);
            }

            $company = $this->getCompany();
            $company_id = $company->id;
            $whatsapp_permanent_access_token = Config::where('model_id', $company_id)
                ->where('key', 'whatsapp_permanent_access_token')
                ->value('value');
            $whatsapp_business_account_id = Config::where('model_id', $company_id)
                ->where('key', 'whatsapp_business_account_id')
                ->value('value');

            // Use config() instead of env() for better performance and testing
            $accessToken = config('services.facebook.catalog_token') ?? $whatsapp_permanent_access_token;
            $businessId = $whatsapp_business_account_id;

            if (!$accessToken || !$businessId) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'WhatsApp access token or business ID not configured'
                ], 400);
            }

            $url = "https://graph.facebook.com/v21.0/{$businessId}/product_catalogs";
            $response = Http::timeout(30)->get($url, [
                'access_token' => $accessToken
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (empty($data['data'])) {
                    return response()->json(['status' => 'error', 'message' => 'No catalog data found']);
                }

                foreach ($data['data'] as $catalog) {
                    $catalog_update = Catalog::updateOrCreate(
                        ['catalog_id' => $catalog['id'], 'company_id' => $company_id],
                        ['name' => $catalog['name'] ?? 'Unnamed Catalog']
                    );

                    // fetch products (with pagination handled)
                    $this->fetchProductCatalog($accessToken, $catalog['id'], $company_id);
                }

                return response()->json(['status' => 'success', 'message' => 'Products synced successfully']);
            }

            $errorData = $response->json();
            \Log::error('Facebook catalog API error: ' . json_encode($errorData));
            
            return response()->json([
                'status' => 'error', 
                'message' => 'API error: ' . ($errorData['error']['message'] ?? 'Unknown error'),
                'details' => $errorData
            ], $response->status());
        } catch (\Exception $e) {
            \Log::error('Fetch catalog error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching catalog: ' . $e->getMessage()
            ], 500);
        }
    }

    private function fetchProductCatalog($accessToken, $catalogId, $company_id)
    {
        try {
            $url = "https://graph.facebook.com/v21.0/{$catalogId}/products";
            $after = null;
            $maxPages = 100; // Prevent infinite loops
            $pageCount = 0;

            do {
                $params = ['access_token' => $accessToken];
                if ($after) {
                    $params['after'] = $after;
                }

                $response = Http::timeout(30)->get($url, $params);

                if (!$response->successful()) {
                    \Log::error('Facebook product catalog API error: ' . json_encode($response->json()));
                    break;
                }

                $data = $response->json();

                if (empty($data['data'])) {
                    break;
                }

                foreach ($data['data'] as $product) {
                    try {
                        CatalogProduct::updateOrCreate(
                            [
                                'catalog_id' => $catalogId,
                                'product_id' => $product['id'] ?? null,
                                'company_id' => $company_id
                            ],
                            [
                                'name' => $product['name'] ?? 'Unnamed Product',
                                'retailer_id' => $product['retailer_id'] ?? null,
                                'product_name' => $product['name'] ?? 'Unnamed Product',
                            ]
                        );

                        // fetch product details
                        if (!empty($product['id'])) {
                            $detailUrl = "https://graph.facebook.com/v21.0/{$product['id']}";
                            $detailResponse = Http::timeout(30)->get($detailUrl, [
                                'fields' => 'name,description,price,image_url',
                                'access_token' => $accessToken
                            ]);

                            if ($detailResponse->successful()) {
                                $details = $detailResponse->json();
                                CatalogProduct::where('product_id', $product['id'])
                                    ->where('company_id', $company_id)
                                    ->update([
                                        'product_name' => $details['name'] ?? null,
                                        'description'  => $details['description'] ?? null,
                                        'price'        => $details['price'] ?? null,
                                        'image_url'    => $details['image_url'] ?? null,
                                    ]);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error processing product: ' . $e->getMessage());
                        continue;
                    }
                }

                // Handle pagination
                $after = $data['paging']['cursors']['after'] ?? null;
                $pageCount++;
                
                if (!$after || $pageCount >= $maxPages) {
                    break;
                }
            } while (true);
        } catch (\Exception $e) {
            \Log::error('Fetch product catalog error: ' . $e->getMessage());
        }
    }


   
    public function catalogsTemplatesIndex()
    {
        $this->authChecker();

        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }

        $items = Template::where('type', '1')->orderBy('created_at', 'desc');

        if (isset($_GET['name']) && strlen($_GET['name']) > 1) {
            $items = $items->where('name', 'like', '%' . $_GET['name'] . '%');
        } else {
            //If there are 0 template,and there is no filter, load them
            try {
                $this->loadTemplatesFromWhatsAppCatalog();
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        $items = $items->paginate(config('settings.paginate'));

        //If there is a refresh request
        if (isset($_GET['refresh']) && $_GET['refresh'] == 'yes') {
            $this->loadTemplatesFromWhatsAppCatalog();
        }

        return view($this->view_path . 'templates_index', [
            'setup' => [
                'title' => 'Catalog Templates management',

                'action_link5' => 'https://business.facebook.com/wa/manage/message-templates/',
                'action_name5' => __('WhatsApp Manager'),
                'action_icon5' => '',
                'action_link3' => route('catalog.catalogsTemplatesCreate'),
                'action_name3' => '+ ' . __('Create Template'),

                'action_icon3' => '',
                'action_link4' => route('catalog.carouselTemplatesCreate'),
                'action_name4' => '+ ' . __('Carousel Template'),
                'action_icon4' => '',
                'items' => $items,
                'item_names' => '',
                'webroute_path' => '',
                'fields' => [],
                'custom_table' => true,
                'parameter_name' => '',
                'parameters' => count($_GET) != 0,
            ],
        ]);
        return view($this->view_path . 'templates_index', compact('templates'));
    }
    public function catalogsTemplatesCreate()
    {
        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }
        $languages = 'Afrikaans,af,Albanian,sq,Arabic,ar,Azerbaijani,az,Bengali,bn,Bulgarian,bg,Catalan,ca,Chinese (CHN),zh_CN,Chinese (HKG),zh_HK,Chinese (TAI),zh_TW,Croatian,hr,Czech,cs,Danish,da,Dutch,nl,English,en,English (UK),en_GB,English (US),en_US,Estonian,et,Filipino,fil,Finnish,fi,French,fr,Georgian,ka,German,de,Greek,el,Gujarati,gu,Hausa,ha,Hebrew,he,Hindi,hi,Hungarian,hu,Indonesian,id,Irish,ga,Italian,it,Japanese,ja,Kannada,kn,Kazakh,kk,Kinyarwanda,rw_RW,Korean,ko,Kyrgyz (Kyrgyzstan),ky_KG,Lao,lo,Latvian,lv,Lithuanian,lt,Macedonian,mk,Malay,ms,Malayalam,ml,Marathi,mr,Norwegian,nb,Persian,fa,Polish,pl,Portuguese (BR),pt_BR,Portuguese (POR),pt_PT,Punjabi,pa,Romanian,ro,Russian,ru,Serbian,sr,Slovak,sk,Slovenian,sl,Spanish,es,Spanish (ARG),es_AR,Spanish (SPA),es_ES,Spanish (MEX),es_MX,Swahili,sw,Swedish,sv,Tamil,ta,Telugu,te,Thai,th,Turkish,tr,Ukrainian,uk,Urdu,ur,Uzbek,uz,Vietnamese,vi,Zulu,zuv';
        $languages = explode(',', $languages);
        $languages = array_chunk($languages, 2);
        $products = CatalogProduct::get();
        return view($this->view_path . 'templates_create', compact('languages', 'products'));
    }

    //uploadImage
    public function uploadImage(Request $request)
    {
        $this->authChecker();

        $imageURL = $this->saveImage('media', $request->imageupload);

        return response()->json(['status' => 'success', 'url' => $imageURL]);
    }

    //uploadVideo
    public function uploadVideo(Request $request)
    {
        $this->authChecker();

        $videoURL = $this->saveImage('media', $request->videoupload);

        return response()->json(['status' => 'success', 'url' => $videoURL]);
    }

    //uploadPdf
    public function uploadPdf(Request $request)
    {
        $this->authChecker();

        $pdfURL = $this->saveImage('media', $request->pdfupload);

        return response()->json(['status' => 'success', 'url' => $pdfURL]);
    }
    private function saveImage($folder, $laravel_file_resource)
    {
        if (config('settings.use_s3_as_storage', false)) {
            //S3 - store per company
            $path = $laravel_file_resource->storePublicly('uploads/companies', 's3');

            return Storage::disk('s3')->url($path);
        } else {
            $path = $laravel_file_resource->store($folder, 'public_uploads');
            $url = config('app.url') . '/uploads/' . $path;

            return preg_replace('#(https?:\/\/[^\/]+)\/\/#', '$1/', $url);
        }
    }

    public function submitCatalogTemplate(Request $request)
    {
        $this->authChecker();
        $result = $this->submitWhatsAppTemplateCatalog($request->all());
        //Check s tatus code
        if ($result['status'] == 200) {
            //Respond with json
            return response()->json(['status' => 'success']);
        } else {
            //Respond with json
            return response()->json(['status' => 'error', 'response' => $result]);
        }
    }

    private function submitWhatsAppTemplateCatalog($templateData)
    {
        $company = $this->getCompany();
        $company_id = $company->id;
        $whatsapp_permanent_access_token = Config::where('model_id', $company_id)->where('key', 'whatsapp_permanent_access_token')->first();
        $whatsapp_business_account_id = Config::where('model_id', $company_id)->where('key', 'whatsapp_business_account_id')->first();

        $whatsappBusinessAccountId = $whatsapp_business_account_id->value;
        $url = "https://graph.facebook.com/v21.0/$whatsappBusinessAccountId/message_templates";
        $accessToken = $whatsapp_permanent_access_token->value;
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $templateData);

            $statusCode = $response->status();
            $content = json_decode($response->body(), true);
            return ['status' => $statusCode, 'content' => $content];
        } catch (\Exception $e) {
            // Handle the exception
            return ['status' => 500, 'content' => $e->getMessage()];
        }
    }

    private function loadTemplatesFromWhatsAppCatalog($after = '')
    {
        $company = $this->getCompany();
        $company_id = $company->id;
        $whatsapp_permanent_access_token = Config::where('model_id', $company_id)->where('key', 'whatsapp_permanent_access_token')->first();
        $whatsapp_business_account_id = Config::where('model_id', $company_id)->where('key', 'whatsapp_business_account_id')->first();

        $whatsappBusinessAccountId = $whatsapp_business_account_id->value;
        $url = "https://graph.facebook.com/v21.0/$whatsappBusinessAccountId/message_templates";
        $accessToken = $whatsapp_permanent_access_token->value;
        $queryParams = [
            'fields' => 'name,category,language,quality_score,components,status',
            'limit' => 100,
        ];
        if ($after != '') {
            $queryParams['after'] = $after;
        }
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        $response = Http::withHeaders($headers)->get($url, $queryParams);
        // Handle the response here
        if ($response->successful()) {
            $responseData = $response->json();
            //On success - remove all previous templates, if on initial call
            if ($after == '') {
                Template::query()->each(function ($model) {
                    try {
                        $model->delete();
                    } catch (\Throwable $th) {
                    }
                });
            }

            $companyID = $this->getCompany()->id;

            foreach ($responseData['data'] as $key => $template) {
                if (isset($template['components'][1]['buttons'][0]['type']) && $template['components'][1]['buttons'][0]['type'] == 'CATALOG') {
                    //Insert new messages
                    try {
                        $data = [
                            'name' => $template['name'],
                            'category' => $template['category'],
                            'language' => $template['language'],
                            'status' => $template['status'],
                            'id' => $template['id'],
                            'company_id' => $companyID,
                            'components' => json_encode($template['components']),
                            'product_id' => $template['product_id'] ?? null,
                            'type' => '1',
                        ];
                        $template = Template::upsert($data, 'id', ['components', 'status']);
                    } catch (\Throwable $th) {
                        //throw $th;
                        //dd($th);
                    }
                } elseif (isset($template['components'][1]['type']) && $template['components'][1]['type'] == 'CAROUSEL') {
                    //Insert new messages
                    try {
                        $data = [
                            'name' => $template['name'],
                            'category' => $template['category'],
                            'language' => $template['language'],
                            'status' => $template['status'],
                            'id' => $template['id'],
                            'company_id' => $companyID,
                            'components' => json_encode($template['components']),
                            'product_id' => $template['product_id'] ?? null,
                            'type' => '1',
                        ];
                        $template = Template::upsert($data, 'id', ['components', 'status']);
                    } catch (\Throwable $th) {
                        //throw $th;
                        //dd($th);
                    }
                } else {
                    //Insert new messages
                    try {
                        $data = [
                            'name' => $template['name'],
                            'category' => $template['category'],
                            'language' => $template['language'],
                            'status' => $template['status'],
                            'id' => $template['id'],
                            'company_id' => $companyID,
                            'components' => json_encode($template['components']),
                        ];
                        $template = Template::upsert($data, 'id', ['components', 'status']);
                    } catch (\Throwable $th) {
                        //throw $th;
                        //dd($th);
                    }
                }
            }
            //Check if we have more templates
            if ($responseData['paging'] && isset($responseData['paging']['next']) && isset($responseData['paging']['cursors']['after'])) {
                return $this->loadTemplatesFromWhatsAppCatalog($responseData['paging']['cursors']['after']);
            } else {
                return true;
            }
            // Process $responseData as needed
        } else {
            // Handle error response
            return false;
        }
    }

    public function destroyCatalog($id)
    {
        $this->authChecker();

        //Don't allow to delete if it is demo
        if (config('settings.is_demo', false)) {
            return redirect()
                ->route('catalog.catalogsTemplatesIndex', ['refresh' => 'yes'])
                ->withStatus(__('crud.error', ['error' => 'Disabled in demo']));
        }
        $item = Template::find($id);
        if ($item) {
            $result = $this->deleteWhatsAppTemplateCatalog($item->name);
            if ($result['status'] == 200) {
                return redirect()
                    ->route('catalog.catalogsTemplatesIndex', ['refresh' => 'yes'])
                    ->withStatus(__('crud.item_has_been_deleted', ['item' => __($this->title)]));
            } else {
                return redirect()
                    ->route('catalog.catalogsTemplatesIndex', ['refresh' => 'yes'])
                    ->withStatus(__('crud.error', ['error' => 'Error']));
            }
        } else {
            return redirect()
                ->route('catalog.catalogsTemplatesIndex', ['refresh' => 'yes'])
                ->withStatus(__('crud.error', ['error' => 'Error']));
        }
    }

    private function deleteWhatsAppTemplateCatalog($templateName)
    {
        $company = $this->getCompany();
        $company_id = $company->id;
        $whatsapp_permanent_access_token = Config::where('model_id', $company_id)->where('key', 'whatsapp_permanent_access_token')->first();
        $whatsapp_business_account_id = Config::where('model_id', $company_id)->where('key', 'whatsapp_business_account_id')->first();

        $whatsappBusinessAccountId = $whatsapp_business_account_id->value;
        $accessToken = $whatsapp_permanent_access_token->value;
        $url = "https://graph.facebook.com/v21.0/$whatsappBusinessAccountId/message_templates" . $templateName;
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->delete($url);

            $statusCode = $response->status();
            $content = json_decode($response->body(), true);
            return ['status' => $statusCode, 'content' => $content];
        } catch (\Exception $e) {
            // Handle the exception
            return ['status' => 500, 'content' => $e->getMessage()];
        }
    }

    public function sendWhatsAppCatalogTemplateMessage(Request $request)
    {
        $company = $this->getCompany();
        $group_id = $request->group_id;
        $template_id = $request->template_id;
        $contactsData = Group::with('contacts')->where('id', $group_id)->where('company_id', $company->id)->first();

        $catalog_product = CatalogProduct::where('company_id', $company->id)->first();
        $total_contact = Contact::get();
        $template = Template::where('id', $template_id)->first();

        // $whatsappBusinessPhoneNumberId = '304000432806043';
        // $accessToken = 'EAAR1kVmt2bUBOxU6OfTiLnpQA9SPqabpsowa6ZCEp2pyvzmUFcHdsQxrZAb74Knn4gVyZCmJH54zB8abBKTWUYgs2r1ZBocemDo9ofQ3pW3LGysRhs9Ktzee4GT69dhoClL8pIeRPiJsa7Lamjp7pAwZAWZBV9lqzGBWSeoVVCGh6shPNtRn1C9zd5ZC4e9fapwUQZDZD';
        $companyId = $company->id;
        $whatsapp_permanent_access_token = Config::where('model_id', $companyId)->where('key', 'whatsapp_permanent_access_token')->first();

        $whatsapp_phone_number_id = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone_number_id')->first();

        $accessToken = $whatsapp_permanent_access_token->value;
        $whatsappBusinessPhoneNumberId = $whatsapp_phone_number_id->value;

        $results = [];
        $newCampaignId = DB::table('wa_campaings')->insertGetId([
            'name' => $request->name,
            'send_to' => count($contactsData->contacts),
            'timestamp_for_delivery' => $request->send_time,
            'company_id' => $company->id,
            'template_id' => $template->id,
            'group_id' => $request->group_id,
            'total_contacts' => count($total_contact),
        ]);

        if ($contactsData && count($contactsData->contacts) > 0) {
            foreach ($contactsData->contacts as $contact) {
                $url = "https://graph.facebook.com/v21.0/$whatsappBusinessPhoneNumberId/messages";
                $data = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $contact->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $template->name,
                        'language' => [
                            'code' => $template->language,
                        ],
                        'components' => [
                            [
                                'type' => 'BUTTON',
                                'sub_type' => 'catalog',
                                'index' => 0,
                                'parameters' => [
                                    [
                                        'type' => 'action',
                                        'action' => [
                                            'thumbnail_product_retailer_id' => '1hb3geg15a',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                try {
                    // Send the request using Laravel's Http client
                    $response = Http::withToken($accessToken)->post($url, $data);

                    // Collect response for each contact
                    if ($response->successful()) {
                        $responseData = $response->json();
                        $results[] = [
                            'contact' => $contact->phone,
                            'status' => 'success',
                            'response' => $response->json(),
                        ];
                        if ($responseData['messages'][0]['message_status'] == 'accepted') {
                            $status = 3;
                        } else {
                            $status = 1;
                        }

                        $fb_message_id = $responseData['messages'][0]['id']; // Extract the message ID

                        // Insert into the messages table
                        DB::table('messages')->insert([
                            'fb_message_id' => $fb_message_id,
                            'contact_id' => $contact->id,
                            'company_id' => $company->id,
                            'message_type' => '1',
                            'status' => $status,
                            'sender_name' => $company->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'campaign_id' => $newCampaignId,
                        ]);
                    } else {
                        // Collect error responses
                        $results[] = [
                            'contact' => $contact->phone,
                            'status' => 'error',
                            'error_message' => $response->body(),
                            'http_status' => $response->status(),
                        ];
                    }
                } catch (\Exception $e) {
                    // Catch any exceptions and store the error
                    $results[] = [
                        'contact' => $contact->phone,
                        'status' => 'exception',
                        'exception_message' => $e->getMessage(),
                    ];
                }
            }
        }

        return redirect()->route('campaigns.index')->withStatus(__('Campaign is ready to be send'));
    }

    public function orderIndex(Request $request)
    {
        if (
            $this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' ||
            $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes'
        ) {
            return redirect(route('whatsapp.setup'));
        }

        $company = $this->getCompany();

        // Base query
        $query = Order::where('company_id', $company->id)
            ->orderBy('created_at', 'DESC');

        // Apply search filters
        $this->applyFilters($query, $request);

        // Clone query for stats (without pagination)
        $allItems = (clone $query)->get();

        // Paginate results
        $orders = $query->paginate(config('settings.paginate'));

        // ---- Stats ----
        $totalOrders   = $orders->total();
        $paidOrders    = $allItems->filter(fn ($o) => strtolower($o->payment_status ?? '') === 'paid')->count();
        $pendingOrders = $allItems->where('status', 'order')->count();

        $totalRevenue   = 0;
        $totalShipping  = 0;
        $totalDiscount  = 0;

        foreach ($allItems->filter(fn ($o) => strtolower($o->payment_status ?? '') === 'paid') as $order) {
            $finalAmount = ($order->subtotal_offset ?? 1) != 0
                ? $order->subtotal_value / $order->subtotal_offset
                : 0;

            $shipping = $order->shipping_cast ?? 0;
            $discountType = $order->discount_type ?? null;
            $discountValue = $order->discount ?? 0;
            $discountAmount = 0;

            if ($discountType && $discountValue > 0) {
                if ($discountType === 'percent') {
                    $discountAmount = ($finalAmount * $discountValue) / 100;
                } else {
                    $discountAmount = min($discountValue, $finalAmount);
                }
            }

            $totalRevenue  += $finalAmount - $discountAmount + $shipping;
            $totalShipping += $shipping;
            $totalDiscount += $discountAmount;
        }


        return view($this->view_path . 'order/index', [
            'setup' => [
                'title'         => __('Catalog Order Management'),
                'subtitle'      => __('Manage and track your orders'),
                'items'         => $orders,
                'item_names'    => __('Orders'),
                'iscontent'     => true, // Use cardbody instead of table
                'custom_table'  => true,
                'parameters'    => count(request()->all()) != 0,
            ],
            'stats' => [
                'totalOrders'   => $totalOrders,
                'paidOrders'    => $paidOrders,
                'pendingOrders' => $pendingOrders,
                'totalRevenue'  => $totalRevenue,
                'totalShipping' => $totalShipping,
                'totalDiscount' => $totalDiscount,
            ],
        ]);
    }


    private function applyFilters($query, $request)
    {
        // 1. Simple search (orders, customers, phone)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('reference_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('user_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere(function ($q) use ($searchTerm) {
                        $this->applyPhoneSearch($q, $searchTerm);
                    });
            });
        }

        // 2. Advanced filters
        // Phone filter
        if ($request->filled('phone')) {
            $query->where(function ($q) use ($request) {
                $this->applyPhoneSearch($q, $request->phone);
            });
        }

        // Order ID filter
        if ($request->filled('order_id')) {
            $query->where('reference_id', 'like', '%' . $request->order_id . '%');
        }

        // Customer name filter
        if ($request->filled('customer_name')) {
            $query->where('user_name', 'like', '%' . $request->customer_name . '%');
        }

        // Payment status filter with transaction type support
        if ($request->filled('payment_status')) {
            if ($request->payment_status == 'paid') {
                $query->where('payment_status', 'paid');

                // Add transaction type filter if provided
                if ($request->filled('transaction_type')) {
                    $query->where('transaction_type', $request->transaction_type);
                }
            } elseif ($request->payment_status == 'unpaid') {
                $query->whereNull('transaction_id');
            } elseif ($request->payment_status == 'failed') {
                $query->where('payment_status', 'failed');
            } elseif ($request->payment_status == 'refunded') {
                $query->where('payment_status', 'refunded');
            }
        }

        // Handle pending status as default for empty payment_status
        if (!$request->filled('payment_status') && $request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Order status filter (simplified)
        if ($request->filled('order_status')) {
            $query->where('status', $request->order_status);
        } else {
            $query->whereNotIn('status', ['canceled']);
        }
    }

    private function applyPhoneSearch($query, $phone)
    {
        // Remove all non-digit characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $query->where(DB::raw("REPLACE(REPLACE(REPLACE(phone_number, ' ', ''), '-', ''), '+', '')"), 'LIKE', '%' . $cleanPhone . '%')->orWhere('phone_number', 'like', '%' . $phone . '%');
    }

    public function itemIndex($id)
    {
        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }
        $company = $this->getCompany();
        $orders = OrderItem::where('order_id', $id)->paginate(config('settings.paginate'));
        $order = Order::where('id', $id)->first();
        $deliveryAgents = DeliveryAgent::where('company_id', $company->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $Paymenttemplate = Paymenttemplate::where('company_id', $company->id)->first();
        $products = CatalogProduct::where('company_id', $company->id)->get();
        $freezePricing = false;
        // Get last contact reply time
        $contact = Contact::where('company_id', $company->id)->where('phone', $order->phone_number)->whereNull('deleted_at')->orderBy('last_client_reply_at', 'desc')->first();

        $windowStatus = null;
        $isFreeWindowExpired = false;
        if ($contact && $contact->last_client_reply_at) {
            $lastReply = Carbon::parse($contact->last_client_reply_at);
            $windowEnd = $lastReply->addHours(24);
            $now = Carbon::now();

            if ($now->lt($windowEnd)) {
                $windowStatus = [
                    'remaining' => $now->diff($windowEnd),
                    'expires_at' => $windowEnd,
                ];
            } else {
                $windowStatus = 'expired';
                $isFreeWindowExpired = true;
            }
        }
        return view(
            $this->view_path . 'order/item',
            [
                'setup' => [
                    'title' => 'Order Detail #' . $order['reference_id'] . '',
                    'items' => $orders,
                    'item_names' => '',
                    'webroute_path' => '',
                    'fields' => [],
                    'custom_table' => true,
                    'parameter_name' => '',
                    'parameters' => count($_GET) != 0,
                ],
            ],
            [
                'order' => $order,
                'products' => $products,
                'Paymenttemplate' => $Paymenttemplate,
                'windowStatus' => $windowStatus,
                'freezePricing' => $freezePricing,
                'lastContactReply' => $contact->last_client_reply_at ?? null,
                'isFreeWindowExpired' => $isFreeWindowExpired,
                'deliveryAgents' => $deliveryAgents,
            ],
        );
    }

    public function assignDeliveryAgent(Request $request, Order $order)
    {
        $company = $this->getCompany();

        $validated = $request->validate([
            'delivery_agent_id' => 'nullable|exists:delivery_agents,id',
        ]);

        if (!empty($validated['delivery_agent_id'])) {
            $agent = DeliveryAgent::where('id', $validated['delivery_agent_id'])
                ->where('company_id', $company->id)
                ->first();

            if (!$agent) {
                return redirect()
                    ->back()
                    ->with('error', __('Selected delivery agent is not available.'));
            }

            $order->delivery_agent_id = $agent->id;
            // Keep delivery_partner in sync for existing views/exports
            $order->delivery_partner = $agent->name;
        } else {
            $order->delivery_agent_id = null;
        }

        $order->save();

        return redirect()
            ->back()
            ->with('success', __('Delivery agent updated successfully.'));
    }

    public function itemEdit($id)
    {
        $orders = OrderItem::where('order_id', $id)->paginate(config('settings.paginate'));
        $order = Order::where('id', $id)->first();

        return view(
            $this->view_path . 'order/edit',
            [
                'setup' => [
                    'title' => __('Edit Delivery Address'),
                    'subtitle' => __('Order') . ' #' . ($order->reference_id ?? ''),
                    'items' => $orders,
                    'item_names' => '',
                    'webroute_path' => '',
                    'fields' => [],
                    'custom_table' => true,
                    'iscontent' => true,
                    'parameter_name' => '',
                    'parameters' => count($_GET) != 0,
                ],
                'order' => $order,
            ],
        );
    }

    public function orderUpdate(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();
        if ($order) {
            $order->address = $request->address;
            $order->city = $request->city;
            $order->pin_code = $request->pin_code;
            $order->building_name = $request->building_name;
            $order->house_number = $request->house_number;
            $order->landmark_area = $request->landmark_area;
            $order->tower_number = $request->tower_number;
            $order->state = $request->state;
            $order->update();
            return redirect()->route('catalog.itemIndex', $order->id)->with('success', __('Address updated successfully'));
        }
        return redirect()->route('catalog.orderIndex');
    }

    // public function pdf($id)
    // {
    //     $orderItems = OrderItem::where('order_id', $id)->paginate(config('settings.paginate'));
    //     $order = Order::where('id', $id)->first();
    //     require_once base_path('public/tcpdf/tcpdf.php');

    //     // Create new PDF document
    //     $pdf = new \TCPDF();

    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetAuthor('Your Company Name');
    //     $pdf->SetTitle('Order - #' . $order['reference_id']);
    //     $pdf->SetSubject('Invoice PDF');

    //     // Disable default header and footer
    //     $pdf->setPrintHeader(false);
    //     $pdf->setPrintFooter(false);

    //     $pdf->SetFont('dejavusans', '', 12);

    //     // Add a new page
    //     $pdf->AddPage();

    //     // Prepare organization and invoice information
    //     $info_left_column = '<img src="https://sendinai.com/icon_dark.svg" height="50">';
    //     $info_right_column =
    //         '<div style="white-space: nowrap;">
    //                             <span style="font-size: 20px; margin: 0; display: inline;"><b>ORDER</b></span><br>
    //                             <span>#' .
    //         $order['reference_id'] .
    //         '</span>
    //                         </div>';

    //     $organization_info = '<p><b>Billing Address</b><br><span style="font-size:10px;">Address: ' . $order['address'] . '</span><br><span style="font-size:10px;">State: ' . $order['state'] . '</span><br><span style="font-size:10px;">City: ' . $order['city'] . '</span><br><span style="font-size:10px;">Pin Code: ' . $order['pin_code'] . '</span></p>';
    //     $invoice_info = '<p><b>User Detail</b><br><span style="font-size:10px;">Name: ' . $order['user_name'] . '</span><br><span style="font-size:10px;">Phone No: ' . $order['phone_number'] . '</span><br><span style="font-size:10px;">Currency: ' . $order['currency'] . '</span><br><span style="font-size:10px;">Date: ' . $order['created_at'] . '</span></p>';

    //     // Render header
    //     $pdf->writeHTML(
    //         '<table>
    //                         <tr>
    //                             <td>' .
    //             $info_left_column .
    //             '</td>
    //                             <td  style="text-align: right;">' .
    //             $info_right_column .
    //             '</td>
    //                         </tr>
    //                     </table>',
    //     );
    //     $pdf->Ln(2);

    //     // Render billing and shipping info
    //     $pdf->writeHTML(
    //         '<table>
    //                         <tr>
    //                             <td>' .
    //             $invoice_info .
    //             '</td>
    //                             <td style="text-align: right">' .
    //             $organization_info .
    //             '</td>
    //                         </tr>
    //                     </table>',
    //     );
    //     $pdf->Ln(2);

    //     // Table Header
    //     $tbl = '
    //     <table cellspacing="0" cellpadding="5">
    //         <thead>
    //             <tr style="background-color:#040404;color: white;font-size: 9px;font-family: serif;">
    //                 <th><b>S.No</b></th>
    //                 <th align="right"><b>Product</b></th>
    //                 <th align="right"><b>Quantity</b></th>
    //                 <th align="right"><b>Price</b></th>
    //                 <th align="right"><b>Total</b></th>
    //             </tr>
    //         </thead>
    //         <tbody>';

    //     // Loop through invoice items and render them
    //     foreach ($orderItems as $key => $item) {
    //         $catalogproduct = CatalogProduct::where('retailer_id', $item->retailer_id)->first();
    //         $tbl .=
    //             '
    //         <tr style="font-size: 9px;">
    //             <td>' .
    //             $key +
    //             1 .
    //             '</td>
    //             <td align="right">' .
    //             ($catalogproduct->name ?? '') .
    //             '</td>
    //             <td align="right">' .
    //             $item->quantity .
    //             '</td>
    //             <td align="right">₹' .
    //             $item->amount_value / $item->amount_offset .
    //             '</td>
    //             <td align="right">₹' .
    //             ($item->amount_value / $item->amount_offset) * $item->quantity .
    //             '</td>
    //         </tr>';
    //     }

    //     // Add shipment and totals
    //     $tbl .=
    //         '<br><br>
    //         <tr style="font-size: 10px;">
    //             <td colspan="7" align="right"><b>Total: ₹' .
    //         $order->subtotal_value / $order->subtotal_offset .
    //         '</b></td>
    //         </tr>';
    //     $tbl .= '</tbody></table>';

    //     // Write table to PDF
    //     $pdf->writeHTML($tbl, true, false, false, false, '');

    //     // Add signature and footer
    //     $pdf->Ln(10);
    //     // $pdf->writeHTML('<p><b>Authorized Signature</b></p><p>www.kisaankhata.com | +91-12345-67890 | your-email@example.com</p>');

    //     // Output the PDF to the browser
    //     return response()->streamDownload(function () use ($pdf) {
    //         $pdf->Output('invoice_' . 'example' . '.pdf', 'I');
    //     }, 'invoice.pdf');
    // }

    public function pdf($id)
    {
        $size = request('size', 'full'); // default to 'full' if not present
        $currencySymbol = request('currency', '₹');

        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $id)->get();
        $companyId = $order->company_id;

        $paymentConfig = Paymenttemplate::where('company_id', $companyId)->first();
        if (!$paymentConfig) {
            $paymentConfig = new Paymenttemplate();
            $paymentConfig->business_name = config('app.name');
        }

        if (!class_exists(\TCPDF::class)) {
            return redirect()
                ->route('catalog.orderIndex')
                ->with('error', __('Invoice library (TCPDF) is not installed. Please contact support.'));
        }

        // Preload catalog products efficiently
        $retailerIds = $orderItems->pluck('retailer_id')->unique();
        $catalogProducts = CatalogProduct::whereIn('retailer_id', $retailerIds)->get()->keyBy('retailer_id');

        // Set page size based on parameter
        if ($size === 'thermal') {
            $baseHeight = 150; // header + spacing
            $rowHeight = 6;
            $numItems = $orderItems->count();
            $pageHeight = $baseHeight + $numItems * $rowHeight + 60;

            $pageSize = [80, $pageHeight]; // Thermal width (80mm) with dynamic height
            $fontSize = 8;
            $smallFontSize = 6;
        } else {
            $pageSize = PDF_PAGE_FORMAT; // A4
            $fontSize = 10;
            $smallFontSize = 8;
        }

        // Create new PDF document
        $pdf = new \TCPDF('P', 'mm', $pageSize, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($paymentConfig->business_name);
        $pdf->SetTitle('Invoice - #' . $order->reference_id);
        $pdf->SetSubject('Invoice');

        // Disable default header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        if ($size === 'thermal') {
            $pdf->SetMargins(4, 4, 4); // Smaller margins for thermal
        } else {
            $pdf->SetMargins(15, 15, 15);
        }
        $pdf->SetAutoPageBreak(true, 15);

        // Set font that supports currency symbols
        $pdf->SetFont('dejavusans', '', $fontSize);
        // Add a page
        $pdf->AddPage();

        // Company and invoice header
        //  <td width="60%">
        //         <img src="' . (asset('uploads/storefront/' . $paymentConfig->logo)) . '" height="' .
        //     ($size === 'thermal' ? '30' : '40') .
        //     '">
        //         <br>
        $header =
            '
    <table border="0" cellpadding="2" cellspacing="0">
        <tr>
            <td width="60%">
                <img src="' .
            asset('uploads/storefront/' . $paymentConfig->logo) .
            '" height="' .
            ($size === 'thermal' ? '30' : '40') .
            '">
            <span style="font-size:' .
            $smallFontSize .
            'px;">
                    ' .
            ($paymentConfig->business_address ?? '') .
            '<br>Phone: ' .
            ($paymentConfig->business_phone ?? '') .
            '<br>WhatsApp number: ' .
            ($paymentConfig->business_whatsapp ?? '') .
            '<br>Email: ' .
            ($paymentConfig->business_email ?? '') .
            '
                </span>
            </td>
            <td width="40%" align="right">
                <h1 style="font-size:' .
            ($size === 'thermal' ? '14' : '18') .
            'px; margin:0; color:#333;">INVOICE</h1>
                <span style="font-size:' .
            $smallFontSize .
            'px;">
                    <strong>Invoice #:</strong> ' .
            $order->reference_id .
            '<br>
                    <strong>Date:</strong> ' .
            $order->created_at->format('M d, Y') .
            '<br>
                    <strong>Status:</strong> ' .
            ucfirst($order->status) .
            '<br>
                    <strong>Currency:</strong> ' .
            htmlspecialchars($currencySymbol) .
            '
                </span>
            </td>
        </tr>
    </table>
    <hr style="border:0.5px solid #eee; margin:10px 0;">';

        $pdf->writeHTML($header, true, false, false, false, '');

        // Billing and shipping information
        $addressInfo =
            '
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr>
            <td width="50%" style="border-right:1px solid #eee;">
                <h3 style="font-size:' .
            ($fontSize + 2) .
            'px; margin:0 0 5px 0;">Bill To:</h3>
                <p style="font-size:' .
            $smallFontSize .
            'px; margin:0;">
                    <strong>' .
            $order->user_name .
            '</strong><br>
                    ' .
            $order->address .
            '<br>
                    ' .
            $order->city .
            ', ' .
            $order->state .
            ' ' .
            $order->pin_code .
            '<br>
                    Phone: ' .
            $order->phone_number .
            '<br>
                    Email: ' .
            ($order->email ?? 'N/A') .
            '
                </p>
            </td>
            <td width="50%" style="padding-left:10px;">
                <h3 style="font-size:' .
            ($fontSize + 2) .
            'px; margin:0 0 5px 0;">Shipping To:</h3>
                <p style="font-size:' .
            $smallFontSize .
            'px; margin:0;">
                    <strong>' .
            ($order->for_person ?? $order->user_name) .
            '</strong><br>
                    ' .
            ($order->shipping_address ?? $order->address) .
            '<br>
                    ' .
            ($order->shipping_city ?? $order->city) .
            ', ' .
            ($order->shipping_state ?? $order->state) .
            ' ' .
            ($order->shipping_pin_code ?? $order->pin_code) .
            '<br>
                    Phone: ' .
            ($order->for_person_number ?? $order->shipping_phone) .
            '
                </p>
            </td>
        </tr>
    </table>
    <hr style="border:0.5px solid #eee; margin:10px 0;">';

        $pdf->writeHTML($addressInfo, true, false, false, false, '');

        // Invoice items table
        $tbl =
            '
    <table border="0" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr style="background-color:#f8f8f8; color:#333;">
                <th width="10%" style="text-align:left; border-bottom:1px solid #ddd; font-size:' .
            $smallFontSize .
            'px;"><strong>#</strong></th>
                <th width="40%" style="text-align:left; border-bottom:1px solid #ddd; font-size:' .
            $smallFontSize .
            'px;"><strong>Item</strong></th>
                <th width="15%" style="text-align:right; border-bottom:1px solid #ddd; font-size:' .
            $smallFontSize .
            'px;"><strong>Price</strong></th>
                <th width="15%" style="text-align:right; border-bottom:1px solid #ddd; font-size:' .
            $smallFontSize .
            'px;"><strong>Qty</strong></th>
                <th width="20%" style="text-align:right; border-bottom:1px solid #ddd; font-size:' .
            $smallFontSize .
            'px;"><strong>Total</strong></th>
            </tr>
        </thead>
        <tbody>';

        $subtotal = 0;
        foreach ($orderItems as $key => $item) {
            $price = $item->amount_offset != 0 ? $item->amount_value / $item->amount_offset : 0;
            $total = $price * $item->quantity;
            $subtotal += $total;

            // Get product name safely
            $productName = 'Product';
            if (isset($catalogProducts[$item->retailer_id])) {
                $productName = $catalogProducts[$item->retailer_id]->product_name;
            }

            $tbl .=
                '
        <tr>
            <td width="10%" style="text-align:left; border-bottom:1px solid #eee; font-size:' .
                $smallFontSize .
                'px;">' .
                ($key + 1) .
                '</td>
            <td width="40%" style="text-align:left; border-bottom:1px solid #eee; font-size:' .
                $smallFontSize .
                'px;">' .
                $productName .
                '</td>
            <td width="15%" style="text-align:right; border-bottom:1px solid #eee; font-size:' .
                $smallFontSize .
                'px;">' .
                htmlspecialchars($currencySymbol) .
                ' ' .
                number_format($price, 2) .
                '</td>
            <td width="15%" style="text-align:right; border-bottom:1px solid #eee; font-size:' .
                $smallFontSize .
                'px;">' .
                $item->quantity .
                '</td>
            <td width="20%" style="text-align:right; border-bottom:1px solid #eee; font-size:' .
                $smallFontSize .
                'px;">' .
                htmlspecialchars($currencySymbol) .
                ' ' .
                number_format($total, 2) .
                '</td>
        </tr>';
        }

        // ====== CALCULATIONS FOR NEW FIELDS ======
        // Get shipping cost (show only if not null and > 0)
        $shippingCost = $order->shipping_cast ?? 0;
        $shippingLabel = '(' . $paymentConfig->shipping_description . ')';

        // Tax calculation (unchanged)
        $tax = $order->tax_offset != 0 ? $order->tax_value / $order->tax_offset : 0;

        // Discount handling
        $discountAmount = 0;
        $discountLabel = 'Discount';
        if ($order->discount !== null) {
            $discountAmount = $order->discount_amount ?? 0;
            // Add discount type if available
            if ($order->discount_type == 'percent') {
                $discountLabel = 'Discount (' . ucfirst($order->discount_type) . ') ' . $order->discount . '%';
            } else {
                $discountLabel = 'Discount (' . ucfirst($order->discount_type) . ') ';
            }
        }

        // Calculate final amount
        if ($order->final_amount !== null) {
            $grandTotal = $order->final_amount;
        } else {
            // Calculate total if final_amount isn't provided
            $grandTotal = $subtotal + $shippingCost + $tax - $discountAmount;
        }
        // ====== END OF CALCULATIONS ======

        // Add summary
        $tbl .=
            '
        <tr><td colspan="5"></td></tr>
        <tr>
            <td colspan="4" style="text-align:right; font-size:' .
            $smallFontSize .
            'px;"><strong>Subtotal:</strong></td>
            <td style="text-align:right; font-size:' .
            $smallFontSize .
            'px;">' .
            htmlspecialchars($currencySymbol) .
            ' ' .
            number_format($subtotal, 2) .
            '</td>
        </tr>';

        // ====== SHIPPING COST (SHOW ONLY IF NOT NULL) ======
        if ($shippingCost > 0) {
            $tbl .=
                '
        <tr>
            <td colspan="4" style="text-align:right; font-size:' .
                $smallFontSize .
                'px;"><strong>Shipping:' .
                $shippingLabel .
                '</strong></td>
            <td style="text-align:right; font-size:' .
                $smallFontSize .
                'px;">' .
                htmlspecialchars($currencySymbol) .
                ' ' .
                number_format($shippingCost, 2) .
                '</td>
        </tr>';
        }

        if ($tax > 0) {
            $tbl .=
                '
        <tr>
            <td colspan="4" style="text-align:right; font-size:' .
                $smallFontSize .
                'px;"><strong>Tax:</strong></td>
            <td style="text-align:right; font-size:' .
                $smallFontSize .
                'px;">' .
                htmlspecialchars($currencySymbol) .
                ' ' .
                number_format($tax, 2) .
                '</td>
        </tr>';
        }

        // ====== DISCOUNT HANDLING (SHOW ONLY IF DISCOUNT EXISTS) ======
        if ($order->discount > 0) {
            if ($order->discount !== null) {
                $tbl .=
                    '
            <tr>
                <td colspan="4" style="text-align:right; font-size:' .
                    $smallFontSize .
                    'px;"><strong>' .
                    $discountLabel .
                    ':</strong></td>
                <td style="text-align:right; font-size:' .
                    $smallFontSize .
                    'px;">-' .
                    htmlspecialchars($currencySymbol) .
                    ' ' .
                    number_format($discountAmount, 2) .
                    '</td>
            </tr>';
            }
        }

        $tbl .=
            '
        <tr>
            <td colspan="4" style="text-align:right; font-size:' .
            ($smallFontSize + 1) .
            'px; border-top:1px solid #ddd; padding-top:5px;"><strong>Grand Total:</strong></td>
            <td style="text-align:right; font-size:' .
            ($smallFontSize + 1) .
            'px; border-top:1px solid #ddd; padding-top:5px;"><strong>' .
            htmlspecialchars($currencySymbol) .
            ' ' .
            number_format($grandTotal, 2) .
            '</strong></td>
        </tr>
    </tbody>
    </table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');

        // Payment method and notes
        $footer =
            '
    <table border="0" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <td width="50%" style="font-size:' .
            $smallFontSize .
            'px;">
                <h3 style="font-size:' .
            ($fontSize + 1) .
            'px; margin:0 0 5px 0;">Payment Method:</h3>
                <p style="margin:0;">' .
            ucfirst($order->payment_method ?? 'N/A') .
            '</p>
                <p style="margin:10px 0 0 0;"><strong>Status:</strong> ' .
            ucfirst($order->payment_status ?? 'Pending') .
            '</p>
            </td>
            <td width="50%" style="font-size:' .
            $smallFontSize .
            'px;">
                <h3 style="font-size:' .
            ($fontSize + 1) .
            'px; margin:0 0 5px 0;">Notes:</h3>
                <p style="margin:0;">Thank you for your business!</p>
                <p style="margin:10px 0 0 0;">All amounts in ' .
            htmlspecialchars($currencySymbol) .
            '</p>
            </td>
        </tr>
    </table>
    <hr style="border:0.5px solid #eee; margin:10px 0;">
    <p style="text-align:center; font-size:' .
            ($smallFontSize - 1) .
            'px; color:#888;">
        If you have any questions about this invoice, please contact<br>
        ' .
            config('app.email') .
            ' or call ' .
            config('app.phone') .
            '
    </p>';

        $pdf->writeHTML($footer, true, false, false, false, '');

        // Output the PDF
        $filename = 'invoice_' . $order->reference_id . '.pdf';

        return response()->streamDownload(function () use ($pdf, $filename) {
            $pdf->Output($filename, 'I');
        }, $filename);
    }

    public function printReceipt($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $id)->get();

        // Preload product names
        $retailerIds = $orderItems->pluck('retailer_id')->unique();
        $catalogProducts = CatalogProduct::whereIn('retailer_id', $retailerIds)->get()->keyBy('retailer_id');

        if (!class_exists(\TCPDF::class)) {
            return redirect()
                ->route('catalog.orderIndex')
                ->with('error', __('Receipt library (TCPDF) is not installed. Please contact support.'));
        }

        // Calculate page height dynamically
        $baseHeight = 50; // header + spacing
        $rowHeight = 6;
        $numItems = $orderItems->count();
        $pageHeight = $baseHeight + $numItems * $rowHeight + 20;
        $pageSize = [80, $pageHeight]; // mm

        $pdf = new \TCPDF('P', 'mm', $pageSize, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(4, 4, 4);
        $pdf->SetAutoPageBreak(true, 4);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->AddPage();

        // === HTML Receipt Layout ===
        $html =
            '
    <style>
        .title { text-align:center; font-size:10px; font-weight:bold; }
        .info { font-size:8px; line-height:1.5; margin-bottom: 4px; }
        .line { border-top: 0.5px dashed #000; margin: 4px 0; }
        .item-row td { padding: 2px 0; border-bottom: 0.5px dotted #ccc; }
        .footer { text-align:center; font-size:7px; margin-top:8px; }
    </style>

    <div class="title">ORDER RECEIPT</div>
    <div class="line"></div>

    <div class="info">
        <strong>Order #:</strong> ' .
            $order->reference_id .
            '<br>
        <strong>Date:</strong> ' .
            $order->created_at->format('d M Y, h:i A') .
            '<br>
        <strong>Customer:</strong> ' .
            $order->user_name .
            '<br>
        <strong>Phone:</strong> ' .
            $order->phone_number .
            '
    </div>

    <div class="line"></div>

    <table width="100%" cellspacing="0" cellpadding="0">';

        foreach ($orderItems as $key => $item) {
            $product = $catalogProducts[$item->retailer_id] ?? null;
            $name = $product ? $product->product_name : 'Product #' . ($key + 1);

            $html .=
                '
        <tr class="item-row">
            <td width="80%">' .
                $name .
                '</td>
            <td width="20%" align="right">x' .
                $item->quantity .
                '</td>
        </tr>';
        }

        $html .= '</table>
    <div class="line"></div>
    <div class="footer">Thank you for your order!</div>';

        $pdf->writeHTML($html, true, false, false, false, '');

        $filename = 'receipt_' . $order->reference_id . '.pdf';

        return response()->streamDownload(function () use ($pdf, $filename) {
            $pdf->Output($filename, 'I');
        }, $filename);
    }

    public function settingUpdatecc(Request $request)
    {
        // dd($request->all());

        $company = $this->getCompany();
        $companyId = $company->id;
        $Paymenttemplate = Paymenttemplate::where('company_id', $companyId)->first();
        if ($Paymenttemplate) {
            if ($request->body) {
                $Paymenttemplate->body = $request->body;
                $Paymenttemplate->footer = $request->footer;
                if ($request->payment_configuration) {
                    $Paymenttemplate->payment_configuration = $request->payment_configuration;
                }
                if ($request->payment_configuration_other) {
                    $Paymenttemplate->payment_configuration_other = $request->payment_configuration_other;
                }
            }
            if ($request->address_mess) {
                $Paymenttemplate->address_mess = $request->address_mess;
                $Paymenttemplate->product_id = $request->product_id;
            }
            if ($request->order_message) {
                $Paymenttemplate->order_message = $request->order_message;
            }
            if ($request->shipping) {
                $Paymenttemplate->shipping = $request->shipping;
                $Paymenttemplate->shipping_description = $request->shipping_description;
                $Paymenttemplate->shipping_free_from_amount = $request->shipping_free_from_amount;
                $Paymenttemplate->isShippingFree = $request->isShippingFree;
            }

            if ($request->payment_type == 0) {
                $Paymenttemplate->payment_type = 0;
            } else {
                $Paymenttemplate->payment_type = 1;
            }
            $Paymenttemplate->whatsapp_number = $request->whatsapp_number;
            $Paymenttemplate->update();
        } else {
            $PaymenttemplateSave = new Paymenttemplate();
            $PaymenttemplateSave->body = $request->body;
            $PaymenttemplateSave->footer = $request->footer;
            $PaymenttemplateSave->payment_configuration = $request->payment_configuration;
            $PaymenttemplateSave->payment_configuration_other = $request->payment_configuration_other;

            $PaymenttemplateSave->address_mess = $request->address_mess;
            $PaymenttemplateSave->product_id = $request->product_id;
            $PaymenttemplateSave->whatsapp_number = $request->whatsapp_number;
            $PaymenttemplateSave->company_id = $companyId;
            $PaymenttemplateSave->shipping = $request->shipping;
            $PaymenttemplateSave->shipping_description = $request->shipping_description;
            $Paymenttemplate->shipping_free_from_amount = $request->shipping_free_from_amount;
            $Paymenttemplate->isShippingFree = $request->isShippingFree;
            $PaymenttemplateSave->order_message = $request->order_message;
            $PaymenttemplateSave->save();
        }

        return redirect()->back();
    }

    public function whatsappPhoneUpdate(Request $request)
    {
        // dd($request->all());

        // $request->validate([
        //     'whatsapp_number' => 'required|digits:10',
        // ]);

        $company = $this->getCompany();
        $companyId = $company->id;

        $config = Config::update(['model_id' => $companyId, 'key' => 'whatsapp_phone'], ['value' => $request->whatsapp_number]);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

    // -----------------------------------------------------
    // enable and disable address message and payment method
    public function toggleAddressMessage(Request $request)
    {
        $company = $this->getCompany();
        $companyId = $company->id;
        $Paymenttemplate = Paymenttemplate::where('company_id', $companyId)->first();

        if ($request->has('address_message_enable')) {
            $Paymenttemplate->address_message_enable = $request->input('address_message_enable');
        } else {
            $Paymenttemplate->address_message_enable = 0;
        }
        $Paymenttemplate->save();
        return response()->json(['success' => true]);
    }

    public function togglePaymentMethod(Request $request)
    {
        $company = $this->getCompany();
        $companyId = $company->id;
        $Paymenttemplate = Paymenttemplate::where('company_id', $companyId)->first();

        if ($request->has('payment_method_enable')) {
            $Paymenttemplate->payment_method_enable = $request->input('payment_method_enable');
        } else {
            $Paymenttemplate->payment_method_enable = 0;
        }

        $Paymenttemplate->save();
        return response()->json(['success' => true]);
    }

    public function storefrontUpdate(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }

    public function templatesUpdate(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }

    public function sendDefaultTemplate($id)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }

    public function carouselTemplatesCreate()
    {
        $languages = 'Afrikaans,af,Albanian,sq,Arabic,ar,Azerbaijani,az,Bengali,bn,Bulgarian,bg,Catalan,ca,Chinese (CHN),zh_CN,Chinese (HKG),zh_HK,Chinese (TAI),zh_TW,Croatian,hr,Czech,cs,Danish,da,Dutch,nl,English,en,English (UK),en_GB,English (US),en_US,Estonian,et,Filipino,fil,Finnish,fi,French,fr,Georgian,ka,German,de,Greek,el,Gujarati,gu,Hausa,ha,Hebrew,he,Hindi,hi,Hungarian,hu,Indonesian,id,Irish,ga,Italian,it,Japanese,ja,Kannada,kn,Kazakh,kk,Kinyarwanda,rw_RW,Korean,ko,Kyrgyz (Kyrgyzstan),ky_KG,Lao,lo,Latvian,lv,Lithuanian,lt,Macedonian,mk,Malay,ms,Malayalam,ml,Marathi,mr,Norwegian,nb,Persian,fa,Polish,pl,Portuguese (BR),pt_BR,Portuguese (POR),pt_PT,Punjabi,pa,Romanian,ro,Russian,ru,Serbian,sr,Slovak,sk,Slovenian,sl,Spanish,es,Spanish (ARG),es_AR,Spanish (SPA),es_ES,Spanish (MEX),es_MX,Swahili,sw,Swedish,sv,Tamil,ta,Telugu,te,Thai,th,Turkish,tr,Ukrainian,uk,Urdu,ur,Uzbek,uz,Vietnamese,vi,Zulu,zuv';
        $languages = explode(',', $languages);
        $languages = array_chunk($languages, 2);
        $company = $this->getCompany();
        $companyId = $company->id;
        $products = CatalogProduct::where('company_id', $companyId)->get();
        return view($this->view_path . 'carousel_templates_create', compact('languages', 'products'));
    }

    public function categoryIndex()
    {
        $this->authChecker();

        if (
            $this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes'
            || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes'
        ) {
            return redirect(route('whatsapp.setup'));
        }

        $company = $this->getCompany();
        $companyId = $company->id;

        // Categories filtered correctly
        $orders = ProductCategory::where('company_id', $companyId)
            ->paginate(config('settings.paginate'));

        // FIX → Filter products by company ID
        $products = CatalogProduct::where('company_id', $companyId)->get();

        return view($this->view_path . 'category/index', [
            'setup' => [
                'title' => __('Categories Management'),
                'subtitle' => __('Organize your products into categories'),
                'action_link' => route('catalog.categoryCreate'),
                'action_name' => __('Add Category'),
                'items' => $orders,
                'item_names' => __('Categories'),
                'products' => $products,
                'fields' => [],
                'custom_table' => true,
                'parameter_name' => 'category',
            ],
        ]);
    }


    public function categoryCreate()
    {
        $company = $this->getCompany();
        $companyId = $company->id;
        $products = CatalogProduct::where('company_id', $companyId)->get();
        return view($this->view_path . 'category/create', compact('products'));
    }

    public function categoryStore(Request $request)
    {
        $company = $this->getCompany();
        $companyId = $company->id;
        $productCategory = new ProductCategory();
        $productCategory->name = $request->name;
        $productCategory->company_id = $companyId;
        if ($request->product) {
            $productCategory->retailer_id = implode(',', $request->product) ?? '';
        }
        $productCategory->save();
        return redirect()->route('catalog.categoryIndex');
    }

    public function categoryEdit($id)
    {
        $productcategory = ProductCategory::findOrFail($id);
        $company = $this->getCompany();

        $products = CatalogProduct::where('company_id', $company->id)->get();

        return view($this->view_path . 'category/edit', compact('products', 'productcategory'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $company = $this->getCompany();
        $companyId = $company->id;
        $productCategory = ProductCategory::where('id', $id)->first();
        $productCategory->name = $request->name;
        $productCategory->company_id = $companyId;
        if ($request->product) {
            $productCategory->retailer_id = implode(',', $request->product) ?? '';
        }
        $productCategory->update();
        return redirect()->route('catalog.categoryIndex');
    }

    public function productEdit($id)
    {
        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }

        $company = $this->getCompany();
        $companyId = $company->id;
        $productcategory = ProductCategory::where('company_id', $companyId)->get();
        $CatalogProduct = CatalogProduct::where('id', $id)->first();
        return view($this->view_path . 'catalog-product-edit', compact('productcategory', 'CatalogProduct'));
    }

    public function inventoryIndex(Request $request)
    {
        $this->authChecker();
        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }
        $company = $this->getCompany();
        $company_id = $company->id;
        $catalogIds = Catalog::where('company_id', $company_id)->where('status', 1)->pluck('catalog_id')->toArray();
        $query = CatalogProduct::where('company_id', $company_id)->whereIn('catalog_id', $catalogIds);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('retailer_id', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('stock_status') && $request->stock_status !== '') {
            $query->where('stock_status', (int) $request->stock_status);
        }
        $products = $query->orderBy('product_name')->paginate(15)->withQueryString();
        return view($this->view_path . 'inventory.index', [
            'setup' => [
                'title' => __('Inventory Management'),
                'subtitle' => __('Track and adjust product stock levels'),
                'items' => $products,
                'item_names' => __('Products'),
                'custom_table' => true,
            ],
            'products' => $products,
            'company_id' => $company_id,
        ]);
    }

    public function inventoryAdjust(Request $request)
    {
        $this->authChecker();
        $request->validate([
            'product_id' => 'required|integer|exists:catalog_products,id',
            'adjustment' => 'required|integer',
            'type' => 'required|in:in,out',
        ]);
        $product = CatalogProduct::findOrFail($request->product_id);
        if ($product->company_id != $this->getCompany()->id) {
            abort(403);
        }
        if (!Schema::hasColumn('catalog_products', 'stock_quantity')) {
            return redirect()->route('catalog.inventoryIndex')->with('error', __('Please run migrations to enable inventory features.'));
        }
        $currentQty = (int) ($product->stock_quantity ?? 0);
        $adjustment = abs((int) $request->adjustment);
        if ($request->type === 'in') {
            $product->stock_quantity = $currentQty + $adjustment;
        } else {
            $product->stock_quantity = max(0, $currentQty - $adjustment);
        }
        $product->save();
        return redirect()->route('catalog.inventoryIndex')->with('success', __('Stock updated successfully'));
    }

    // public function productUpdate(Request $request){
    //     // return $request;
    //     $productCategory = ProductCategory::where('id',$request->product_id)->first();
    //     $productCategoryChack = ProductCategory::where('retailer_id', 'like', "%{$request->check_id}%")->where('id',$request->product_id)->first();
    //     if($productCategoryChack){
    //         return redirect()->route('catalog.productsCatalog')->with('success', 'Product updated successfully!');
    //     }
    //     else{
    //         $productCategory->retailer_id = implode(',',$request->product)??'';
    //         $productCategory->update();
    //         return redirect()->route('catalog.productsCatalog')->with('success', 'Product updated successfully!');
    //     }

    // }

    public function productUpdate(Request $request)
    {
        // Validate the request
        $request->validate([
            'categories' => 'required|array',
            'check_id' => 'required|string',
            'product_id' => 'required|integer|exists:catalog_products,id',
            'stock_status' => 'nullable|in:0,1',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        // Update stock status and quantity (only if columns exist)
        $product = CatalogProduct::findOrFail($request->product_id);
        if (Schema::hasColumn('catalog_products', 'stock_status') && isset($request->stock_status)) {
            $product->stock_status = (int) $request->stock_status;
        }
        if (Schema::hasColumn('catalog_products', 'stock_quantity') && array_key_exists('stock_quantity', $request->all())) {
            $product->stock_quantity = $request->stock_quantity ?: null;
        }
        $product->save();

        // Get the product's retailer_id
        $retailerId = $request->check_id;

        // Process each selected category
        foreach ($request->categories as $categoryId) {
            $category = ProductCategory::findOrFail($categoryId);

            // Split existing retailer_ids into array
            $currentRetailerIds = explode(',', $category->retailer_id);

            // Add the product's retailer_id if not already present
            if (!in_array($retailerId, $currentRetailerIds)) {
                $currentRetailerIds[] = $retailerId;
                $category->retailer_id = implode(',', array_filter($currentRetailerIds));
                $category->save();
            }
        }

        // Remove from unselected categories
        if (count($request->categories)) {
            ProductCategory::whereNotIn('id', $request->categories)
                ->where('retailer_id', 'like', "%{$retailerId}%")
                ->each(function ($category) use ($retailerId) {
                    $retailerIds = explode(',', $category->retailer_id);
                    $updatedIds = array_diff($retailerIds, [$retailerId]);
                    $category->retailer_id = implode(',', $updatedIds);
                    $category->save();
                });
        }

        return redirect()->route('catalog.productsCatalog')->with('success', 'Product categories updated successfully');
    }

    // public function sendOrderDispatch(Request $request, $order_id)
    // {
    //     $company = $this->getCompany();
    //     $companyId = $company->id;

    //     $order = Order::find($order_id);
    //     if (!$order) {
    //         return response()->json([
    //             'status' => false,
    //             'errMsg' => __('Order not found.'),
    //         ]);
    //     }

    //     $whatsappAccessToken = Config::where('model_id', $companyId)->where('key', 'whatsapp_permanent_access_token')->value('value');
    //     $whatsappPhoneNumberId = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone_number_id')->value('value');

    //     if (!$whatsappAccessToken || !$whatsappPhoneNumberId) {
    //         return response()->json([
    //             'status' => false,
    //             'errMsg' => __('WhatsApp configuration is missing.'),
    //         ]);
    //     }

    //     $recipientPhoneNumber = $order->phone_number;
    //     $messageContent = $request->order_mess;
    //     $trackingNumber = $request->number;

    //     if (!$messageContent || !$trackingNumber) {
    //         return response()->json([
    //             'status' => false,
    //             'errMsg' => __('Message and tracking number are required.'),
    //         ]);
    //     }

    //     if (strip_tags($messageContent) != $messageContent) {
    //         return response()->json([
    //             'status' => false,
    //             'errMsg' => __('Only plain text messages are allowed.'),
    //         ]);
    //     }

    //     // Format message
    //     $finalMessage = $messageContent . "\nTracking Number: " . $trackingNumber;

    //     // Send message via WhatsApp Cloud API
    //     try {
    //         $response = Http::withToken($whatsappAccessToken)->post("https://graph.facebook.com/v19.0/{$whatsappPhoneNumberId}/messages", [
    //             'messaging_product' => 'whatsapp',
    //             'to' => $recipientPhoneNumber,
    //             'type' => 'text',
    //             'text' => [
    //                 'body' => $finalMessage,
    //             ],
    //         ]);

    //         if ($response->failed()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'errMsg' => 'Failed to send message: ' . $response->json('error.message'),
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'toast' => 'Dispatch message sent successfully',
    //             'response' => $response->json(),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'errMsg' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function updateStatus(Request $request)
    // {
    //     $order = Order::find($request->order_id);
    //     if (!$order) {
    //         return response()->json(['success' => false, 'message' => 'Order not found']);
    //     }

    //     $order->status = $request->status;
    //     $order->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order status updated successfully',
    //         'whatsapp_sent' => false, // or true if you send a message
    //     ]);
    // }

    public function updateShipping(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'shipping_method' => 'nullable|string|max:255',
            'state' => 'string|max:255',
            'city' => 'string|max:255',
            'for_person' => 'nullable|string|max:255',
            'for_person_number' => 'nullable|string|max:20',
            'house_number' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'landmark_area' => 'nullable|string|max:255',
            'pin_code' => 'string|max:255',
            'address' => 'string',
            'tower_number' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping details updated successfully',
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'transaction_id' => 'nullable|string|max:255',
            'transaction_type' => 'required|string|in:UPI,CASH,BANK Transfer,Card',
            'payment_status' => 'required|string|in:Pending,Paid,Failed,Refunded',
            'currency' => 'required|string|max:3',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment details updated successfully',
        ]);
    }

    public function sendPaymentWhatsApp(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $company = $this->getCompany();

        // Calculate total amount
        $subtotal = $order->subtotal_value / $order->subtotal_offset;
        $shipping = $order->shipping_cast;
        $total = $subtotal + $shipping;

        // Format payment date
        $paymentDate = now()->format('d-M-Y h:i A');

        // Create WhatsApp message

        $paymentStatus = $order->payment_status;
        $message = "Hello {$order->user_name},\n\n";

        switch ($paymentStatus) {
            case 'Refunded':
                $message .= "Your refund for order #{$order->reference_id} has been processed.\n";
                $message .= "Amount: ₹{$total} has been credited back to your account.\n\n";
                $message .= 'If you have any questions, contact our support team.';
                break;

            case 'Failed':
                $message .= "Payment for order #{$order->reference_id} has failed. ❌\n\n";
                $message .= "Please try again or use a different payment method.\n";
                $message .= "Amount: ₹{$total}\n\n";
                $message .= "We'll hold your items for 24 hours.";
                break;

            case 'Pending':
                $message .= "Your payment for order #{$order->reference_id} is pending. ⏳\n\n";
                $message .= "Amount: ₹{$total}\n";
                $message .= "We'll process your order once payment is confirmed.";
                break;

            default:
                // Paid
                $message .= "Your payment for order #{$order->reference_id} has been confirmed! 🎉\n\n";
                $message .= "💰 *Payment Details*\n";
                $message .= "Amount: ₹{$total}\n";
                $message .= "Method: {$order->transaction_type}\n";
                $message .= "Date: {$paymentDate}\n\n";
                $message .= "We'll update you about your order status soon. You can track your order anytime.\n\n";
                $message .= 'Thank you for your purchase! ❤️';
        }

        // Send WhatsApp message
        $whatsappAccessToken = Config::where('model_id', $company->id)->where('key', 'whatsapp_permanent_access_token')->value('value');
        $whatsappPhoneNumberId = Config::where('model_id', $company->id)->where('key', 'whatsapp_phone_number_id')->value('value');

        try {
            $response = Http::withToken($whatsappAccessToken)->post("https://graph.facebook.com/v21.0/{$whatsappPhoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $order->phone_number,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp payment confirmation sent',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send WhatsApp: ' . $response->json('error.message'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }

        // Prevent changes if already delivered
        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Delivered orders cannot be modified',
            ]);
        }

        // Check payment requirement for delivery
        if ($request->status === 'delivered' && $order->payment_status !== 'Paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark as delivered - payment not received',
                'payment_status' => $order->payment_status,
            ]);
        }

        $order->status = $request->status;

        // Add delivery datetime and note if status is delivered
        if ($request->status === 'delivered') {
            $order->delivery_datetime = now(); // Use current time if not provided

            if ($request->has('delivery_datetime')) {
                $order->delivery_datetime = $request->delivery_datetime;
            }

            if ($request->has('delivery_note')) {
                $order->delivery_note = $request->delivery_note;
            }
        }

        $order->save();

        $whatsappSent = false;

        // Send WhatsApp if requested
        if ($request->send_whatsapp) {
            try {
                $statusMessages = [
                    'pending' => "⏳ Your order #{$order->reference_id} is pending confirmation",
                    'accepted' => "✅ Order #{$order->reference_id} has been accepted!",
                    'preparing' => "👨‍🍳 Your order #{$order->reference_id} is being prepared",
                    'ready_to_dispatch' => "📦 Order #{$order->reference_id} is ready for dispatch!",
                    'dispatched' => "🚚 Order #{$order->reference_id} has been dispatched! Tracking: {$order->tracking_number}",
                    'delivered' => "🎉 Order #{$order->reference_id} has been delivered!\n" . ($order->delivery_note ? "Note: {$order->delivery_note}" : ''),
                ];

                $message = "Hello {$order->user_name},\n";
                $message .= $statusMessages[$request->status] ?? 'Your order status has been updated to: ' . ucwords(str_replace('_', ' ', $request->status));

                // Send WhatsApp
                $whatsappAccessToken = Config::where('model_id', $this->getCompany()->id)
                    ->where('key', 'whatsapp_permanent_access_token')
                    ->value('value');
                $whatsappPhoneNumberId = Config::where('model_id', $this->getCompany()->id)
                    ->where('key', 'whatsapp_phone_number_id')
                    ->value('value');

                $response = Http::withToken($whatsappAccessToken)->post("https://graph.facebook.com/v21.0/{$whatsappPhoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $order->phone_number,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

                $whatsappSent = $response->successful();
            } catch (\Exception $e) {
                // Log error but don't prevent status update
                \Log::error('WhatsApp send failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'whatsapp_sent' => $whatsappSent,
            'new_status' => $order->status,
        ]);
    }

    public function sendOrderDispatch(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'errMsg' => 'Order not found',
            ]);
        }

        // Update status to dispatched
        $order->status = 'dispatched';
        $order->tracking_number = $request->number;
        $order->save();

        // Send WhatsApp
        $whatsappAccessToken = Config::where('model_id', $this->getCompany()->id)
            ->where('key', 'whatsapp_permanent_access_token')
            ->value('value');
        $whatsappPhoneNumberId = Config::where('model_id', $this->getCompany()->id)
            ->where('key', 'whatsapp_phone_number_id')
            ->value('value');

        $response = Http::withToken($whatsappAccessToken)->post("https://graph.facebook.com/v21.0/{$whatsappPhoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to' => $order->phone_number,
            'type' => 'text',
            'text' => ['body' => $request->order_mess . $request->number],
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'toast' => 'Dispatch message sent successfully!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errMsg' => 'Failed to send WhatsApp: ' . $response->json('error.message'),
            ]);
        }
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Validate request
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
            'send_whatsapp' => 'boolean',
        ]);

        // Update order status
        $order->status = 'canceled';
        $order->cancel_reason = $request->cancel_reason;
        $order->canceled_at = now();
        $order->save();

        $whatsappSent = false;

        // Send WhatsApp if requested
        if ($request->send_whatsapp) {
            try {
                $company = $this->getCompany();
                $whatsappAccessToken = Config::where('model_id', $company->id)->where('key', 'whatsapp_permanent_access_token')->value('value');
                $whatsappPhoneNumberId = Config::where('model_id', $company->id)->where('key', 'whatsapp_phone_number_id')->value('value');

                // Create cancellation message
                $message = "Hello {$order->user_name},\n\n";
                $message .= "We regret to inform you that your order #{$order->reference_id} has been canceled. 😔\n\n";
                $message .= "📝 *Reason for Cancellation*\n";
                $message .= "{$request->cancel_reason}\n\n";
                $message .= "If you have any questions, please contact our support team.\n\n";
                $message .= 'Thank you for your understanding. 🙏';

                // Send WhatsApp
                $response = Http::withToken($whatsappAccessToken)->post("https://graph.facebook.com/v21.0/{$whatsappPhoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $order->phone_number,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

                $whatsappSent = $response->successful();
            } catch (\Exception $e) {
                // Log error but don't prevent cancellation
                \Log::error('Failed to send cancellation WhatsApp: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'whatsapp_sent' => $whatsappSent,
        ]);
    }

    public function updateContact(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate(['user_name' => 'required|string|max:255']);

        $order->update(['user_name' => $validated['user_name']]);

        // Update contact information
        Contact::where('phone', $order->phone_number)
            ->where('company_id', $order->company_id)
            ->update(['name' => $validated['user_name']]);

        return response()->json(['success' => true, 'newName' => $validated['user_name']]);
    }

    public function updateOrderNote(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Prevent updates if already delivered
        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update instructions for delivered orders',
            ]);
        }

        $order->order_process_note = $request->input('order_process_note');
        $order->save();

        return response()->json([
            'success' => true,
            'newNote' => $order->order_process_note,
        ]);
    }

    public function applyDiscount(Request $request, $orderId)
    {
        $request->validate([
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($orderId);

        // Update shipping
        $shipping = (float) $request->shipping_amount;
        $order->shipping_cast = $shipping;

        // Calculate discount
        $subtotal = $order->subtotal_value / $order->subtotal_offset;
        $discountAmount = 0; // This will store the actual monetary discount
        $discountValue = $request->discount_value ? (float) $request->discount_value : 0;

        if ($request->discount_type && $discountValue > 0) {
            if ($request->discount_type === 'percent') {
                $discountAmount = ($subtotal * $discountValue) / 100;
            } else {
                $discountAmount = min($discountValue, $subtotal);
            }
        }

        // Calculate final amount
        $finalAmount = $subtotal - $discountAmount + $shipping;

        // Update order
        $order->discount_type = $request->discount_type;
        $order->discount = $discountValue; // The discount value (percentage or fixed amount)
        $order->discount_amount = $discountAmount; // The actual monetary discount
        $order->final_amount = $finalAmount;
        $order->save();

        return response()->json([
            'success' => true,
            'discount_value' => $discountValue, // The entered discount value
            'discount_amount' => $discountAmount, // The calculated monetary discount
            'discount_type' => $request->discount_type,
            'shipping' => $shipping,
            'final_amount' => $finalAmount,
        ]);
    }

    // In resendPaymentLink method
    public function resendPaymentLink(Request $request)
    {
        $order = Order::with('items')->findOrFail($request->order_id);

        // Check if discount was applied
        if (!$order->final_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Please apply discount first',
            ]);
        }

        try {
            $companyId = $order->company_id;

            // Get WhatsApp credentials
            $whatsapp_permanent_access_token = Config::where('model_id', $companyId)->where('key', 'whatsapp_permanent_access_token')->first();

            $whatsapp_phone_number_id = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone_number_id')->first();

            $accessToken = $whatsapp_permanent_access_token->value;
            $whatsappBusinessPhoneNumberId = $whatsapp_phone_number_id->value;

            $paymentConfig = Paymenttemplate::where('company_id', $companyId)->first();

            // Map items correctly
            $items = [];
            if ($order->items) {
                $items = $order->items
                    ->map(function ($item) {
                        return [
                            'retailer_id' => $item->retailer_id,
                            'name' => $item->name,
                            'amount' => [
                                'value' => $item->amount_value,
                                'offset' => $item->amount_offset,
                            ],
                            'quantity' => $item->quantity,
                        ];
                    })
                    ->toArray();
            }

            $discountDis = strtoupper($order->discount_type) . ($order->discount_type == 'percent' ? ' : ' . $order->discount . '%' : '');

            // Send payment message with updated amount
            $response = $this->sendWhatsAppOrderDetailMessage(
                $order->phone_number,
                $accessToken,
                $whatsappBusinessPhoneNumberId,
                [
                    'body_text' => $paymentConfig->body ?? 'Thank you for placing order with us. Kindly make payment to proceed your orders.',
                    'footer_text' => $paymentConfig->footer ?? '',
                    'items' => $items,
                    'subtotal_value' => $order->subtotal_value,
                    'subtotal_offset' => $order->subtotal_offset,
                    'tax_value' => $order->tax_value,
                    'tax_offset' => $order->tax_offset,
                ],
                $order->reference_id,
                $paymentConfig->payment_configuration,
                $paymentConfig->payment_configuration_other,
                $paymentConfig->payment_type,
                $paymentConfig->shipping_description,
                $order->shipping_cast,
                $order->discount_amount,
                $discountDis,
                $order->final_amount,
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment link resent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend payment link: ' . $e->getMessage(),
            ]);
        }
    }

    private function calculateTotalAmount($subtotal, $tax)
    {
        return $subtotal + $tax;
    }

    private function sendWhatsAppOrderDetailMessage($phoneNumber, $accessToken, $whatsappBusinessPhoneNumberId, $orderDetails, $referenceId, $paymentConfiguration, $payment_configuration_other, $payment_type, $shipping_description, $shipping, $discount, $discountDis, $finalAmount)
    {
        $url = "https://graph.facebook.com/v21.0/$whatsappBusinessPhoneNumberId/messages";

        // $this->logData("test sendWhatsAppOrderDetailMessage-orderDetails: ".print_r($orderDetails), $logFile);
        $subtotal = $orderDetails['subtotal_value'];
        $tax = $orderDetails['tax_value'];

        $totalAmountValue = $this->calculateTotalAmount($subtotal, $tax);
        if ($payment_type == 1) {
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'order_details',
                    'body' => [
                        'text' => $orderDetails['body_text'],
                    ],
                    'footer' => [
                        'text' => $orderDetails['footer_text'],
                    ],
                    'action' => [
                        'name' => 'review_and_pay',
                        'parameters' => [
                            'reference_id' => $referenceId,
                            'type' => 'digital-goods',
                            'payment_settings' => [
                                [
                                    'type' => 'payment_gateway',
                                    'payment_gateway' => [
                                        'type' => 'razorpay',
                                        'configuration_name' => $payment_configuration_other,
                                        'razorpay' => [
                                            'receipt' => $referenceId,
                                        ],
                                    ],
                                ],
                            ],
                            'currency' => 'INR',
                            'total_amount' => [
                                'value' => ($finalAmount ?? 0) * 100,
                                'offset' => 100,
                            ],
                            'order' => [
                                'status' => 'pending',
                                'items' => $orderDetails['items'],
                                'subtotal' => [
                                    'value' => $subtotal,
                                    'offset' => 100,
                                ],
                                'tax' => [
                                    'value' => $tax,
                                    'offset' => 100,
                                    'description' => $orderDetails['tax_description'] ?? '',
                                ],
                                'shipping' => [
                                    'value' => ($shipping ?? 0) * 100,
                                    'offset' => 100,
                                    'description' => $shipping_description ?? 'no shipping',
                                ],
                                'discount' => [
                                    'value' => $discount ? ($discount ?? 0) * 100 : 0,
                                    'offset' => 100,
                                    'description' => $discountDis ?? 'No Discount',
                                    'discount_program_name' => 'None',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'order_details',
                    'body' => [
                        'text' => $orderDetails['body_text'],
                    ],
                    'footer' => [
                        'text' => $orderDetails['footer_text'],
                    ],
                    'action' => [
                        'name' => 'review_and_pay',
                        'parameters' => [
                            'reference_id' => $referenceId,
                            'type' => 'digital-goods',
                            'payment_type' => 'upi',
                            'payment_configuration' => $paymentConfiguration,
                            'currency' => 'INR',
                            'total_amount' => [
                                'value' => ($finalAmount ?? 0) * 100,
                                'offset' => 100,
                            ],
                            'order' => [
                                'status' => 'pending',
                                'items' => $orderDetails['items'],
                                'subtotal' => [
                                    'value' => $totalAmountValue,
                                    'offset' => 100,
                                ],
                                'tax' => [
                                    'value' => $tax,
                                    'offset' => 100,
                                    'description' => $orderDetails['tax_description'] ?? '',
                                ],
                                'shipping' => [
                                    'value' => ($shipping ?? 0) * 100,
                                    'offset' => 100,
                                    'description' => $shipping_description ?? 'no shipping',
                                ],
                                'discount' => [
                                    'value' => $discount ? ($discount ?? 0) * 100 : 0,
                                    'offset' => 100,
                                    'description' => $discountDis ?? 'No Discount',
                                    'discount_program_name' => 'None',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $accessToken"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        // $this->logData('Payment Data: ' . print_r($response, true), '');

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            echo "cURL error: $error";
            return null;
        }

        curl_close($ch);

        if ($httpStatus >= 400) {
            echo "HTTP request failed with status $httpStatus: $response";
            return null;
        }

        return json_decode($response, true);
    }

    private function sendWhatsAppAddressDetailMessage($phoneNumber, $whatsappBusinessPhoneNumberId, $accessToken, $referenceId)
    {
        // Remove existing logging or replace with proper Laravel logging
        $url = "https://graph.facebook.com/v21.0/$whatsappBusinessPhoneNumberId/messages";

        $orders = OrderAddress::where('phone_number', $phoneNumber)->whereNotNull('address')->get();

        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'address_message',
                'body' => [
                    'text' => 'Please update your shipping address for order #' . $referenceId,
                ],
                'action' => [
                    'name' => 'address_message',
                    'parameters' => [
                        'country' => 'IN',
                        'reference_id' => (string) $referenceId,
                    ],
                ],
            ],
        ];

        // Add saved addresses if available
        if ($orders->isNotEmpty()) {
            $data['interactive']['action']['parameters']['saved_addresses'] = $orders
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'value' => [
                            'name' => $order->user_name,
                            'phone_number' => $order->phone_number,
                            'in_pin_code' => $order->pin_code,
                            'floor_number' => $order->tower_number,
                            'building_name' => $order->building_name,
                            'address' => $order->address,
                            'landmark_area' => $order->landmark_area,
                            'city' => $order->city,
                            'state' => $order->state,
                        ],
                    ];
                })
                ->toArray();
        }

        // Send request using Laravel HTTP client
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $data);

        if ($response->failed()) {
            Log::error('WhatsApp API error', $response->json());
        }
    }

    public function resendAddressForm(Order $order)
    {
        // Check if order can be updated
        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resend address form for delivered orders',
            ]);
        }

        try {
            $recipient = $order->phone_number; // Adjust to your phone field
            $companyId = $order->company_id;

            // Get WhatsApp credentials
            $whatsapp_permanent_access_token = Config::where('model_id', $companyId)->where('key', 'whatsapp_permanent_access_token')->first();

            $whatsapp_phone_number_id = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone_number_id')->first();

            $accessToken = $whatsapp_permanent_access_token->value;
            $whatsappBusinessPhoneNumberId = $whatsapp_phone_number_id->value;

            $this->sendWhatsAppAddressDetailMessage(
                $recipient,
                $whatsappBusinessPhoneNumberId,
                $accessToken,
                $order->reference_id ?? $order->id, // Using order ID as reference
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('WhatsApp resend failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp message',
            ]);
        }
    }

    public function setting()
    {
        $this->authChecker();

        if ($this->getCompany()->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $this->getCompany()->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            return redirect(route('whatsapp.setup'));
        }

        $company = $this->getCompany();
        $companyId = $company->id;

        // Fetch utility templates
        $utilityTemplates = Template::where('category', 'utility')->where('status', 'approved')->where('company_id', $companyId)->get();

        $catalogs = Catalog::where('company_id', $companyId)->get();
        $products = CatalogProduct::where('company_id', $companyId)->get();
        $Paymenttemplate = Paymenttemplate::where('company_id', $companyId)->first();
        $config = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone')->first();

        return view(
            $this->view_path . 'setting',
            compact(
                'Paymenttemplate',
                'products',
                'config',
                'catalogs',
                'companyId',
                'utilityTemplates', // Make sure to pass this to the view
            ),
        );
    }

    public function settingUpdate(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $setting = Paymenttemplate::firstOrNew(['company_id' => $companyId]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo && Storage::disk('storefront')->exists($setting->logo)) {
                Storage::disk('storefront')->delete($setting->logo);
            }

            // Store new logo
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $logoPath = $file->storeAs('', $fileName, 'storefront');
            $setting->logo = $fileName;
        }
        // Handle logo removal
        elseif ($request->input('remove_logo') == 1) {
            if ($setting->logo && Storage::disk('storefront')->exists($setting->logo)) {
                Storage::disk('storefront')->delete($setting->logo);
            }
            $setting->logo = null;
        }

        // Handle checkbox fields
        $booleanFields = ['isShippingFree', 'isDiscountAutoApply', 'enable_self_pickup', 'enable_in_store', 'enable_delivery'];

        foreach ($booleanFields as $field) {
            $setting->$field = $request->input($field, 0) ? 1 : 0;
        }

        // Update all fields
        $fields = [
            // Existing fields
            'body',
            'footer',
            'payment_configuration',
            'payment_configuration_other',
            'shipping',
            'shipping_description',
            'shipping_free_from_amount',
            'isShippingFree',
            'payment_type',
            'address_message_enable',
            'payment_method_enable',
            'address_mess',
            'whatsapp_number',
            'order_message',
            'product_id',

            // StoreFront fields
            'business_name',
            'business_address',
            'business_phone',
            'business_whatsapp',
            'business_email',
            'gstin_vat',
            'currency_code',
            'currency_symbol',
            'discount_type',
            'default_discount',
            'discount_from_amount',
            'isDiscountAutoApply',

            // Message Templates fields
            'order_accepted',
            'order_dispatched',
            'order_prepared',
            'order_delivered',
            'review_template',
            'payment_received',
            'payment_failed',
            'payment_refunded',
            'order_cancel',

            // NEW: Default template for 24hr inactive window
            'default_template_id',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $setting->$field = $request->input($field);
            }
        }

        // Handle toggles
        //  if ($request->has('address_message_enable') || $request->has('payment_method_enable')) {
        //     $toggles = ['address_message_enable', 'payment_method_enable'];
        //     foreach ($toggles as $toggle) {
        //         $setting->$toggle = $request->has($toggle) ? 1 : 0;
        //     }
        // }

        if ($request->has('shipping_free_from_amount')) {
            $shippingMethods = [$request->input('enable_self_pickup', 0), $request->input('enable_in_store', 0), $request->input('enable_delivery', 0)];

            if (!in_array(1, $shippingMethods)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'At least one shipping method must be enabled.',
                    ],
                    422,
                );
            }
        }

        $setting->company_id = $companyId;
        $setting->save();

        return response()->json(['success' => true]);
    }

    public function refreshWindowStatus($id)
    {
        $order = Order::findOrFail($id);
        $company = $this->getCompany();

        $contact = Contact::where('company_id', $company->id)->where('phone', $order->phone_number)->whereNull('deleted_at')->orderBy('last_client_reply_at', 'desc')->first();

        // Recalculate window status
        $windowStatus = null;
        $isFreeWindowExpired = false;
        $lastContactReply = null;

        if ($contact && $contact->last_client_reply_at) {
            $lastContactReply = $contact->last_client_reply_at;
            $lastReply = Carbon::parse($contact->last_client_reply_at);
            $windowEnd = $lastReply->addHours(24);
            $now = Carbon::now();

            if ($now->lt($windowEnd)) {
                $windowStatus = [
                    'remaining' => $now->diff($windowEnd),
                    'expires_at' => $windowEnd,
                ];
            } else {
                $windowStatus = 'expired';
                $isFreeWindowExpired = true;
            }
        }

        $html = view('partials.whatsapp_window_status', [
            'windowStatus' => $windowStatus,
            'lastContactReply' => $lastContactReply,
            'isFreeWindowExpired' => $isFreeWindowExpired,
            'order' => $order,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'windowActive' => $windowStatus !== 'expired',
        ]);
    }

    public function sendTemplateMessage($id)
    {
        $order = Order::findOrFail($id);
        $company = $this->getCompany();
        $template = Paymenttemplate::where('company_id', $company->id)->first();

        if (!$template || !$template->default_template_id) {
            return response()->json([
                'success' => false,
                'message' => 'Template not configured',
            ]);
        }

        // Get the actual template with its content
        $templateInfo = Template::find($template->default_template_id);

        if (!$templateInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found',
            ]);
        }

        try {
            // Get WhatsApp configuration from company
            $companyId = $company->id;
            $whatsapp_permanent_access_token = Config::where('model_id', $companyId)->where('key', 'whatsapp_permanent_access_token')->first();
            $whatsapp_phone_number_id = Config::where('model_id', $companyId)->where('key', 'whatsapp_phone_number_id')->first();

            if (!$whatsapp_permanent_access_token || !$whatsapp_phone_number_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp not properly configured',
                ]);
            }

            $accessToken = $whatsapp_permanent_access_token->value;
            $whatsappBusinessPhoneNumberId = $whatsapp_phone_number_id->value;
            $version = 'v21.0';

            // Check if template body contains {{ 1 }}
            $hasVariable = false;
            $components = is_array($templateInfo->components) ? $templateInfo->components : json_decode($templateInfo->components, true);

            if ($components) {
                foreach ($components as $component) {
                    if (($component['type'] ?? null) === 'BODY') {
                        if (str_contains($component['text'] ?? '', '{{ 1 }}')) {
                            $hasVariable = true;
                            break;
                        }
                    }
                }
            }

            // FIX: Use correct property name and provide default language
            $languageCode = $templateInfo->language ?? 'en_US'; // Corrected property name

            // Prepare template parameters
            $templateData = [
                'name' => $templateInfo->name,
                'language' => ['code' => $languageCode], // Now always has valid language code
            ];

            // Add parameters if variable exists
            if ($hasVariable) {
                $templateData['components'] = [
                    [
                        'type' => 'body',
                        'parameters' => [['type' => 'text', 'text' => $order->user_name]],
                    ],
                ];
            }

            // Send WhatsApp message
            $client = new \GuzzleHttp\Client();
            $url = "https://graph.facebook.com/{$version}/{$whatsappBusinessPhoneNumberId}/messages";

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $order->phone_number,
                    'type' => 'template',
                    'template' => $templateData,
                ],
            ]);

            $status = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status === 200) {
                return response()->json(['success' => true]);
            }

            return response()->json([
                'success' => false,
                'message' => $body['error']['message'] ?? 'Failed to send template',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Add this new method to handle item addition
    // Add this new method to handle item addition
    public function addItemToOrder(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $retailerId = $request->input('retailer_id');
        $productName = $request->input('product_name');

        $price = $request->input('price');
        $cleanPrice = floatval(preg_replace('/[^\d.]/', '', $price));

        $quantity = $request->input('quantity', 1);

        // Check if product already exists in order
        $existingItem = OrderItem::where('order_id', $order->id)->where('retailer_id', $retailerId)->first();

        if ($existingItem) {
            // Update quantity
            $existingItem->quantity += $quantity;
            $existingItem->save();
        } else {
            // Create new item
            OrderItem::create([
                'order_id' => $order->id,
                'retailer_id' => $retailerId,
                'name' => $productName,
                'quantity' => $quantity,
                'amount_value' => $cleanPrice * 100, // Convert to cents
                'amount_offset' => 100,
            ]);
        }

        // Recalculate order total
        $this->recalculateOrderTotal($order);

        return response()->json([
            'success' => true,
            'message' => 'Item added successfully',
        ]);
    }

    // Helper method to recalculate order total
    private function recalculateOrderTotal(Order $order)
    {
        $subtotal = 0;

        foreach ($order->items as $item) {
            $itemPrice = $item->amount_value / $item->amount_offset;
            $subtotal += $itemPrice * $item->quantity;
        }

        // Update order values
        $order->subtotal_value = $subtotal * 100;
        $order->subtotal_offset = 100;

        // Calculate discount
        $discountAmount = 0;
        if ($order->discount_type === 'percent') {
            $discountAmount = ($subtotal * $order->discount) / 100;
        } else {
            $discountAmount = $order->discount;
        }

        // Calculate final amount
        $order->final_amount = $subtotal - $discountAmount + $order->shipping_cast;

        $order->save();
    }

    public function getOrderItemsSection($id)
    {
        $order = Order::findOrFail($id);
        $company = $this->getCompany(); // Your method to get company

        return view('partials.order_items_section', [
            'setup' => [
                'items' => $order->items,
                // Add other needed setup data
            ],
            'order' => $order,
        ]);
    }

    // Main Controller
    public function deleteOrderItem(Request $request, $orderId)
    {
        $request->validate(['item_id' => 'required|exists:order_items,id']);

        $item = OrderItem::findOrFail($request->item_id);
        $item->delete();

        $order = Order::findOrFail($orderId);
        $this->recalculateOrderTotal($order);

        return response()->json(['success' => true]);
    }

    // Main Controller
    public function updateOrderItem(Request $request, $orderId)
    {
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = OrderItem::findOrFail($request->item_id);
        $item->quantity = $request->quantity;
        $item->save();

        $order = Order::findOrFail($orderId);
        $this->recalculateOrderTotal($order);

        return response()->json(['success' => true]);
    }
    public function orderItemsSection($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('Catalogs::order.partials.order_items_section', [
            'setup' => ['items' => $order->items],
            'order' => $order,
        ]);
    }

    public function orderAdjustSection($id)
    {
        // $order = Order::with('items')->findOrFail($id);
        // $freezePricing = $order->payment_status === 'Paid' || $order->status === 'delivered';

        // return view('Catalogs::order.item', [
        //     'order' => $order,
        //     'freezePricing' => $freezePricing
        // ]);
    }

    private function formatOrderDetailsForWhatsApp(Order $order, Paymenttemplate $paymentTemplate)
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'retailer_id' => $item->retailer_id,
                'name' => $item->name,
                'amount' => [
                    'value' => $item->amount_value,
                    'offset' => $item->amount_offset,
                ],
                'quantity' => $item->quantity,
            ];
        }

        return [
            'user_name' => $order->user_name,
            'phone_number' => $order->phone_number,
            'items' => $items,
            'subtotal_value' => $order->subtotal_value,
            'subtotal_offset' => $order->subtotal_offset,
            'tax_value' => 0, // Assuming no tax for updates
            'tax_offset' => 100,
            'tax_description' => 'Tax Included',
            'body_text' => 'Your order has been updated! Review the changes below:',
            'footer_text' => $paymentTemplate->footer ?? 'sendinai.com',
        ];
    }

    private function generatePaymentMessage(Order $order, Paymenttemplate $paymentTemplate)
    {
        $message = "💳 *Payment Link for Order #{$order->reference_id}*\n\n";
        $message .= "Please complete your payment using the link below:\n";

        // Generate payment link - implement this route in your application
        $paymentLink = 'link.com';
        $message .= $paymentLink . "\n\n";

        $message .= "🔐 Secure payment · ⚡ Instant confirmation\n";
        $message .= 'Thank you for your order!';

        return $message;
    }

    private function sendTextMessage($toPhoneNumber, $messageBody, $accessToken, $whatsappBusinessPhoneNumberId)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhoneNumber,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $messageBody,
            ],
        ];

        $url = "https://graph.facebook.com/v21.0/{$whatsappBusinessPhoneNumberId}/messages";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer {$accessToken}"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'response' => json_decode($response, true),
        ];
    }

    // public function sendUpdatedCart(Request $request, $orderId)
    // {
    //     try {
    //         $order = Order::with('items')->findOrFail($orderId);
    //         $companyId = $order->company_id;

    //         // Get WhatsApp configuration
    //         $whatsappConfig = Config::where('model_id', $companyId)
    //             ->whereIn('key', ['whatsapp_permanent_access_token', 'whatsapp_phone_number_id'])
    //             ->pluck('value', 'key');

    //         $accessToken = $whatsappConfig['whatsapp_permanent_access_token'] ?? null;
    //         $whatsappBusinessPhoneNumberId = $whatsappConfig['whatsapp_phone_number_id'] ?? null;

    //         if (!$accessToken || !$whatsappBusinessPhoneNumberId) {
    //             return response()->json(
    //                 [
    //                     'success' => false,
    //                     'message' => 'WhatsApp configuration missing',
    //                 ],
    //                 400,
    //             );
    //         }

    //         // Get payment template configuration
    //         $paymentTemplate = Paymenttemplate::where('company_id', $companyId)->first();
    //         if (!$paymentTemplate) {
    //             return response()->json(
    //                 [
    //                     'success' => false,
    //                     'message' => 'Payment configuration not found',
    //                 ],
    //                 400,
    //             );
    //         }

    //         // Format order details for WhatsApp message
    //         $orderDetails = $this->formatOrderDetailsForWhatsApp($order, $paymentTemplate);
    //         $discountDis = strtoupper($order->discount_type) . ($order->discount_type == 'percent' ? ' : ' . $order->discount . '%' : '');

    //         // Send cart update message
    //         $cartResponse = $this->sendWhatsAppOrderDetailMessage($order->phone_number, $accessToken, $whatsappBusinessPhoneNumberId, $orderDetails, $order->reference_id, $paymentTemplate->payment_configuration, $paymentTemplate->payment_configuration_other, $paymentTemplate->payment_type, $paymentTemplate->shipping_description, $order->shipping_cast, $order->discount_amount, $discountDis, $order->final_amount);

    //         // Send payment link message
    //         $paymentMessage = $this->generatePaymentMessage($order, $paymentTemplate);
    //         $paymentResponse = $this->sendTextMessage($order->phone_number, $paymentMessage, $accessToken, $whatsappBusinessPhoneNumberId);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'WhatsApp messages sent successfully',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'message' => 'Failed to send messages: ' . $e->getMessage(),
    //             ],
    //             500,
    //         );
    //     }
    // }

    public function sendUpdatedCart(Request $request, $orderId)
    {
        try {
            $order = Order::with('items')->findOrFail($orderId);
            $companyId = $order->company_id;

            // Get WhatsApp configuration
            $whatsappConfig = Config::where('model_id', $companyId)
                ->whereIn('key', ['whatsapp_permanent_access_token', 'whatsapp_phone_number_id'])
                ->pluck('value', 'key');

            $accessToken = $whatsappConfig['whatsapp_permanent_access_token'] ?? null;
            $whatsappBusinessPhoneNumberId = $whatsappConfig['whatsapp_phone_number_id'] ?? null;

            if (!$accessToken || !$whatsappBusinessPhoneNumberId) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'WhatsApp configuration missing',
                    ],
                    400,
                );
            }

            // Generate cart review message
            $cartMessage = $this->generateCartReviewMessage($order);

            // Send WhatsApp message
            $response = $this->sendWhatsAppCartReviewMessage($order->phone_number, $cartMessage, $accessToken, $whatsappBusinessPhoneNumberId, $order->reference_id);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp message sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to send messages: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function generateCartReviewMessage(Order $order)
    {
        // $message = "📋 *UPDATED ORDER SUMMARY* 📋\n";
        // $message .= "——————————————\n";
        // $message .= "🆔 *Order ID*: #{$order->reference_id}\n";
        // $message .= "👤 *Customer*: {$order->user_name}\n\n";

        // $message .= "🛒 *ITEMS UPDATED*\n";
        // $message .= "——————————————\n";

        // foreach ($order->items as $index => $item) {
        //     $itemNumber = $index + 1;
        //     $itemPrice = $item->amount_value / $item->amount_offset;
        //     $itemTotal = $itemPrice * $item->quantity;

        //     $message .= "{$itemNumber}. *{$item->name}*\n";
        //     $message .= "   ├─ Quantity: {$item->quantity}\n";
        //     $message .= '   ├─ Unit Price: ₹' . number_format($itemPrice, 2) . "\n";
        //     $message .= '   └─ *Item Total: ₹' . number_format($itemTotal, 2) . "*\n\n";
        // }

        // $message .= "💰 *ORDER SUMMARY*\n";
        // $message .= "——————————————\n";
        // $message .= '├─ Subtotal: ₹' . number_format($order->subtotal_value / $order->subtotal_offset, 2) . "\n";

        // if ($order->discount > 0) {
        //     $discountText = $order->discount_type === 'percent' ? $order->discount . '%' : '₹' . number_format($order->discount, 2);

        //     $message .= '├─ Discount: -' . $discountText . "\n";
        // }

        // $message .= '├─ Shipping: ₹' . number_format($order->shipping_cast, 2) . "\n";
        // $message .= "╰──────────────────\n";
        // $message .= '💳 *GRAND TOTAL: ₹' . number_format($order->final_amount, 2) . "*\n\n";

        // $message .= "📌 *NEXT STEPS*\n";
        // $message .= "——————————————\n";
        // $message .= "Please review the updated items and pricing. If everything looks correct, click the button below to confirm and process your updated order.\n\n";
        // $message .= "✅ We'll prepare your order immediately after confirmation!";

        $message = "🧾 *Updated Order #{$order->reference_id}* for {$order->user_name}\n";
        $message .= "🛍️ *Items:*\n";

        $subtotal = 0;
        foreach ($order->items as $item) {
            $itemPrice = $item->amount_value / $item->amount_offset;
            $itemTotal = $itemPrice * $item->quantity;
            $subtotal += $itemTotal;
            $message .= "- {$item->name} × {$item->quantity} = ₹" . number_format($itemTotal, 2) . "\n";
        }

        $shipping = $order->shipping_cast ?? 0;
        $discount = $order->discount_amount ?? 0;
        $total = $subtotal + $shipping - $discount;

        $message .= "\n💰 *Subtotal:* ₹" . number_format($subtotal, 2) . "\n";

        // Show discount if applied
        if ($discount > 0) {
            $discountType = $order->discount_type === 'percent' ? $order->discount . '%' : '₹' . number_format($order->discount, 2);

            $message .= '🎁 *Discount (' . $discountType . '):* -₹' . number_format($discount, 2) . "\n";
        }

        $message .= '🚚 *Shipping:* ₹' . number_format($shipping, 2) . "\n";
        $message .= '💳 *Total:* ₹' . number_format($total, 2) . "\n\n";
        $message .= '📌 Please confirm & share your delivery address.';

        return $message;
    }
    private function sendWhatsAppCartReviewMessage($toPhoneNumber, $messageBody, $accessToken, $whatsappBusinessPhoneNumberId, $reference_id)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhoneNumber,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $messageBody,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'DOTFLOPC_' . $reference_id,
                                'title' => 'Yes, I confirm!',
                            ],
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'DOTFLOCC_' . $reference_id,
                                'title' => 'Cancel order!',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $url = "https://graph.facebook.com/v21.0/{$whatsappBusinessPhoneNumberId}/messages";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer {$accessToken}"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'response' => json_decode($response, true),
        ];
    }

    public function categoryDelete($id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found']);
        }

        // Optional: prevent delete if products exist
        $category->delete();

        return response()->json(['success' => true]);
    }
}
