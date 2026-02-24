/* create_ads.js
   Merged, cleaned and defensive version for ctwa/create ads page.
   Place in public/js/create_ads.js and include via <script src="{{ asset('js/create_ads.js') }}"></script>
*/

(function () {
    'use strict';

    const $isJq = typeof jQuery !== 'undefined';
    const safe = id => document.getElementById(id) || null;
    const on = (el, ev, fn) => { if (el) el.addEventListener(ev, fn); };
    const qAll = sel => Array.from(document.querySelectorAll(sel || ''));

    const ready = (fn) => {
        if ($isJq) jQuery(fn);
        else if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    };

    ready(() => {

        const steps = qAll('.form-step');
        const indicators = qAll('.step');
        let currentStep = 0;

        const nextBtn = safe('nextBtn');
        const prevBtn = safe('prevBtn');
        const clearBtn = safe('clearBtn');
        const ctwaForm = safe('ctwaAdForm') || safe('ctwaForm');
        const mediaInput = safe('mediaFile');
        const previewImage = safe('previewImage');
        const previewVideo = safe('previewVideo');

        const durationPicker = safe('durationPicker');
        const durationSlider = safe('durationSlider');
        const durationDisplay = safe('durationDisplay');
        const dailyBudget = safe('dailyBudget');
        const estimatedBudget = safe('estimatedBudget');

        const summaryFields = {
            name: safe('summaryName'),
            caption: safe('summaryCaption'),
            headline: safe('summaryHeadline'),
            website: safe('summaryWebsite'),
            cta: safe('summaryCTA'),
            prefilled: safe('summaryPrefilled'),
            age: safe('summaryAge'),
            gender: safe('summaryGender'),
            budget: safe('summaryBudget'),
            startDate: safe('summaryStartDate'),
            duration: safe('summaryDuration'),
            country: safe('summaryCountry'),
            interests: safe('summaryInterests')
        };

        const preview = {
            caption: safe('previewCaption'),
            headline: safe('previewHeadline'),
            whatsappButton: safe('previewWhatsappButton') || safe('previewCTAButton'),
            prefilled: safe('previewPrefilled'),
            country: safe('previewCountry'),
            interests: safe('previewInterests'),
            profileImg: document.querySelector('.preview-profile'),
            pageName: document.querySelector('.page-name')
        };

        const $country = $isJq ? jQuery('#country') : null;
        const $targeting = $isJq ? jQuery('#targeting') : null;

        /* Flatpickr */
        if (typeof flatpickr !== 'undefined' && durationPicker) {
            try {
                flatpickr(durationPicker, {
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    defaultDate: 'today'
                });
            } catch (e) {
                console.warn('flatpickr init failed', e);
            }
        }

        /* Select2 */
        if ($isJq && typeof jQuery().select2 === 'function') {
            if ($country && $country.length) {
                $country.select2({
                    placeholder: 'Search location...',
                    minimumInputLength: 1,
                    ajax: {
                        url: '/meta/locations',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results || [] }),
                        cache: true
                    },
                    width: '100%'
                });
            }
            if ($targeting && $targeting.length) {
                $targeting.select2({
                    placeholder: 'Search interests...',
                    minimumInputLength: 2,
                    ajax: {
                        url: '/meta/meta-interests',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({
                            results: (data.data || []).map(item => ({ id: item.id, text: item.name }))
                        }),
                        cache: true
                    },
                    width: '100%'
                });
            }
        }

        const updateStep = () => {
            if (steps.length) {
                steps.forEach((step, i) => step.classList.toggle('active', i === currentStep));
            }
            if (indicators.length) {
                indicators.forEach((step, i) => step.classList.toggle('active', i === currentStep));
            }
            if (prevBtn) prevBtn.disabled = currentStep === 0;
            if (nextBtn) {
                nextBtn.innerText = currentStep === steps.length - 1 ? 'Submit' : 'Next';
            }
            if (currentStep === steps.length - 1) fillSummary();
        };

        const validateStep = (stepIndex) => {
            if (!steps[stepIndex]) return true;
            const inputs = steps[stepIndex].querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;
            inputs.forEach(input => {
                const id = input.id || '';
                if (!input.value || (id === 'dailyBudget' && parseFloat(input.value || 0) < 83.6)) {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
                if (id === 'targeting' && $isJq && jQuery('#targeting').length) {
                    const val = jQuery('#targeting').val() || [];
                    if (val.length === 0) {
                        input.classList.add('is-invalid');
                        valid = false;
                    }
                }
            });
            return valid;
        };

        /* Step indicator clicks — data-step is 0,1,2,3 */
        indicators.forEach(indicator => {
            const stepIndex = parseInt(indicator.dataset.step != null ? indicator.dataset.step : indicator.getAttribute('data-step') || '0', 10);
            indicator.addEventListener('click', () => {
                if (stepIndex > currentStep && !validateStep(currentStep)) return;
                currentStep = stepIndex;
                updateStep();
            });
            indicator.addEventListener('keypress', (e) => { if (e.key === 'Enter') indicator.click(); });
        });

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (!validateStep(currentStep)) return;

                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateStep();
                    return;
                }

                if (!ctwaForm) {
                    console.warn('Form element not found; submit prevented.');
                    return;
                }

                const geoTargetingInput = safe('geo_targeting');
                if (geoTargetingInput && $isJq && $country) {
                    const vals = $country.val() || [];
                    geoTargetingInput.value = JSON.stringify(vals);
                }

                const btn = nextBtn;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                btn.disabled = true;

                const formData = new FormData(ctwaForm);
                const formAction = ctwaForm.getAttribute('action') || '/meta/ads/create';
                const csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

                fetch(formAction, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Ad Created!',
                                    text: `Ad ID: ${data.ad_id || 'N/A'}`,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = '/ctwa';
                                });
                            } else {
                                alert('Ad created. Ad ID: ' + (data.ad_id || 'N/A'));
                                window.location.href = '/ctwa';
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Server error'
                                });
                            } else {
                                alert(data.message || 'Server error');
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Create ad error', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error creating ad.'
                            });
                        } else {
                            alert('Error creating ad.');
                        }
                    })
                    .finally(() => {
                        btn.innerHTML = 'Submit';
                        btn.disabled = false;
                    });
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentStep > 0) { currentStep--; updateStep(); }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                const formEl = ctwaForm || safe('ctwaForm');
                if (formEl) formEl.reset();

                if ($isJq) {
                    if ($country && $country.length) $country.val(null).trigger('change');
                    if ($targeting && $targeting.length) $targeting.val(null).trigger('change');
                }

                if (durationPicker && durationPicker._flatpickr) durationPicker._flatpickr.setDate('today');
                if (durationSlider && durationDisplay) {
                    durationSlider.value = 30;
                    durationDisplay.innerText = 30;
                }
                if (estimatedBudget) estimatedBudget.innerText = '0';
                if (preview.caption) preview.caption.innerText = 'Enter caption';
                if (preview.headline) preview.headline.innerText = 'Enter headline';
                if (previewImage) { previewImage.style.display = 'none'; previewImage.removeAttribute('src'); }
                if (previewVideo) { previewVideo.style.display = 'none'; previewVideo.removeAttribute('src'); }
                if (preview.country) preview.country.innerText = 'Not selected';
                if (preview.interests) preview.interests.innerText = 'No interests selected';
                ['adName', 'adCaption', 'headline', 'whatsappButtonText', 'prefilledMessage'].forEach(id => {
                    const ct = safe(id + 'Count');
                    if (ct) ct.textContent = '0';
                });
            });
        }

        const updateBudget = () => {
            const daily = parseFloat((dailyBudget && dailyBudget.value) || 0);
            const days = parseInt((durationSlider && durationSlider.value) || 0) || 0;
            if (durationDisplay) durationDisplay.innerText = days;
            if (estimatedBudget) estimatedBudget.innerText = ((daily * days) || 0).toFixed(2);
        };

        if (dailyBudget) dailyBudget.addEventListener('input', updateBudget);
        if (durationSlider) durationSlider.addEventListener('input', updateBudget);

        const fillSummary = () => {
            const getVal = id => (safe(id) && safe(id).value) || 'Not provided';
            if (summaryFields.name) summaryFields.name.innerText = getVal('adName') || 'Not provided';
            if (summaryFields.caption) summaryFields.caption.innerText = getVal('adCaption') || 'Not provided';
            if (summaryFields.headline) summaryFields.headline.innerText = getVal('headline') || 'Not provided';
            if (summaryFields.website) summaryFields.website.innerText = getVal('websiteLink') || 'Not provided';
            if (summaryFields.cta) summaryFields.cta.innerText = getVal('whatsappButtonText') || 'WhatsApp';
            if (summaryFields.prefilled) summaryFields.prefilled.innerText = getVal('prefilledMessage') || 'Not provided';

            if (safe('ageFrom') && safe('ageTo') && summaryFields.age) {
                summaryFields.age.innerText = safe('ageFrom').value + ' - ' + safe('ageTo').value;
            }
            if (safe('gender') && summaryFields.gender) {
                const sel = safe('gender');
                summaryFields.gender.innerText = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : 'Not provided';
            }
            if (summaryFields.budget) summaryFields.budget.innerText = (estimatedBudget && estimatedBudget.innerText) || '0';
            if (summaryFields.startDate) summaryFields.startDate.innerText = (durationPicker && durationPicker.value) || 'Not provided';
            if (summaryFields.duration) summaryFields.duration.innerText = (durationSlider && durationSlider.value) || '0';

            if (summaryFields.country && $isJq && $country && $country.length) {
                const cs = ($country.val() || []).length ? $country.find('option:selected').map((i, o) => o.text).get().join(', ') : 'Not provided';
                summaryFields.country.innerText = cs || 'Not provided';
            } else if (summaryFields.country) {
                const sel = safe('country');
                summaryFields.country.innerText = (sel && sel.selectedOptions ? Array.from(sel.selectedOptions).map(o => o.text).join(', ') : 'Not provided');
            }

            if (summaryFields.interests && $isJq && $targeting && $targeting.length) {
                const it = ($targeting.val() || []).length ? $targeting.find('option:selected').map((i, o) => o.text).get().join(', ') : 'Not provided';
                summaryFields.interests.innerText = it || 'Not provided';
            } else if (summaryFields.interests) {
                const sel = safe('targeting');
                summaryFields.interests.innerText = (sel && sel.selectedOptions ? Array.from(sel.selectedOptions).map(o => o.text).join(', ') : 'Not provided');
            }
        };

        ['adName', 'adCaption', 'headline', 'whatsappButtonText', 'prefilledMessage'].forEach(id => {
            const input = safe(id);
            const countEl = safe(id + 'Count');
            if (input && countEl) {
                input.addEventListener('input', () => {
                    countEl.textContent = input.value.length;
                    input.classList.toggle('is-invalid', !input.value);
                });
            }
        });

        if (safe('adCaption') && preview.caption) {
            safe('adCaption').addEventListener('input', e => {
                preview.caption.innerText = e.target.value || 'Enter caption';
            });
        }
        if (safe('headline') && preview.headline) {
            safe('headline').addEventListener('input', e => {
                preview.headline.innerText = e.target.value || 'Enter headline';
            });
        }
        if (safe('whatsappButtonText') && preview.whatsappButton) {
            safe('whatsappButtonText').addEventListener('input', e => {
                preview.whatsappButton.innerText = e.target.value || 'WhatsApp';
            });
        }
        if (safe('prefilledMessage') && preview.prefilled) {
            safe('prefilledMessage').addEventListener('input', e => {
                preview.prefilled.innerText = e.target.value || 'Prefilled message will appear here...';
            });
        }

        if (safe('country') && preview.country) {
            const updateCountryPreview = () => {
                if ($isJq && $country && $country.length) {
                    const selText = ($country.find('option:selected').map((i, o) => o.text).get().join(', ')) || 'Not selected';
                    preview.country.innerText = selText;
                } else {
                    const sel = safe('country');
                    preview.country.innerText = (sel && sel.selectedOptions && sel.selectedOptions[0]) ? sel.selectedOptions[0].text : 'Not selected';
                }
            };
            const nativeCountry = safe('country');
            if (nativeCountry) nativeCountry.addEventListener('change', updateCountryPreview);
            if ($isJq && $country && $country.length) $country.on('change', updateCountryPreview);
        }

        if (safe('targeting') && preview.interests) {
            const updateTargetingPreview = () => {
                if ($isJq && $targeting && $targeting.length) {
                    const text = ($targeting.find('option:selected').map((i, o) => o.text).get().join(', ')) || 'No interests selected';
                    preview.interests.innerText = text;
                } else {
                    const sel = safe('targeting');
                    preview.interests.innerText = sel && sel.selectedOptions ? Array.from(sel.selectedOptions).map(o => o.text).join(', ') : 'No interests selected';
                }
            };
            const nativeTarget = safe('targeting');
            if (nativeTarget) nativeTarget.addEventListener('change', updateTargetingPreview);
            if ($isJq && $targeting && $targeting.length) $targeting.on('change', updateTargetingPreview);
        }

        let currentMediaURL = null;
        const noImageUrl = '/assets/images/no-image.png';

        if (mediaInput) {
            mediaInput.addEventListener('change', e => {
                const file = e.target.files ? e.target.files[0] : null;

                if (currentMediaURL) {
                    URL.revokeObjectURL(currentMediaURL);
                    currentMediaURL = null;
                }

                if (!file) {
                    if (previewImage) {
                        previewImage.src = noImageUrl;
                        previewImage.style.display = 'block';
                    }
                    if (previewVideo) {
                        previewVideo.style.display = 'none';
                        previewVideo.removeAttribute('src');
                    }
                    return;
                }

                const url = URL.createObjectURL(file);
                currentMediaURL = url;

                if (file.type.startsWith('image/')) {
                    if (previewImage) { previewImage.src = url; previewImage.style.display = 'block'; }
                    if (previewVideo) { previewVideo.style.display = 'none'; previewVideo.removeAttribute('src'); }
                } else if (file.type.startsWith('video/')) {
                    if (previewVideo) { previewVideo.src = url; previewVideo.style.display = 'block'; }
                    if (previewImage) { previewImage.style.display = 'none'; previewImage.removeAttribute('src'); }
                } else {
                    if (previewImage) { previewImage.src = noImageUrl; previewImage.style.display = 'block'; }
                    if (previewVideo) { previewVideo.style.display = 'none'; previewVideo.removeAttribute('src'); }
                }
            });
        }

        if ($isJq) {
            (function loadPages() {
                const sel = jQuery('#pageSelector');
                if (!sel.length) return;
                jQuery.ajax({
                    url: '/meta/pages',
                    method: 'GET',
                    success: function (response) {
                        sel.empty().append('<option value="">Select a Page</option>');
                        (response.pages || []).forEach(page => {
                            sel.append('<option value="' + page.id + '" data-token="' + (page.access_token || '') + '">' + page.name + '</option>');
                        });
                    },
                    error: function () {
                        sel.empty().append('<option value="">Failed to load pages</option>');
                    }
                });
            })();

            (function loadAdAccounts() {
                const sel = jQuery('#adAccountSelector');
                if (!sel.length) return;
                jQuery.ajax({
                    url: '/meta/ad-accounts',
                    method: 'GET',
                    success: function (response) {
                        sel.empty().append('<option value="">Select Ad Account</option>');
                        (response || []).forEach(account => {
                            sel.append('<option value="' + account.id + '">' + (account.name || 'Ad Account') + ' (' + account.id + ')</option>');
                        });
                    },
                    error: function () {
                        sel.empty().append('<option value="">Failed to load ad accounts</option>');
                    }
                });
            })();

            jQuery('#pageSelector').on('change', function () {
                const pageId = jQuery(this).val();
                const pageToken = jQuery('option:selected', this).data('token');
                if (!pageId || !pageToken) return;

                jQuery.ajax({
                    url: '/meta/page-profile',
                    method: 'POST',
                    data: {
                        _token: jQuery('meta[name="csrf-token"]').attr('content'),
                        page_id: pageId,
                        page_token: pageToken
                    },
                    success: function (data) {
                        if (data.picture && preview.profileImg) preview.profileImg.src = data.picture;
                        if (data.name && preview.pageName) preview.pageName.textContent = data.name;
                    },
                    error: function () { alert('Failed to fetch page profile.'); }
                });
            });
        }

        updateStep();
        updateBudget();
    });
})();
