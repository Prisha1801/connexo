$(document).ready(function() {
const steps = document.querySelectorAll(".form-step");
const indicators = document.querySelectorAll(".step");
let currentStep = 0;

$('#country').select2({
  placeholder: "Serach location (multiple)",
  width: '100%',
  allowClear: true,
  dropdownCssClass: 'custom-select2-dropdown',
  selectionCssClass: 'custom-select2-selection',
  templateResult: formatOption,
  templateSelection: formatSelection
});
$('#targeting').select2({
  placeholder: "Select interests (multiple)",
  width: '100%',
  allowClear: true,
  dropdownCssClass: 'custom-select2-dropdown',
  selectionCssClass: 'custom-select2-selection',
  templateResult: formatOption,
  templateSelection: formatSelection
});

// Custom formatting for Select2 options
function formatOption(option) {
  if (!option.id) return option.text;
  return $('<span>' + option.text + '</span>');
}

function formatSelection(option) {
  return option.text || option.placeholder;
}

// Initialize Flatpickr
flatpickr("#durationPicker", {
  dateFormat: "Y-m-d",
  minDate: "today",
  defaultDate: "today"
});

// Step navigation
const updateStep = () => {
  steps.forEach((step, i) => step.classList.toggle("active", i === currentStep));
  indicators.forEach((step, i) => step.classList.toggle("active", i === currentStep));
  document.getElementById("prevBtn").disabled = currentStep === 0;
  document.getElementById("nextBtn").innerText = currentStep === steps.length - 1 ? "Submit" : "Next";
  if (currentStep === steps.length - 1) fillSummary();
};

// Validate form step
const validateStep = (step) => {
  const inputs = steps[step].querySelectorAll("input[required], select[required]");
  let valid = true;
  inputs.forEach(input => {
    if (!input.value || (input.id === "dailyBudget" && parseFloat(input.value) < 83.6)) {
      input.classList.add("is-invalid");
      valid = false;
    } else {
      input.classList.remove("is-invalid");
    }
    if (input.id === "targeting" && !$('#targeting').val().length) {
      input.classList.add("is-invalid");
      valid = false;
    }
  });
  return valid;
};

// Step navigation clicks
indicators.forEach(indicator => {
  indicator.addEventListener("click", () => {
    if (currentStep < parseInt(indicator.dataset.step) && !validateStep(currentStep)) return;
    currentStep = parseInt(indicator.dataset.step);
    updateStep();
  });
  indicator.addEventListener("keypress", (e) => {
    if (e.key === "Enter") indicator.click();
  });
});

document.getElementById("nextBtn").onclick = () => {
  if (!validateStep(currentStep)) return;

  if (currentStep < steps.length - 1) {
    currentStep++;
    updateStep();
  } else {
    
    const selectedCountryValues = $('#country').val(); 
    document.getElementById('geo_targeting').value = JSON.stringify(selectedCountryValues);
    const nextBtn = document.getElementById("nextBtn");
    nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    nextBtn.disabled = true;

    const form = document.getElementById("ctwaAdForm");
    const formData = new FormData(form);

    fetch("/meta/ads/create", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(`Ad Created! Ad ID: ${data.ad_id}`);
        nextBtn.innerHTML = "Submit";
        nextBtn.disabled = false;
      } else {
        console.error(data);
        alert("Failed to create ad.");
        nextBtn.innerHTML = "Submit";
        nextBtn.disabled = false;
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error creating ad.");
      nextBtn.innerHTML = "Submit";
      nextBtn.disabled = false;
    });
  }
};


document.getElementById("prevBtn").onclick = () => {
  if (currentStep > 0) {
    currentStep--;
    updateStep();
  }
};

// Clear form
document.getElementById("clearBtn").onclick = () => {
  document.getElementById("ctwaForm").reset();
  $('#country').val('').trigger('change');
  $('#targeting').val('').trigger('change');
  document.getElementById("durationPicker")._flatpickr.setDate("today");
  document.getElementById("durationSlider").value = 30;
  document.getElementById("durationDisplay").innerText = 30;
  document.getElementById("estimatedBudget").innerText = "0";
  document.getElementById("previewCaption").innerText = "Enter your caption here...";
  document.getElementById("previewHeadline").innerText = "Enter headline here...";
  document.getElementById("previewImage").style.display = "none";
  document.getElementById("previewVideo").style.display = "none";
  document.getElementById("previewCountry").innerText = "Not selected";
  document.getElementById("previewInterests").innerText = "No interests selected";
  ['adName', 'adCaption', 'headline'].forEach(id => {
    document.getElementById(`${id}Count`).textContent = "0";
  });
};

// Budget calculation
const updateBudget = () => {
  const daily = parseFloat(document.getElementById("dailyBudget").value || 0);
  const days = parseInt(document.getElementById("durationSlider").value);
  document.getElementById("durationDisplay").innerText = days;
  document.getElementById("estimatedBudget").innerText = (daily * days).toFixed(2) || 0;
};

document.getElementById("dailyBudget").addEventListener("input", updateBudget);
document.getElementById("durationSlider").addEventListener("input", updateBudget);

// Summary
const fillSummary = () => {
  document.getElementById("summaryName").innerText = document.getElementById("adName").value || "Not provided";
  document.getElementById("summaryCaption").innerText = document.getElementById("adCaption").value || "Not provided";
  document.getElementById("summaryHeadline").innerText = document.getElementById("headline").value || "Not provided";
  document.getElementById("summaryWebsite").innerText = document.getElementById("websiteLink").value || "Not provided";
  document.getElementById("summaryCTA").innerText = document.getElementById("whatsappButtonText").value || "WhatsApp";
  document.getElementById("summaryPrefilled").innerText = document.getElementById("prefilledMessage").value || "Not provided";
  document.getElementById("summaryAge").innerText = `${document.getElementById("ageFrom").value} - ${document.getElementById("ageTo").value}`;
  document.getElementById("summaryGender").innerText = document.getElementById("gender").options[document.getElementById("gender").selectedIndex].text;
  document.getElementById("summaryBudget").innerText = document.getElementById("estimatedBudget").innerText;
  document.getElementById("summaryStartDate").innerText = document.getElementById("durationPicker").value || "Not provided";
  document.getElementById("summaryDuration").innerText = document.getElementById("durationSlider").value;
//   document.getElementById("summaryCountry").innerText = document.getElementById("country").options[document.getElementById("country").selectedIndex]?.text || "Not provided";
  const countries = Array.from(document.getElementById("country").selectedOptions).map(option => option.text).join(", ");
  document.getElementById("summaryCountry").innerText = countries || "Not provided";
  const interests = Array.from(document.getElementById("targeting").selectedOptions).map(option => option.text).join(", ");
  document.getElementById("summaryInterests").innerText = interests || "Not provided";
};

// Character counters
['adName', 'adCaption', 'headline'].forEach(id => {
  const input = document.getElementById(id);
  const count = document.getElementById(`${id}Count`);
  input.addEventListener('input', () => {
    count.textContent = input.value.length;
    input.classList.toggle("is-invalid", !input.value);
  });
});

// Live Preview
document.getElementById("adCaption").addEventListener("input", e => {
  document.getElementById("previewCaption").innerText = e.target.value || "Enter caption";
});
document.getElementById("headline").addEventListener("input", e => {
  document.getElementById("previewHeadline").innerText = e.target.value || "Enter headline";
});
// ✅ WhatsApp CTA Live Preview
document.getElementById("whatsappButtonText").addEventListener("input", e => {
  document.getElementById("previewWhatsappButton").innerText = e.target.value || "WhatsApp";
});

// ✅ Prefilled WhatsApp Message Live Preview
document.getElementById("prefilledMessage").addEventListener("input", e => {
  document.getElementById("previewPrefilled").innerText = e.target.value || "Prefilled message will appear here...";
});
document.getElementById("country").addEventListener("change", () => {
  document.getElementById("previewCountry").innerText = document.getElementById("country").options[document.getElementById("country").selectedIndex]?.text || "Not selected";
});
document.getElementById("targeting").addEventListener("change", () => {
  const interests = Array.from(document.getElementById("targeting").selectedOptions).map(option => option.text).join(", ");
  document.getElementById("previewInterests").innerText = interests || "No interests selected";
});
const mediaInput = document.getElementById("mediaFile");
const previewImage = document.getElementById("previewImage");
const previewVideo = document.getElementById("previewVideo");
const noImageUrl = "/assets/images/no-image.png"; 
let currentMediaURL = null;

mediaInput.addEventListener("change", e => {
  const file = e.target.files[0];

  // Revoke previously created object URL
  if (currentMediaURL) {
    URL.revokeObjectURL(currentMediaURL);
    currentMediaURL = null;
  }

  if (file) {
    const url = URL.createObjectURL(file);
    currentMediaURL = url;

    if (file.type.startsWith("image/")) {
      previewImage.src = url;
      previewImage.style.display = "block";
      previewVideo.style.display = "none";
      previewVideo.removeAttribute("src");
    } else if (file.type.startsWith("video/")) {
      previewVideo.src = url;
      previewVideo.style.display = "block";
      previewImage.style.display = "none";
      previewImage.removeAttribute("src");
    } else {
      // Unsupported file type – show fallback image
      previewImage.src = noImageUrl;
      previewImage.style.display = "block";
      previewVideo.style.display = "none";
      previewVideo.removeAttribute("src");
    }
  } else {
    // No file selected – show fallback image
    previewImage.src = noImageUrl;
    previewImage.style.display = "block";
    previewVideo.style.display = "none";
    previewVideo.removeAttribute("src");
  }
});
});

$(document).ready(function () {
    $('#country').select2({
        placeholder: "Search location...",
        minimumInputLength: 1,
        ajax: {
            url: '/meta/locations',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        width: '100%'
    });
});

$(document).ready(function () {
  $('#targeting').select2({
    placeholder: "Search interests...",
    minimumInputLength: 2,
    ajax: {
      url: '/meta/meta-interests',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return { q: params.term }; 
      },
      processResults: function (data) {
        return {
          results: data.data.map(item => ({
            id: item.id,
            text: item.name
          }))
        };
      },
      cache: true
    },
    width: '100%'
  });
});

// $(document).ready(function () {
//     $.ajax({
//         url: '/meta/pages',
//         method: 'GET',
//         success: function (response) {
//             const selector = $('#pageSelector');
//             selector.empty().append(`<option value="">Select a Page</option>`);

//             response.pages.forEach(page => {
//                 selector.append(
//                     `<option value="${page.id}" data-token="${page.access_token}">${page.name}</option>`
//                 );
//             });
//         },
//         error: function () {
//             alert('Failed to load pages.');
//         }
//     });
// });

// $(document).ready(function () {
//     $.ajax({
//         url: '/meta/ad-accounts',
//         method: 'GET',
//         success: function (response) {
//             const selector = $('#adAccountSelector');
//             selector.empty().append(`<option value="">Select Ad Account</option>`);

//             response.forEach(account => {
//                 selector.append(
//                     `<option value="${account.id}">${account.name || 'Ad Account'} (${account.id})</option>`
//                 );
//             });
//         },
//         error: function () {
//             alert('Failed to load ad accounts.');
//         }
//     });
// });
   
$('#pageSelector').on('change', function () {
    const pageId = $(this).val();
    const pageToken = $('option:selected', this).data('token');

    if (!pageId || !pageToken) return;

    $.ajax({
        url: '/meta/page-profile',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            page_id: pageId,
            page_token: pageToken
        },
        success: function (data) {
            $('.preview-profile').attr('src', data.picture).attr('alt', data.name);
            $('.preview-box p:first').text(data.name);
        },
        error: function () {
            alert('Failed to fetch page profile.');
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
  const btnInput = document.getElementById('whatsappButtonText');
  const btnPreview = document.getElementById('previewWhatsappButton');
  const btnCount = document.getElementById('whatsappButtonTextCount');

  btnInput.addEventListener('input', function () {
    const value = btnInput.value.trim();
    btnPreview.textContent = value || 'WhatsApp';
    btnCount.textContent = value.length;
  });
});
// search
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('customSearch');
    const searchForm = document.getElementById('searchForm');

    // Submit form on Enter key
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            searchForm.submit();
        }
    });
});


// Modal
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.addEventListener('click', () => {
            const adId = row.getAttribute('data-id');
            if (adId) {
                window.location.href = `/ads/${adId}`; 
            }
        });
    });
});

function fetchLeads() {
const start = document.getElementById('start-date').value;
const end = document.getElementById('end-date').value;

fetch(`/leads/filter?start=${start}&end=${end}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('leads-summary').innerHTML = `
            <div class="col-md-4">
                <div class="lead-box">
                    <div class="summary-title">Total</div>
                    <div class="summary-value">${data.total}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lead-box success">
                    <div class="summary-title">New</div>
                    <div class="summary-value">${data.new}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lead-box warning">
                    <div class="summary-title">Existing</div>
                    <div class="summary-value">${data.existing}</div>
                </div>
            </div>
        `;
    });
}

document.getElementById('preset-range').addEventListener('change', function () {
    const days = parseInt(this.value);
    const end = new Date().toISOString().slice(0, 10);
    const start = new Date(Date.now() - days * 864e5).toISOString().slice(0, 10);
    document.getElementById('start-date').value = start;
    document.getElementById('end-date').value = end;
    fetchLeads();
});

document.getElementById('start-date').addEventListener('change', fetchLeads);
document.getElementById('end-date').addEventListener('change', fetchLeads);    
    
    


$(document).ready(function () {
    // ✅ Show/hide schedule input
    $('#scheduleToggle').on('change', function () {
        $('#scheduleInput').toggleClass('d-none', !this.checked);
    });

    // ✅ Select/Deselect all leads
    $('#selectAllLeads').on('change', function () {
        $('.leadCheckbox').prop('checked', this.checked);
    });

    // ✅ If any individual checkbox unchecked → uncheck selectAll
    $(document).on('change', '.leadCheckbox', function () {
        if (!this.checked) {
            $('#selectAllLeads').prop('checked', false);
        } else if ($('.leadCheckbox:checked').length === $('.leadCheckbox').length) {
            $('#selectAllLeads').prop('checked', true);
        }
    });

    // ✅ Handle form submission with AJAX
    $('#campaignForm').on('submit', function (e) {
        e.preventDefault();

        let selectedLeads = [];
        $('.leadCheckbox:checked').each(function () {
            const leadData = $(this).data('lead');
            if (leadData && leadData.wa_id) {
                selectedLeads.push(leadData);
            }
        });

        if (selectedLeads.length === 0) {
            alert("⚠️ Please select at least one lead.");
            return;
        }

        // Prepare form data
        const form = $(this);
        const formData = {
            campaign_name: form.find('[name="campaign_name"]').val(),
            template_id: form.find('[name="template_id"]').val(),
            scheduled_at: form.find('[name="scheduled_at"]').val(),
            selected_leads: JSON.stringify(selectedLeads),
            _token: form.find('[name="_token"]').val()
        };

        // AJAX call
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            beforeSend: function () {
                form.find('button[type="submit"]').prop('disabled', true).text('Sending...');
            },
            success: function (response) {
                alert(`✅ Campaign processed. Success: ${response.success_count}, Failed: ${response.failed_count}`);
                $('#broadcastModal').modal('hide');
                form[0].reset();
                $('#scheduleInput').addClass('d-none');
                $('#selectAllLeads').prop('checked', false);
                $('.leadCheckbox').prop('checked', false);
                // window.location.reload();
            },
            error: function (xhr) {
                let msg = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                alert(`❌ Error: ${msg}`);
            },
            complete: function () {
                form.find('button[type="submit"]').prop('disabled', false).text('Send Now');
            }
        });
    });
});