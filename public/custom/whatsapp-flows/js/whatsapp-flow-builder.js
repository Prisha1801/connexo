
"use strict"
var default_data = $('#default_form_data').val();
var unique_flow_id = $('#unique_flow_id').val();
if(default_data.length  === 0 && unique_flow_id === undefined){
  default_data = [];
  var save_url = whatsapp_flows_optin_save_form_data;
  unique_flow_id = '';
}
else{
  default_data = JSON.parse(default_data);
  var save_url = whatsapp_flows_optin_edit_form_data

}


var options = {
    // Makes fields to be used for one time only
    allowOneTimeFields: ['button'],

    // Decides whether controls should be draggable or not
    draggableControls: true,

    // Disables action button
    disabledActionButtons: ['data'], // save, data, clear

    disabledSubtypes: {text: ['color','password'],},

    // set control position on left side
    controlPosition: 'left',
    disableFields: ['autocomplete','time','number','hidden','file','paragraph','button'],
    fields:[
        {
            class: "form-control",
            label: 'Submit Button',
            name: "button",
            style:"primary",
             type: "button"
        }
    ],
    defaultFields: default_data,
    //control orders
    controlOrder: ['header','text','textarea','checkbox-group','radio-group','select','date','button'],


    // event to be used when saving data
    onSave: function(e, formData) {
        e.preventDefault()
        var check_text_area_lenght = false;
        var check_checkbox_length = false;
        var check_radio_length =  false;
        var check_select_length =  false;
        var check_checkbox_option_value = false;
        var check_radio_option_value =  false;
        var check_select_option_value =  false;


        var parsed_form_data = JSON.parse(formData)
        // Shows error if button field doesn't exist
        if (Array.isArray(parsed_form_data)) {
            var submit_button_found = parsed_form_data.find((val) => {
                if (val && val.hasOwnProperty('type')) {
                      return val.type === 'button'
                }
            })
             parsed_form_data.find((val) => {
              if (val && val.hasOwnProperty("type")) {
                if (val.type === "textarea") {
                   var char_textarea = val.label.length;
                   if(char_textarea>= 20){
                     check_text_area_lenght = true;
                   }
                }
              }
            });
             parsed_form_data.find((val) => {
              if (val && val.hasOwnProperty("type")) {
                if (val.type === "checkbox-group") {

                  if(Array.isArray(val.values)){

                    $.each(val.values, function (index, element) {

                      if ((!(element.label === "" || element.label === null) && (element.value === null || element.value === "")) || ( (!(element.value === "" || element.value === null)  && (element.label === null || element.label === ""))))  {
                        check_checkbox_option_value = true;
                      }
                    });
                  }
                   var char  = val.label.length;
                   if(char >= 30){
                       check_checkbox_length = true;
                   }
                }
              }
            });
             parsed_form_data.find((val) => {
              if (val && val.hasOwnProperty("type")) {
                if (val.type === "radio-group") {
                  if(Array.isArray(val.values)){

                    $.each(val.values, function (index, element) {
                      if ((!(element.label === "" || element.label === null) && (element.value === null || element.value === "")) || ( (!(element.value === "" || element.value === null)  && (element.label === null || element.label === ""))))  {
                        check_radio_option_value = true;
                      }
                    });

                  }
                   var char = val.label.length;
                   if(char>= 30){
                     check_radio_length = true;
                   }
                }
              }
            });

            parsed_form_data.find((val) => {
              if (val && val.hasOwnProperty("type")) {
                if (val.type === "select") {
                  if(Array.isArray(val.values)){

                    $.each(val.values, function (index, element) {

                      if ((!(element.label === "" || element.label === null) && (element.value === null || element.value === "")) || ( (!(element.value === "" || element.value === null)  && (element.label === null || element.label === ""))))  {
                        check_select_option_value = true;
                      }
                    });

                  }
                   var char  = val.label.length;
                   if(char >= 30){
                     check_select_length = true ;
                   }
                }
              }
            });

            // if (! submit_button_found) {
            //   Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_forget_submit_button});
            //   return
            // }

            if ( check_text_area_lenght === true ) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_textarea_lenght_check});
              return
            }
            if (check_checkbox_length === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_checkbox_group_length_check});
              return
            }
            if (check_radio_length === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_radio_group_length_check});
              return
            }
            if (check_select_length === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_select_group_length_check});
              return
            }

            if (check_checkbox_option_value === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_checkbox_group_option_check});
              return
            }
            if (check_radio_option_value === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_radio_group_option_check});
              return
            }
            if (check_select_option_value === true) {
              Swal.fire({icon: 'warning',title: global_lang_warning, html: optin_form_lang_select_group_option_check});
              return
            }


        }

        // Starts loading state
        e.target.classList.remove('disabled', 'btn-progress')
        e.target.classList.add('disabled', 'btn-progress')

       // Prepares form data to be submitted
    //  console.log('Preparing form data to be submitted');
       var missing_input = false;
       var flow_name = $("#flow_name").val();
       var flow_update_id = $('#flow_update_id').val();
       var whatsapp_flow_category = $("#whatsapp_flow_category").val();
       var csrf_token = $("#csrf_token").val();
      // var whatsapp_bot_post_back = $("#whatsapp_bot_post_back").val();
       var screen_id = $('#optin_form_name').val();
       var regex = /^[a-zA-Z_]*$/;
       var form_title = $('#form_title').val();
       //||whatsapp_bot_post_back === '0'
       if(whatsapp_flow_category.length === 0 || flow_name === '' || screen_id ==='' || form_title ===''){
           missing_input = true;
       }
       if (!regex.test(screen_id)){
          Swal.fire({title: global_lang_warning, text: optin_form_lang_screein_id_validate,icon: 'warning',confirmButtonText: global_lang_ok});
          e.target.classList.remove('disabled', 'btn-progress')
          return false;
       }
       if(missing_input) {
          Swal.fire({title: global_lang_warning, text: global_lang_fill_required_fields,icon: 'warning',confirmButtonText: global_lang_ok});
          e.target.classList.remove('disabled', 'btn-progress')
          return false;
       }

     // whatsapp_bot_post_back : whatsapp_bot_post_back,
        var form_data = {
            flow_name : flow_name,
            whatsapp_flow_category : whatsapp_flow_category,
            screen_id: screen_id,
            form_title: form_title,
            unique_flow_id: unique_flow_id,
            form_data: formData
        }

        Swal.fire({
            title: 'Checking...',
            text: 'Please wait',
            imageUrl: progress_gif,
            showConfirmButton: false,
            allowOutsideClick: false
          });

        $.ajax({
            type: 'POST',
            url: save_url,
            dataType: 'JSON',
            data: form_data || null,
             beforeSend: function (xhr) {
              xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
              },
            success: function (response) {
                if (response) {
                      if (response.error === false) {
                        e.target.classList.remove('disabled', 'btn-progress')
                        if (parsed_form_data.length) {
                            document.getElementById('optin_form_name').value = ''
                            document.getElementById('form_title').value = ''
            
                            Swal.fire({
                                title: 'Finished!',
                                showConfirmButton: false,
                                timer: 1000
                              });

                            Swal.fire('WhatsApp flow created successfully',response.message,'WhatsApp flow created successfully').then((value) => {
                                window.location.href = whatsapp_flow_list;
                            });
                        }


                      } else {
                             e.target.classList.remove('disabled', 'btn-progress')
                             Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                             e.target.classList.remove('disabled', 'btn-progress')
                      }
                }
              },
           error: function (xhr, statusText) {
            const msg = handleAjaxError(xhr, statusText);
            Swal.fire({icon: 'error',title: global_lang_error,html: msg});
            return false;
        }
        })
      },
}
$('#optin-form-builder').formBuilder(options)

$(document).on('input', '.fld-label[contenteditable="true"]', function (e) {
  if (e.originalEvent?.data === ' ') return;
  $(this).on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
  });

  const sel = window.getSelection();
  const range = sel.getRangeAt(0);
  const startOffset = range.startOffset;

  const trimmedText = this.innerText.replace(/\n+/g, ' ');
  this.innerText = trimmedText;

  const newRange = document.createRange();
  const textNode = this.firstChild || this;
  newRange.setStart(textNode, Math.min(startOffset, trimmedText.length));
  newRange.collapse(true);
  sel.removeAllRanges();
  sel.addRange(newRange);
});

$(document).on('blur', '.fld-label[contenteditable="true"]', function (e) {
  const trimmedText = this.innerText.replace(/\s+/g, ' ');
  this.innerText = trimmedText;
});
