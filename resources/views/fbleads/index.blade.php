@extends('layouts.app', ['title' => __('FB Campaigns Leads')])
@section('content')

<div class="header  pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🏢 {{__('FB Campaigns Leads')}}</h1>
              
              <a href="{{ route('admin.downlaodCsv') }}?downlodcsv=true" class="btn btn-sm btn-outline-primary">{{ __('Export CSV') }}</a>
              <a href="{{ route('admin.downlaodPDF') }}?downlodcsv=true" class="btn btn-sm btn-outline-primary">Export PDF</a>
            <div class="row align-items-center pt-2">
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <form>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="form-group-group" class="form-group col-md-12">
                                            <label class="form-control-label">{{__('Select Campaign')}}</label><br>
                                            <select class="form-control form-control-alternative  select2init " name="campaign" id="campaign">
                                                <option disabled="" selected="" value=""> Select Campaign</option>
                                                @if(isset($campaignFilter))
                                                @foreach($campaignFilter as $campaign)
                                                    <option  value="{{$campaign->campaign_id}}" {{(isset($_GET['campaign']) && $_GET['campaign']==$campaign->campaign_id)?'selected':''}}>{{$campaign->campaign_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-6">
                                    <div class="row">
                                        @if ($parameters)
                                        <div class="col-md-4">
                                            <a href="{{ Request::url() }}" class="btn btn-sm ">{{ __('crud.clear_filters') }}</a>
                                        </div>
                                        @else
                                        <div class="col-md-8"></div>
                                        @endif

                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-sm btn-primary ">{{ __('crud.filter') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4 text-right">
                            <a href="javascript:void(0);" class="btn btn-sm btn-primary syncFbLead"> {{ __('Sync FB Leads') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    @include('partials.flash')
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Details') }}</th>
                                <th scope="col">{{ __('Last Activity') }}</th>
                                <th scope="col">{{ __('Date Added') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fbLeads as $fblead)
                            <tr>
                                <td><a href="javascript:void(0);" class="leadDetails active" data-id="{{$fblead->id}}" data-name="{{$fblead->full_name}}">{{ $fblead->full_name }}</a></td>
                                <td>
                                    @php $detail =" via ".$fblead->campaign_name. " Full Name: ".$fblead->full_name @endphp
                                    @if($fblead->platform =='ig')
                                    @php $details ='Instagram Lead'.$detail; @endphp
                                    @endif
                                    @if($fblead->platform =='fb')
                                    @php $details ='Facebook Lead'.$detail; @endphp
                                    @endif
                                    <a href="javascript:void(0);" class="leadDetails active" data-id="{{$fblead->id}}" data-name="{{$fblead->full_name}}">{{\Illuminate\Support\Str::limit($details,40,'...')}}</a>
                                </td>
                                <td class="text-center">-</td>
                                <td>{{ $fblead->created_time->locale(Config::get('app.locale'))->isoFormat('LLLL') }}</td>
                            </tr>
                            @empty
                            <td colspan="4" class="text-center">No facebook leads found.</td>
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
<div class="modal fade" id="modal-lead-info" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalLeadTitle" class="modal-title notranslate"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="leadloader text-center align-items-center" style="min-height: 100px;">Loding....</div>
                <div class="leadHtml"></div>
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
                console.log(response);
                $(".syncFbLead").html(btnTxt);
                $(".syncFbLead").removeClass('disabled');
                alert(response.message);
                // window.location.reload();
            }

            function error_synced(response) {
                console.log(response);
                $(".syncFbLead").html(btnTxt);
                $(".syncFbLead").removeClass('disabled');
                alert(response.message);

            }
        });
        // js.notify('testig', 'error');
        $(".leadDetails").on("click", function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $("#modalLeadTitle").html(name);
            $("#modal-lead-info").modal('show');
            $('.leadloader').show();
            $(".leadHtml").hide();
            let formData = new FormData;
            formData.append('id', id);
            ajax_request("{{route('admin.fblead.get')}}", 'GET', formData, success_lead, error_lead);

            function success_lead(response) {
                if (response.status == true) {
                    $('.leadloader').hide();
                    $(".leadHtml").show();
                    $(".leadHtml").html(response.data.html);
                } else {
                    alert(response.message);
                }

            }

            function error_lead(response) {
                alert(response.message);
            }

        });

    });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;

    // Create Pusher connection
    const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
        cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        encrypted: true
    });

    // Subscribe to the Facebook Leads channel
    const channel = pusher.subscribe("fb-leads");

    // Listen for the leads.fetched event
    channel.bind("leads.fetched", function (data) {
        console.log("📡 Facebook Leads Fetched:", data);

        // Display real-time notification
        alert(`${data.message} (${data.newLeadsCount} new leads)`);

        fetch('/cron-sync-leads')
            .then(res => res.text())
            .then(html => {
                document.querySelector("#leadTable").innerHTML = html;
            })
            .catch(err => console.error("❌ Error refreshing table:", err));
    });
</script>

@endsection