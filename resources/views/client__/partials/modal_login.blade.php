@if (session('first_login'))
    <button type="button" id="button_first_login" class="btn btn-info d-none" data-bs-toggle="modal"
        data-bs-target="#kt_first_login_google_face">
        Modal
    </button>

    <div class="modal fade" tabindex="-1" id="kt_first_login_google_face">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ __('Antes de Comenzar') }}
                    </h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="alert bg-dark d-flex flex-column align-items-center flex-sm-row p-5">
                            <i class="ki-solid ki-shield-tick fs-2hx text-white me-4"></i>
                            <div class="d-flex flex-column text-white pe-0 pe-sm-10">
                                <h4 class="mb-1 text-white">Estimad@ <span>{{ auth()->user()->name }}</span></h4>
                                <span>Para completar el registro favor
                                    proporcionar lo siguientes datos para recibir notificaciones y poder abrir tickets
                                    de
                                    asistencia.</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Whatsapp Phone</label>
                                <div class="fv-row mb-4 d-flex align-items-center">
                                    <span><i class="ki-duotone ki-whatsapp fs-2x mx-4 ki-graph-up text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i></span>
                                    <input id="phone" name="phone" required
                                        class="form-control form-control-lg form-control-solid" type="tel">
                                </div>
                            </div>
                            <input type="hidden" id="country_code" name="country_code" value="" />
                            <div class="col-12">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Empresa</label>
                                <input type="text" name="name_company"
                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                    placeholder="Ingrese el nombre de su empresa" required />
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-info">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
