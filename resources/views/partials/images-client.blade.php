<div class="mt-1 form-group text-center">
    @isset($image['help'])
       <br /> <span class="small">{{ $image['help'] }}</span>
    @endisset

    <style>.image-input-placeholder { background-image: url("{{ asset('Metronic/assets') }}/media/svg/files/blank-image.svg"); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url("{{ asset('Metronic/assets') }}/media/svg/files/blank-image-dark.svg"); }</style>

    <div class="image-input image-input-outline image-input-placeholder image-input-empty" data-provides="fileinput" data-kt-image-input="true">

        <div class="image-input-wrapper" data-trigger="fileinput"
            style="width: 170px; height: 150px; background-image: url('{{ isset($image['value']) && !empty($image['value']) && strlen($image['value']) > 4 && $image['value'] != config('app.url') ? $image['value'] : '' }}'); background-size: cover; background-position: center;">
        </div>

        <label class="btn btn-icon btn-circle btn-active-color-primary w-40px h-40px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
            <i class="ki-outline ki-pencil fs-2"></i>
            <!--begin::Inputs-->
            <input type="file" name="{{ $image['name'] }}" accept="image/x-png,image/png,image/gif,image/jpeg">
            <input type="hidden" name="avatar_remove" />
            <!--end::Inputs-->
        </label>

        <span class="btn btn-icon btn-circle btn-active-color-primary w-40px h-40px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
            <i class="ki-outline ki-cross fs-1"></i>
        </span>

        <span class="btn btn-icon btn-circle btn-active-color-primary w-40px h-40px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
            <i class="ki-outline ki-cross fs-1"></i>
        </span>

    </div>

    @if ($errors->has($image['name']))
        <span class="text-danger"><strong>{{ $errors->first($image['name']) }}</strong></span>
    @endif
</div>
