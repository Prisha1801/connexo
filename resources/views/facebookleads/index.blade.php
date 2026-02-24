@extends('layouts.app', ['title' => __('FB Campaigns Leads')])
@section('content')

<div class="header  pb-8 pt-5 pt-md-8">
    <div class="container-fluid header-body d-flex justify-content-between align-items-center flex-wrap">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🏢 {{__('FB Campaigns Leads')}}</h1>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            <a href="{{ route('admin.downlaodCsv') }}?downlodcsv=true" class="btn btn-sm btn-outline-primary">{{ __('Export CSV') }}</a>
            <a href="{{ route('admin.downlaodPDF') }}?downlodcsv=true" class="btn btn-sm btn-outline-primary">Export PDF</a>
              <a href="{{ route('cron-fb-leads') }}?company_id=37" class="btn btn-sm btn-outline-primary">Refresh</a>
            <div class="row align-items-center pt-2">
            </div>
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
            <div class="card shadow">


                <div class="col-12">
                    @include('partials.flash')
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                       
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">{{ __('Sr.NO') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Details') }}</th>
                            <!--<th scope="col">{{ __('Last Activity') }}</th>-->
                            <th scope="col">{{ __('Date Added') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Action') }}</th> 
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($fbLeads as $fblead)
                                @php
                                    $rowNumber = $fbLeads->firstItem() + $loop->index;
                                    $detail = " via {$fblead->campaign_name}. Full Name: {$fblead->full_name}";
                                    $details = match($fblead->platform) {
                                        'ig' => 'Instagram Lead' . $detail,
                                        'fb' => 'Facebook Lead' . $detail,
                                        default => 'Lead' . $detail,
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ $fblead->full_name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($details, 40, '...') }}</td>
                                    <!--<td class="text-center">-</td>-->
                                    <td>{{ \Carbon\Carbon::parse($fblead->created_time)->locale(Config::get('app.locale'))->isoFormat('LLLL') }}</td>
                                    <td>{{ $fblead->whatsapp_sent_at ? 'Sent' : 'Not Sent' }}</td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary leadDetails"
                                            data-id="{{ $fblead->id }}"
                                            data-name="{{ e($fblead->full_name) }}">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No facebook leads found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                    <br /><br /><br />

                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        {{ $fbLeads->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="modal fade" id="modal-lead-info" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">-->
<!--    <div class="modal-dialog modal- modal-dialog-centered modal-lg" role="document">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 id="modalLeadTitle" class="modal-title notranslate"></h5>-->
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                    <span aria-hidden="true">×</span>-->
<!--                </button>-->
<!--            </div>-->
<!--            <div class="modal-body p-0">-->
<!--                <div class="leadloader text-center align-items-center" style="min-height: 100px;">Loding....</div>-->
<!--                <div class="leadHtml"></div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="modal fade" id="modal-lead-info" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h1 id="modalLeadTitle" class="modal-title notranslate text-white"></h1>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="leadloader text-center" style="min-height: 100px;">Loading...</div>
                <div class="leadHtml d-none"></div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('js')
<script type="text/javascript">
    $(function() {
        $(".syncFbLead").on("click", function() {
            let btnTxt = $(".syncFbLead").html();
            $(".syncFbLead").addClass('disabled');
            $(".syncFbLead").html('Sync in progress...');
            let data = new FormData();
            data.append('_token', "{{csrf_token()}}");
            ajax_request("{{route('admin.fblead.synced')}}", 'POST', data, success_synced, error_synced);

            function success_synced(response) {
                $(".syncFbLead").html(btnTxt);
                $(".syncFbLead").removeClass('disabled');
                alert(response.message);
                window.location.reload();
            }

            function error_synced(response) {
                $(".syncFbLead").html(btnTxt);
                $(".syncFbLead").removeClass('disabled');
                alert(response.message);

            }
        });
       
    });
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function () {
        $(".leadDetails").on("click", function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $("#modalLeadTitle").text(name || 'Lead Details');
            $("#modal-lead-info").modal('show');
            $(".leadloader").show();
            $(".leadHtml").addClass('d-none').html('');

            $.ajax({
                url: `{{ route('admin.fblead.get') }}?id=${id}`,
                method: 'GET',
                success: function (response) {
                    $(".leadloader").hide();
                    if (response.status && response.data?.html) {
                        $(".leadHtml").html(response.data.html).removeClass('d-none');
                    } else {
                        $(".leadHtml").html(`<div class="alert alert-warning">${response.message || 'No data found.'}</div>`).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    $(".leadloader").hide();
                    const message = xhr.responseJSON?.message || 'Something went wrong.';
                    $(".leadHtml").html(`<div class="alert alert-danger">${message}</div>`).removeClass('d-none');
                }
            });
        });
    });


</script>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection