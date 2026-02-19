"use strict";
var table16;
var table_name ='';
var tableWebhook='';
var tableWebhookReport='';
var tableWebhookreport2='';
var perscrollwebhook;
var perscrollWebhookReport;
var perscrollWebhookReport2;


$(document).ready(function() {

    $(document).on('click','#sync_all_flow',function(e){
        e.preventDefault();
        let sync_btn = $("#sync_all_flow").html();
        $("#sync_all_flow").addClass("disabled");
        $("#sync_all_flow").html("<i class='fas fa-sync fa-spin'></i>");
        $("#sync_all_flow i").css({"color":"#20C997","font-size":"13px"});
        var whatsapp_bot_id = $("#flow_whatsapp_bot_id").val();
        $.ajax({
            url: sync_whatsapp_flow,
            method: "POST",
            data: {whatsapp_bot_id:whatsapp_bot_id},
            dataType: 'JSON',

            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {   
                if(response.error===true){
                    $("#sync_all_flow").html(sync_btn);
                    $("#sync_all_flow").removeClass("disabled");
                    $('#sync_all_flow i').css({"color":"#000000","font-size":""});
                    Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                } 
                else {
                    toastr.success('',  response.message,{'positionClass':'toast-bottom-right'});
                    $("#sync_all_flow").html(sync_btn);
                    $('#sync_all_flow i').css({"color":"#000000","font-size":""});
                    $("#sync_all_flow").removeClass("disabled");
                    $("#sync_all_flow").attr("disabled", false);
                    table16.draw();
                }
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });
    });

    $(document).on('click','.delete_whatsapp_flow',function(e){
        e.preventDefault();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: global_lang_confirm,
            text: global_lang_delete_confirmation,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '',
            confirmButtonText: global_lang_delete,
            cancelButtonText: global_lang_cancel
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Checking...',
                    text: 'Please wait',
                    imageUrl: progress_gif,
                    showConfirmButton: false,
                    allowOutsideClick: false
                  });

                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id},
                    url: whatsapp_flow_delete,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        Swal.fire({
                            title: '',
                            showConfirmButton: false,
                            timer: 10
                        });
                        if (false === response.error) {
                            toastr.success(response.message, global_lang_deleted_successfully,{'positionClass':'toast-bottom-right'});
                            table16.draw();

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

    $(document).on('click','.publish_flow',function(e){
        e.preventDefault();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: global_lang_confirm,
            text: global_lang_whatsapp_flow_publish_confirmation_msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '',
            confirmButtonText: global_lang_publish_whatsapp_flow,
            cancelButtonText: global_lang_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id},
                    url: whatsapp_flow_publish,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        if (false === response.error) {
                            toastr.success(response.message, global_lang_saved_successfully,{'positionClass':'toast-bottom-right'});
                            table16.draw();

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

    $(document).on('click','.depricate_whatsapp_flow',function(e){
        e.preventDefault();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: global_lang_confirm,
            text: global_lang_whatsapp_flow_depricate_confirmation_msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '',
            confirmButtonText: global_lang_depricate_whatsapp_flow,
            cancelButtonText: global_lang_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id},
                    url: whatsapp_flow_depricate,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        if (false === response.error) {
                            toastr.success(response.message, global_lang_saved_successfully,{'positionClass':'toast-bottom-right'});
                            table16.draw();

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

    $(document).on('click','#refresh_flow_builder_reply_message',function(e){
        $.ajax({
            url: whatsapp_flows_get_reply_message,
            method: "POST",
            data: {},

            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {   
                
                $('#post_back_dropdown').html(response);
                $('#whatsapp_bot_post_back').select2();
                    
                
           },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });

    });

    // webhook function list(Ronok)

    $(document).on('click', '.add_whatsapp_flow_webhook', function(e) {
        e.preventDefault();
        var flow_id = $(this).attr('data-id');
        var unique_flow_id = $(this).attr('data-flow_unique_id');
        $('#flow_id').val(flow_id);
        $('#unique_flow_id').val(unique_flow_id);
        $("#whatsapp-flows-webhook-modal").modal('show');
        $("#createWebhookField").hide();
        if ($.fn.DataTable.isDataTable("#tableWebhook")) {
            tableWebhook.destroy();
        }
        tableWebhook = $("#tableWebhook").DataTable({
            fixedHeader: false,
            colReorder: true,
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 2, "desc" ]],
            pageLength: 10,
            lengthMenu: [
                [5,10, 25, 50, 100],
                [5,10, 25, 50, 100],
            ],
            ajax:
                {
                    "url": whatsapp_flows_webhook_data,
                    "type": 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    data: function ( d )
                    {
                        d.search_value = $('#search_value_webhook').val();
                        d.flow_id = flow_id; 
                        d.unique_flow_id = unique_flow_id; 
                    }
                },
            language:
                {
                    url: global_url_datatable_language
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [],
                    className: 'text-center'
                },
                {
                    targets: [3],
                    className: 'text-end'
                },
                {
                    targets: [1],
                    visible: false
                },
                {
                    targets: [0],
                    sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin         
                if(areWeUsingScroll)
                {
                    if (perscrollwebhook) perscrollwebhook.destroy();
                    perscrollwebhook = new PerfectScrollbar('#tableWebhook_wrapper .dataTables_scrollBody');
                }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll)
                {
                    if (perscrollwebhook) perscrollwebhook.destroy();
                    perscrollwebhook = new PerfectScrollbar('#tableWebhook_wrapper .dataTables_scrollBody');
                }
            }
    });    

        
    });
    $(document).on('keyup', '#search_value_webhook', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) tableWebhook.draw(false);
    });
    $(document).on('click', '#create_new_webhook', function(e){
        e.preventDefault();
        $("#createWebhookField").show();
    });
    $(document).on('click', '#saveButtonwebhook', function(e){
            var flow_id = $("#flow_id").val();
            var webhook_url = $("#whatsapp_webhook_url").val();
            var unique_flow_id = $("#unique_flow_id").val();
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: { flow_id,webhook_url,unique_flow_id },
                url: whatsapp_flows_save_webhook,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                },
                success: function(res) {
        
                    $("#webhook_url").val('');
                    // Shows error if something goes wrong
                    if (res.error) {
                    Swal.fire({
                        icon: 'error',
                        text: res.error,
                        title: global_lang_error,
                    });
                    return;
                    }
                    // If everything goes well, requests for downloading the file
                    if (res.error === false) {
                    Swal.fire({
                            icon: 'success',
                            text: res.message,
                            title: global_lang_success,
                        }).then((value) => {
                            $("#webhook_url").val('');
                            tableWebhook.draw();
                            $("#createWebhookField").hide();
                        });
                    } else Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                },
                error: function(xhr, status, error) {
                    // Shows error if something goes wrong
                    Swal.fire({
                    icon: 'error',
                    text: xhr.responseText,
                    title: global_lang_error,
                    });            
                }
                });

    });

    $(document).on('click','.delete-webhook',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).attr('data-id');
        Swal.fire({
            title: global_lang_remove,
            text: global_lang_remove_confirmation,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '',
            confirmButtonText: global_lang_delete,
            cancelButtonText: global_lang_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id},
                    url: link,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        if (false === response.error) {
                            tableWebhook.draw();

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


    $(document).on('click', '.whatsapp-flows-view-webhook', function(e) {
        e.preventDefault();
        var webhook_id = $(this).attr('data-id');
        $("#view_webhook_id").val(webhook_id);
        $("#view_whatsapp_flows_webhook_modal").modal('show');
        if(tableWebhookReport==''){

            tableWebhookReport = $("#tableWebhookReport").DataTable({
                fixedHeader: false,
                colReorder: true,
                serverSide: true,
                processing:true,
                bFilter: true,
                order: [[ 5, "desc" ]],
                pageLength: 10,
                ajax:
                    {
                        "url": whatsapp_flows_webhook_activity_data,
                        "type": 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                        },
                        data: function ( d )
                        {
                            d.webhook_id = $("#view_webhook_id").val();
                        }
                    },
                language:
                    {
                        url: global_url_datatable_language
                    },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                columnDefs: [
                    {
                        targets: '',
                        className: 'text-center'
                    },
                    {
                        targets: [0,1,4],
                        sortable: false
                    },
                    {
                        targets: [1],
                        visible: false
                    }

                ],
                fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                    if(areWeUsingScroll)
                    {
                        if (perscrollWebhookReport) perscrollWebhookReport.destroy();
                        perscrollWebhookReport = new PerfectScrollbar('#tableWebhookReport_wrapper .dataTables_scrollBody');
                    }
                    var $searchInput = $('#tableWebhookReport_filter input');
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
                        if(this.value.length > 2 || this.value.length==0) {
                            tableWebhookReport.search( this.value ).draw();
                        }
                    });
                },
                scrollX: 'auto',
                fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                    if(areWeUsingScroll)
                    {
                        if (perscrollWebhookReport) perscrollWebhookReport.destroy();
                        perscrollWebhookReport = new PerfectScrollbar('#tableWebhookReport_wrapper .dataTables_scrollBody');
                    }
                }
            });
        }
        else tableWebhookReport.draw();
    });

    $(document).on('click', '.whatsapp_flows_view_post_data', function(event) {
        event.preventDefault();
        $("#view_whatsapp_flows_webhook_post_data_modal").modal('show');

        var webhook_activities_id = $(this).attr('data-id');
        $.ajax({
            url: whatsapp_flows_get_webhook_activity_report,
            method: "POST",
            data: {webhook_activities_id:webhook_activities_id},
            dataType: 'JSON',

            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {   
                if(response.error=='1'){
                    Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                    
                } 
                else {
                    var postVal = response.post_data; 
                    
                    if(postVal != "" || postVal != null || typeof(postVal) != 'undefined')
                    {
                        var i = 1;
                        var appendTable = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>'+ whatsapp_bot_manager_lang_webhook_data_view_field_name +'</th><th>'+ whatsapp_bot_manager_lang_webhook_data_view_value +'</th></tr></thead><tbody>';
                        jQuery.each(postVal, function(index, item) {
                            var fieldName = item[0];
                            var value = item[1];
                            var answerHtml = '';
                        
                            if (Array.isArray(value)) {
                                answerHtml = '<td scope="col"><ul>';
                                value.forEach(function(subAnswer) {
                                    answerHtml += '<li>' + subAnswer + '</li>';
                                });
                                answerHtml += '</ul></td>';
                            } else {
                                answerHtml = '<td scope="col">' + value + '</td>';
                            }
                        
                            appendTable += '<tr><th scope="row">' + i + '</th>';
                            appendTable += '<td scope="col">' + fieldName + '</td>';
                            appendTable += answerHtml + '</tr>';
                        
                            i++;
                        });
                        
                        appendTable += '</tbody></table></div>';
                        $("#whatsapp_flows_json_formate_data").html(postVal);
                        $(".infos").html(appendTable); 

                        
            
                    } else {
                        $("#whatsapp_flows_json_formate_data").html("<p>"+whatsapp_bot_manager_lang_webhook_data_view_no_data+"</p>");
                        $(".infos").html(""); 
                    } 
            
                    
                }
           },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });


    });

    $(document).on('click', '.whatsapp_flows_webhook_report', function(e) {
        e.preventDefault();
        var unique_flow_id = $(this).attr('data-flow_unique_id');
        $('#unique_flow_id').val(unique_flow_id);
        $("#whatsapp-flows-webhook-report-modal").modal('show');
        if ($.fn.DataTable.isDataTable("#tableWebhookreport2")) {
            tableWebhookreport2.destroy();
        }
        tableWebhookreport2 = $("#tableWebhookreport2").DataTable({
            fixedHeader: false,
            colReorder: true,
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            lengthMenu: [
                [5,10, 25, 50, 100],
                [5,10, 25, 50, 100],
            ],
            ajax:
                {
                    "url": whatsapp_flows_webhook_report,
                    "type": 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    data: function ( d )
                    {
                        d.search_value = $('#search_value_webhook_report').val();
                        d.unique_flow_id = unique_flow_id; 
                    }
                },
            language:
                {
                    url: global_url_datatable_language
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [4],
                    className: 'text-center'
                },
                {
                    targets: [],
                    className: 'text-end'
                },
                {
                    targets: [],
                    visible: false
                },
                {
                    targets: [0],
                    sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin         
                if(areWeUsingScroll)
                {
                    if (perscrollWebhookReport2) perscrollWebhookReport2.destroy();
                    perscrollWebhookReport2 = new PerfectScrollbar('#tableWebhookreport2_wrapper .dataTables_scrollBody');
                }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll)
                {
                    if (perscrollWebhookReport2) perscrollWebhookReport2.destroy();
                    perscrollWebhookReport2 = new PerfectScrollbar('#tableWebhookreport2_wrapper .dataTables_scrollBody');
                }
            }
    });    

        
    });

    $(document).on('keyup', '#search_value_webhook_report', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) tableWebhookreport2.draw(false);
    });

    $(document).on('click', '.whatsapp_flows_webhook_details_data', function(event) {
        event.preventDefault();

        $("#whatsapp-flows-webhook_report_post_data_modal").modal('show');

        var unique_flow_id = $(this).attr('data-unique_flow_id');
        var submitted_data_id = $(this).attr('data-id');
        $.ajax({
            url: whatsapp_flows_get_webhook_report_data,
            method: "POST",
            data: {unique_flow_id:unique_flow_id,submitted_data_id:submitted_data_id},
            dataType: 'JSON',

            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {   
                if(response.error=='1'){
                    Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                    
                } 
                else {
                    var postVal = response.post_data;
                    if(postVal != "" || postVal != null || typeof(postVal) != 'undefined')
                    {
                        var i = 1;
                        var appendTable = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>'+ whatsapp_bot_manager_lang_webhook_data_view_field_name +'</th><th>'+ whatsapp_bot_manager_lang_webhook_data_view_question +'</th><th>'+ whatsapp_bot_manager_lang_webhook_data_view_answer +'</th></tr></thead><tbody>';
                        jQuery.each(postVal, function(index, item) {
                            var fieldName = item[0];
                            var question = item[1];
                            var answers = item[2];
                            var answerHtml = '';
                        
                            if (Array.isArray(answers)) {
                                answerHtml = '<td scope="col"><ul>';
                                answers.forEach(function(subAnswer) {
                                    answerHtml += '<li>' + subAnswer + '</li>';
                                });
                                answerHtml += '</ul></td>';
                            } else {
                                answerHtml = '<td scope="col">' + answers + '</td>';
                            }
                        
                            appendTable += '<tr><th scope="row">' + i + '</th>';
                            appendTable += '<td scope="col">' + fieldName + '</td>';
                            appendTable += '<td scope="col">' + question + '</td>';
                            appendTable += answerHtml + '</tr>';
                        
                            i++;
                        });
                        
                        appendTable += '</tbody></table></div>';
                        $(".infos").html(appendTable); 
            
                    } else {
                        $(".infos").html(""); 
                        $(".user_input_flows").html('');
                        $(".user_input_flows_card").css('display','none');
                    } 
            
                }
           },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });

    });


    // End webhook function list(Ronok)

});
