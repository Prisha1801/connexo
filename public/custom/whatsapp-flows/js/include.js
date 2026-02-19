"use strict";
function is_email(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


function search_in_class(obj,class_name){
  var filter=$(obj).val().toUpperCase();
  $('.'+class_name).each(function(){
    var content=$(this).text().trim();

    if (content.toUpperCase().indexOf(filter) > -1) {
      $(this).css('display','');
    }
    else $(this).css('display','none');
  });
}

function search_in_div(obj,ul_id){  // obj = 'this' of jquery, ul_id = id of the ul
    var filter=$(obj).val().toUpperCase();
    $('#'+ul_id+' div').each(function(){
      var content=$(this).text().trim();

      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).css('display','');
      }
      else $(this).css('display','none');
    });
}

function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(';');
    for(var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i].trim();
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return null;
}

function hasNonAlphaNumericExceptUnderscore(input) {
    return /[^a-zA-Z0-9_]/.test(input);
}

$(document).ready(function() {


    if(areWeUsingScroll)
    {
        setTimeout(function(){
            $(".overflow-x,.overflow-y,.overflow-xy").niceScroll({
                cursorcolor:"#eee"
        }); }, 500);

        if($('#sidebar-menu').length){
            // $("#sidebar-menu").niceScroll({
            //     cursorcolor:"#eee"
            // });
            new PerfectScrollbar("#sidebar-menu");
            $(document).on('click','.sidebar-item.has-sub',function () {
                $("#sidebar-menu").getNiceScroll().resize();
            });
        }

        $(".makescroll-no-delay").niceScroll({
            cursorcolor:"#eee"
        });
    }




    if ($('.select2').hasClass("select2-hidden-accessible")) {
        $('.select2').select2('destroy');
    }else $('.select2').select2();

    $('.select2').select2();
	$(".select2Tag").select2({
		allowClear: true,
		tags: true
	});

    // $('.datepicker').datepicker({
    //     format: 'yyyy/mm/dd',
    //     startDate : today
    // });
    // $('.datepicker-anydate').datepicker({
    //     format: 'yyyy/mm/dd'
    // });

    $('.datetimepicker_x,.datetimepicker').datetimepicker({
      theme:'light',
      format:'Y-m-d H:i:s',
      closeOnDateSelect:true
    });

    $('.datepicker_x,.datepicker').datetimepicker({
      theme:'light',
      format:'Y-m-d',
      timepicker:false,
      closeOnDateSelect:true
    });

    $('.timepicker_x,.timepicker').datetimepicker({
      datepicker:false,
      format:'H:i:s',
      closeOnDateSelect:true
    });

    $('[data-toggle="tooltip"],[data-bs-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"],[data-bs-toggle="popover"]').popover();
    $('[data-toggle="popover"],[data-bs-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    // new Quill("#quilleditor", {
    //     theme: "snow"
    // });

    $(document).on('change','#datatableSelectAllRows',function(e){
        if ($(this).is(':checked')) $(".datatableCheckboxRow").prop("checked",true);
        else $(".datatableCheckboxRow").prop("checked",false);
    });

    $(document).on('click','.notification-mark-seen',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var href = $(this).attr('href');
        $.ajax({
            method: 'post',
            dataType: 'JSON',
            context : this,
            data: {id},
            url: global_url_notification_mark_seen,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function (response) {
                if(response.status=='0') alert(response.message);
                else {
                    $(this).hide();
                    if(href!='#' && href!='') setTimeout(function(){window.location.replace(href); }, 500);
                }
            }
        });
    });

    $(document).on('click','.delete-row',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).attr('data-id');
        var datatable_name = $(this).attr('data-table-name');
        var lang_confirm_title = $(this).attr('data-lang-confirm-title');
        var lang_confirm_message = $(this).attr('data-lang-confirm-message');
        var lang_confirm_yes = $(this).attr('data-lang-confirm-yes');
        var lang_confirm_cancel = $(this).attr('data-lang-confirm-no');
        var soft_delete = $(this).attr('data-soft-delete');

        if(typeof lang_confirm_title === 'undefined' || lang_confirm_title == '') lang_confirm_title = global_lang_confirm;
        if(typeof lang_confirm_message === 'undefined' || lang_confirm_message == '') lang_confirm_message = global_lang_delete_confirmation;
        if(typeof lang_confirm_yes === 'undefined' || lang_confirm_yes == '') lang_confirm_yes = global_lang_delete;
        if(typeof lang_confirm_cancel === 'undefined' || lang_confirm_cancel == '') lang_confirm_cancel = global_lang_cancel;
        if(typeof soft_delete === 'undefined' || soft_delete == '') soft_delete = '0';

        Swal.fire({
            title: lang_confirm_title,
            text: lang_confirm_message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '',
            confirmButtonText: lang_confirm_yes,
            cancelButtonText: lang_confirm_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id,soft_delete},
                    url: link,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        if (false === response.error) {
                            toastr.success(response.message, global_lang_deleted_successfully,{'positionClass':'toast-bottom-right'});
                            if(typeof datatable_name !== 'undefined' && datatable_name == 'table24') table24.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table23') table23.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table22') table22.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table21') table21.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table20') table20.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table19') table19.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table18') table18.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table17') table17.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table16') table16.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table15') table15.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table14') table14.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table13') table13.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table12') table12.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table11') table11.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table10') table10.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table9') table9.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table8') table8.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table7') table7.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table6') table6.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table5') table5.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table4') table4.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table3') table3.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table2') table2.draw('page');
                            else if(typeof datatable_name !== 'undefined' && datatable_name == 'table1') table1.draw('page');
                            else table.draw('page');

                        }
                        if (true === response.error) toastr.error(response.message, global_lang_something_wrong,{'positionClass':'toast-bottom-right'});
                        return false;
                    },
                    error: function (xhr, statusText) {
                        const msg = handleAjaxError(xhr, statusText);
                        Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                        return false;
                    },
                });
            }
        });

    });

    $(document).on('change','.update-status',function(e){
        e.preventDefault();
        var status = '0';
        if ($(this).is(':checked')) status = '1';
        var id = $(this).attr('data-id');
        var href = $(this).attr('data-url');
        update_status(status,id,href,false);
    });

    $(document).on('click','.update-status-click',function(e){
        e.preventDefault();
        var status = $(this).attr('data-status');
        var id = $(this).attr('data-id');
        var href = $(this).attr('data-url');
        update_status(status,id,href,true);
    });

    function update_status(status,id,href,refresh) {

        $.ajax({
            method: 'post',
            dataType: 'JSON',
            data: {status,id},
            url: href,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function (response) {
                if (false === response.error) {
                    toastr.success(response.message, global_lang_success,{'positionClass':'toast-bottom-right'});
                    if(refresh) {
                        setTimeout(function() {location.reload()}, 500);
                    }
                }
                if (true === response.error) Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                return false;
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            },
        });
    }

    $(document).on('draw.dt','#mytable1,#mytable2,#mytable2,#mytable3,#mytable4,#mytable5,#mytable6,#mytable7,#mytable8,#mytable9,#mytable10,#mytable11,#mytable12',function(){
        $('tbody td a:not([data-bs-toggle="popover"]),[data-bs-toggle="tooltip"],[data-toggle="tooltip"]').tooltip();
        $('[data-bs-toggle="popover"]').popover();
    });

    $(document).on('click','.nav_switch_bot',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).attr('data-id');

        $.ajax({
            context:this,
            method: 'post',
            data: {id},
            url: telegram_bot_manager_url_switch_bot,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function (response) {
                window.location.href = link;
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            },
        });

    });

});

// Start Whatsapp Followup Reminder
if(typeof(subscribers_previous_followup_list)==='undefined') subscribers_previous_followup_list = [];

/// Whatsapp Previous Followup Reminder Show
$(document).ready(function(){
    let previous_followup_count = subscribers_previous_followup_list.length;

    if(previous_followup_count)
    {
        $('#followupNotificationAudio')[0].play();

        for(let i=0; i<previous_followup_count; i++)
        {
            let previous_followup = subscribers_previous_followup_list[i];
            let whatsapp_bot_subscriber_subscriber_id = previous_followup['whatsapp_bot_subscriber_subscriber_id'];
            let subscriber_number = whatsapp_bot_subscriber_subscriber_id.split("-");
            subscriber_number = "+" + subscriber_number[0];
            let followupToastr = toastr.info(
                previous_followup['verified_name'] + "<br>" +
                previous_followup['subscriber_name'] +
                " (" + subscriber_number +")",
                "Whatsapp Followup Reminder:",
                {
                    "progressBar": false,
                    "timeOut": "0",
                    "extendedTimeOut": "0",
                    "onHidden": function() {
                        follow_up_reminder_delete(whatsapp_bot_subscriber_subscriber_id);
                    }
                }
            );

            $(followupToastr).on('click', function() {
                followupToastr.remove();
                follow_up_reminder_delete(whatsapp_bot_subscriber_subscriber_id);
                setTimeout(function(){
                    let url = whatsapp_livechat_load_url+"?subscriber_id="+whatsapp_bot_subscriber_subscriber_id;
                    window.location.href = url;
                }, 100);
            });
        }
    }
});

function follow_up_reminder_delete(whatsapp_bot_subscriber_subscriber_id=null)
{
    let subscriber_id = whatsapp_bot_subscriber_subscriber_id;
    $.ajax({
        type:'POST',
        url:whatsapp_subscriber_follow_up_reminder_delete,
        data:{
                "subscriber_id":subscriber_id
            },
        beforeSend:function(xhr)
        {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response)
        {
            if(response.status === true || response.status===-1)
                return;
            else
            {
                Swal.fire('Error', response.message, 'error');
                return;
            }
        }
    });
}
// End Whatsapp Followup Reminder
