<div class="card">
    <div class="card-body">
        <h2 class="text-center btn btn-primary">{{ __('Client Info') }}</h2>

        <div class="row">
            {{-- Full Name and Phone --}}
            <div class="col-md-6 mb-3">
                <h6 class="heading-small">Full Name</h6>
                <p>{{ $leadData->full_name ?? '-' }}</p>

                <h6 class="heading-small">Phone No</h6>
                <p>
                    @if (!empty($leadData->phone_number))
                        <a href="tel:{{ $leadData->phone_number }}">
                            {{ $leadData->phone_number }} <i class="fa fa-whatsapp text-success ms-1"></i>
                        </a>
                    @else
                        -
                    @endif
                </p>
            </div>

            {{-- Email and City --}}
            <div class="col-md-6 mb-3">
                <h6 class="heading-small">Email</h6>
                <p>
                    @if (!empty($leadData->email))
                        <a href="mailto:{{ $leadData->email }}">
                            <i class="fa fa-envelope me-1"></i> {{ $leadData->email }}
                        </a>
                    @else
                        -
                    @endif
                </p>

                <h6 class="heading-small">City</h6>
                <p>{{ $leadData->city ?? '-' }}</p>
            </div>

            {{-- Platform --}}
            <div class="col-md-6 mb-3">
                <h6 class="heading-small">Platform</h6>
                <p class="text-uppercase">{{ $leadData->platform ?? '-' }}</p>
            </div>

            {{-- Created At --}}
            <div class="col-md-6 mb-3">
                <h6 class="heading-small">Created At</h6>
                <p>{{ \Carbon\Carbon::parse($leadData->created_time)->toDayDateTimeString() }}</p>
            </div>

            {{-- Notes Section --}}
            <div class="col-12">
                <h6 class="heading-small">Notes</h6>
                <p>
                    @php
                        $detail = ' via ' . ($leadData->campaign_name ?? '-');
                        $notes = match($leadData->platform) {
                            'ig' => 'Instagram Lead' . $detail,
                            'fb' => 'Facebook Lead' . $detail,
                            default => 'Lead' . $detail,
                        };
                    @endphp
                    {{ $notes }}
                </p>
            </div>
        </div>
    </div>
</div>
