@extends('layouts.app', ['title' => __('Automation Form')])

@section('content')
<style>
    .form-container {
        max-width: 90%;
        margin: 40px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .btn-success,
    .btn-primary {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: transform 0.2s, background-color 0.3s;
    }

    .btn-success:hover,
    .btn-primary:hover {
        transform: translateY(-2px);
    }

    .or-divider {
        text-align: center;
        color: #6c757d;
        font-weight: 500;
        margin: 20px 0;
        position: relative;
    }

    .or-divider::before,
    .or-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 45%;
        height: 1px;
        background: #ced4da;
    }

    .or-divider::before {
        left: 0;
    }

    .or-divider::after {
        right: 0;
    }

    .select-icon {
        position: relative;
    }

    .select-icon i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    @media (max-width: 576px) {
        .form-container {
            margin: 20px;
            padding: 20px;
        }

        .btn-primary {
            width: 100% !important;
        }
    }
</style>

<div class="header pb-8 pt-5 pt-md-8">
    <div class="container-fluid header-body d-flex justify-content-between align-items-center flex-wrap">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🏢 {{__('Automation Form')}}</h1>
        </div>
        <div class="mb-3">
                    <a href="{{ route('automation.fb_automation') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </div>
    </div>
</div>

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="form-container">
                <form id="automationForm" action="" method="POST">
                    @csrf
                     <input type="hidden" name="id" id="automation_id">
                    <!-- Ad Account -->
                    <div class="mb-4">
                        <label for="ad_account_id" class="form-label">Ad Account</label>
                        <div class="select-icon">
                            <select name="ad_account_id" id="ad_account_id" class="form-control">
                                <option value="">Select Ad Account</option>
                                @foreach ($accountNames->unique('account_id') as $lead)
                                    <option value="{{ $lead->account_id }}">{{ $lead->account_name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    
                    <!-- Campaign -->
                    <div class="mb-4">
                        <label for="campaign_id" class="form-label">Campaign</label>
                        <div class="select-icon">
                            <select name="campaign_id" id="campaign_id" class="form-control" disabled>
                                <option value="">Select Campaign</option>
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    
                    <!-- Ad Set -->
                    <div class="mb-4">
                        <label for="adset_id" class="form-label">Ad Set</label>
                        <div class="select-icon">
                            <select name="adset_id" id="adset_id" class="form-control" disabled>
                                <option value="">Select Ad Set</option>
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    
                    <!-- Ad -->
                    <div class="mb-4">
                        <label for="ad_id" class="form-label">Ad</label>
                        <div class="select-icon">
                            <select name="ad_id" id="ad_id" class="form-control" disabled>
                                <option value="">Select Ad</option>
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>

                    
                    <!-- Template -->
                    <div class="mb-4">
                        <label for="template_id" class="form-label">Template</label>
                        <div class="select-icon">
                            <select name="template_id" id="template_id" class="form-control">
                                <option value="">Select Template</option>
                                @foreach ($template as $fblead)
                                    <option value="{{ $fblead->id }}">{{ $fblead->name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success mx-auto w-25">Submit</button>
                    </div>
                    
                     <!--OR Divider -->
                    <div class="or-divider">OR</div>
                    <!-- Create Template Button -->
                    <div class="mb-4 text-center">
                        <a href="{{ route('templates.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Create Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<hr class="my-5">

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h4 class="text-center mb-4">📋 Saved Automations</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th>Ad Account ID</th>
                            <th>Campaign ID</th>
                            <th>Ad Set ID</th>
                            <th>Ad ID</th>
                            <th>Template Name</th>
                            <!--<th>Created At</th>-->
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($automations as $index => $automation)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $automation->ad_account_id }}</td>
                            <td>{{ $automation->campaign_id }}</td>
                            <td>{{ $automation->adset_id }}</td>
                            <td>{{ $automation->ad_id }}</td>
                            <td>{{ optional($automation->template)->name }}</td>
                            <!--<td>{{ \Carbon\Carbon::parse($automation->created_at)->format('d M Y, h:i A') }}</td>-->
                            <td>
                                <span class="badge {{ $automation->status == 'paused' ? 'bg-warning' : 'bg-success' }}">
                                    {{ ucfirst($automation->status) }}
                                </span>
                            </td>

                            <td>
                                <button type="button" class="btn btn btn-primary editAutomation" data-toggle="modal" data-target="#automationModal"
                                    data-id="{{ $automation->id }}"
                                    data-ad_account_id="{{ $automation->ad_account_id }}"
                                    data-campaign_id="{{ $automation->campaign_id }}"
                                    data-adset_id="{{ $automation->adset_id }}"
                                    data-ad_id="{{ $automation->ad_id }}"
                                    data-template_id="{{ $automation->template_id }}">
                                    Edit
                                </button>
                                <form action="{{ route('automation.delete', $automation->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this automation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn btn-danger mt-1">Delete</button>
                                </form>
                                 <form action="{{ route('automation.toggleStatus', $automation->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn mt-1 {{ $automation->status == 'paused' ? 'btn-success' : 'btn-warning' }}">
                                        {{ $automation->status == 'paused' ? 'Resume' : 'Pause' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No automations found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Edit (Reusing the Same Form) -->
<div class="modal fade" id="automationModal" tabindex="-1" role="dialog" aria-labelledby="automationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="automationModalLabel">Edit Automation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-container">
            <form id="modalAutomationForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="modal_automation_id">
                <div class="form-group">
                    <label for="modal_ad_account_id">Ad Account</label>
                    <select name="ad_account_id" id="modal_ad_account_id" class="form-control">
                        <option value="">Select Ad Account</option>
                  
                         @foreach ($accountNames->unique('account_id') as $lead)
                                    <option value="{{ $lead->account_id }}">{{ $lead->account_name }}</option>
                                @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_campaign_id">Campaign</label>
                    <select name="campaign_id" id="modal_campaign_id" class="form-control">
                        <option value="">Select Campaign</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_adset_id">Ad Set</label>
                    <select name="adset_id" id="modal_adset_id" class="form-control">
                        <option value="">Select Ad Set</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_ad_id">Ad</label>
                    <select name="ad_id" id="modal_ad_id" class="form-control">
                        <option value="">Select Ad</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_template_id">Template</label>
                    <select class="form-control" name="template_id" id="modal_template_id">
                        <option value="">Select Template</option>
                        @foreach ($template as $tpl)
                        <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="saveAutomationBtn">Save changes</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // Function to fetch and populate dropdowns
    function fetchAndPopulate(url, dataKey, targetId, selectedValue = null) {
        return new Promise((resolve, reject) => {
            console.log(`Fetching data for ${targetId} with data:`, dataKey);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...dataKey
                },
                success: function (response) {
                    console.log(`Response for ${targetId}:`, response);
                    const target = $(targetId);
                    target.prop('disabled', false).html('<option value="">Select</option>');

                    // Find the array key in the response (e.g., 'adsets', 'campaigns', 'ads')
                    const key = Object.keys(response).find(k => Array.isArray(response[k]));
                    if (key && response[key] && Array.isArray(response[key])) {
                        console.log(`Populating ${targetId} with ${key} data:`, response[key]);
                        $.each(response[key], function (i, obj) {
                            const val = obj[key.slice(0, -1) + '_id']; // e.g., adset_id
                            const text = obj[key.slice(0, -1) + '_name']; // e.g., adset_name
                            console.log(`Adding option: value=${val}, text=${text}, selected=${String(selectedValue) === String(val)}`);
                            target.append(`<option value="${val}" ${String(selectedValue) === String(val) ? 'selected' : ''}>${text}</option>`);
                        });
                        console.log(`Final HTML for ${targetId}:`, target.html());
                    } else {
                        console.error(`No valid array data found for ${key} in response`);
                    }
                    resolve();
                },
                error: function (xhr) {
                    console.error(`Error fetching data for ${targetId}:`, xhr.responseJSON);
                    reject(xhr);
                }
            });
        });
    }

    // On Ad Account Change (Main Form)
    $('#ad_account_id').on('change', function () {
        const adAccountId = $(this).val();
        console.log('Ad Account changed:', adAccountId);
        $('#campaign_id').html('<option value="">Select Campaign</option>').prop('disabled', true);
        $('#adset_id').html('<option value="">Select Ad Set</option>').prop('disabled', true);
        $('#ad_id').html('<option value="">Select Ad</option>').prop('disabled', true);

        if (adAccountId) {
            fetchAndPopulate(
                "{{ route('automation.fetchCampaigns') }}",
                { account_id: adAccountId },
                '#campaign_id'
            );
        }
    });

    // On Campaign Change (Main Form)
    $('#campaign_id').on('change', function () {
        const campaignId = $(this).val();
        console.log('Campaign changed:', campaignId);
        $('#adset_id').html('<option value="">Select Ad Set</option>').prop('disabled', true);
        $('#ad_id').html('<option value="">Select Ad</option>').prop('disabled', true);

        if (campaignId) {
            fetchAndPopulate(
                "{{ route('automation.fetchAdSets') }}",
                { campaign_id: campaignId },
                '#adset_id'
            );
        }
    });

    // On Ad Set Change (Main Form)
    $('#adset_id').on('change', function () {
        const adsetId = $(this).val();
        console.log('Ad Set changed:', adsetId);
        $('#ad_id').html('<option value="">Select Ad</option>').prop('disabled', true);

        if (adsetId) {
            fetchAndPopulate(
                "{{ route('automation.fetchAds') }}",
                { adset_id: adsetId },
                '#ad_id'
            );
        }
    });

    // Edit Automation (Modal)
    $(document).on('click', '.editAutomation', function () {
        const automation = $(this).data();
        console.log('Edit automation clicked, data:', automation);

        // Set initial values
        $('#modal_automation_id').val(automation.id);
        $('#modal_ad_account_id').val(automation.ad_account_id);
        $('#modal_template_id').val(automation.template_id);

        // Reset dropdowns
        $('#modal_campaign_id').html('<option value="">Select Campaign</option>').prop('disabled', true);
        $('#modal_adset_id').html('<option value="">Select Ad Set</option>').prop('disabled', true);
        $('#modal_ad_id').html('<option value="">Select Ad</option>').prop('disabled', true);

        // Populate campaigns
        fetchAndPopulate(
            "{{ route('automation.fetchCampaigns') }}",
            { account_id: automation.ad_account_id },
            '#modal_campaign_id',
            automation.campaign_id
        ).then(() => {
            // Populate ad sets if campaign_id exists
            if (automation.campaign_id) {
                console.log('Fetching ad sets for campaign_id:', automation.campaign_id);
                return fetchAndPopulate(
                    "{{ route('automation.fetchAdSets') }}",
                    { campaign_id: automation.campaign_id },
                    '#modal_adset_id',
                    automation.adset_id
                );
            } else {
                console.warn('No campaign_id provided, skipping ad set population');
            }
        }).then(() => {
            // Populate ads if adset_id exists
            if (automation.adset_id) {
                console.log('Fetching ads for adset_id:', automation.adset_id);
                return fetchAndPopulate(
                    "{{ route('automation.fetchAds') }}",
                    { adset_id: automation.adset_id },
                    '#modal_ad_id',
                    automation.ad_id
                );
            } else {
                console.warn('No adset_id provided, skipping ad population');
            }
        }).catch(err => {
            console.error('Error in editAutomation chain:', err);
        });
    });

    // Main Form Submission
$('#automationForm').on('submit', function (e) {
    e.preventDefault();
    console.log('Submitting main form:', $(this).serialize());

    $.ajax({
        url: "{{ route('automation.storeOrUpdate') }}",
        method: 'POST',
        data: $(this).serialize(),
        success: function (response) {
            console.log('Main form submission response:', response);
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Automation saved successfully.',
                    timer: 1800,
                    showConfirmButton: false
                });

                setTimeout(() => location.reload(), 2000);
            }
        },
        error: function (xhr) {
            console.error('Main form submission error:', xhr.responseJSON);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to save automation.',
                confirmButtonColor: '#d33'
            });
        }
    });
});
    
    $('#saveAutomationBtn').on('click', function () {
    console.log('Submitting modal form:', $('#modalAutomationForm').serialize());

    $.ajax({
        url: "{{ route('automation.storeOrUpdate') }}",
        method: 'POST',
        data: $('#modalAutomationForm').serialize(),
        success: function (response) {
            console.log('Modal form submission response:', response);
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Automation updated successfully.',
                    confirmButtonColor: '#3085d6',
                    timer: 1800,
                    showConfirmButton: false
                });

                $('#automationModal').modal('hide');

                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
        error: function (xhr) {
            console.error('Modal form submission error:', xhr.responseJSON);

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while saving automation.',
                confirmButtonColor: '#d33'
            });
        }
    });
});


    // Reset modal on close
    $('#automationModal').on('hidden.bs.modal', function () {
        console.log('Resetting modal dropdowns');
        $('#modal_campaign_id').html('<option value="">Select Campaign</option>').prop('disabled', true);
        $('#modal_adset_id').html('<option value="">Select Ad Set</option>').prop('disabled', true);
        $('#modal_ad_id').html('<option value="">Select Ad</option>').prop('disabled', true);
        $('#modal_automation_id').val('');
        $('#modal_ad_account_id').val('');
        $('#modal_template_id').val('');
    });
});

// Pusher Integration (unchanged from your original code)
setInterval(() => {
    fetch("/fetch-facebook-accounts")
        .then(res => res.json())
        .then(data => console.log("Fetched:", data))
        .catch(err => console.error("Error:", err));
}, 60000);
</script>



<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;

    const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
        forceTLS: true
    });

    const channel = pusher.subscribe("whatsapp-channel");

    channel.bind("trigger.whatsapp.send", function (data) {
        console.log("📡 Triggered via Pusher:", data);
        fetch(`/trigger-leads?key=${data.secret}`)
            .then((res) => res.text())
            .then((response) => console.log("📬 WhatsApp Trigger Response:", response))
            .catch((err) => console.error("Fetch error:", err));
    });
</script>

@endsection