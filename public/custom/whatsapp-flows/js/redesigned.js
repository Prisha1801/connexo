$('#optin-form-builder').hide('0')
$(document).ready(function() {
    setTimeout(loadDesign, 700);
});

function loadDesign(){
    /*header*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-header').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-element-9 fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-header
        $(this).removeClass('formbuilder-icon-header');
    });
    /*Text Field*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-text').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-text fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-text
        $(this).removeClass('formbuilder-icon-text');
    });
    /*Text Area*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-textarea').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-notepad fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
        newIcon.append('<span class="path3"></span>');
        newIcon.append('<span class="path4"></span>');
        newIcon.append('<span class="path5"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-textarea
        $(this).removeClass('formbuilder-icon-textarea');
    });
    /*Checkbox Group*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-checkbox-group').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-questionnaire-tablet fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-checkbox-group
        $(this).removeClass('formbuilder-icon-checkbox-group');
    });
    /*Radio Group*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-radio-group').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-text-circle fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
        newIcon.append('<span class="path3"></span>');
        newIcon.append('<span class="path4"></span>');
        newIcon.append('<span class="path5"></span>');
        newIcon.append('<span class="path6"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-radio-group
        $(this).removeClass('formbuilder-icon-radio-group');
    });
    /*Select*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-select').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-abstract-14 fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-select
        $(this).removeClass('formbuilder-icon-select');
    });
    /*Date Field*/
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-date').each(function() {
        // Obtener el texto existente y envolverlo en strong
        const existingText = $(this).text();
        // Crear el nuevo elemento i
        const newIcon = $('<i class="ki-duotone ki-calendar-2 fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
        newIcon.append('<span class="path1"></span>');
        newIcon.append('<span class="path2"></span>');
        newIcon.append('<span class="path3"></span>');
        newIcon.append('<span class="path4"></span>');
        newIcon.append('<span class="path5"></span>');
    
        const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
        const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);

        // Prender el nuevo icono y el texto envuelto al principio del elemento
        $(this).empty()
        $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
        // Remover la clase formbuilder-icon-date
        $(this).removeClass('formbuilder-icon-date');
    });
    /*Submit Button*/
    // $('#optin-form-builder').find('.frmb-control .formbuilder-icon-button').each(function() {
    //     // Obtener el texto existente y envolverlo en strong
    //     const existingText = $(this).text();
    //     // Crear el nuevo elemento i
    //     const newIcon = $('<i class="ki-duotone ki-send fs-3  ms-5 ms-sm-15 ms-md-0 me-3"></i>');
    //     newIcon.append('<span class="path1"></span>');
    //     newIcon.append('<span class="path2"></span>');
    
    //     const wrappedText = $('<strong class="d-none d-md-inline"></strong>').text(existingText);
    //     const wrappedText2 = $('<p class="d-block d-md-none text-center fs-9 fw-bold mb-0 text-wrap" style="line-height: 1;"></p>').text(existingText);
        
    //     // Prender el nuevo icono y el texto envuelto al principio del elemento
    //     $(this).empty()
    //     $(this).prepend(wrappedText).prepend(wrappedText2).prepend(newIcon);
    
    //     // Remover la clase formbuilder-icon-button
    //     $(this).removeClass('formbuilder-icon-button');
    // });
    $('#optin-form-builder').find('.frmb-control .formbuilder-icon-button').hide();





    
    $('#optin-form-builder').show('fast')
}