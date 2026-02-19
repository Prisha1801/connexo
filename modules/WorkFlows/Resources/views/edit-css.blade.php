<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .floating-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007bff;
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .floating-button:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .variable-selector {
        margin-bottom: 10px;
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #eedbdb;
        border-radius: 10px;
        font-size: small;
        font-weight: 500;
        height: 45px;
        /* Increased height */
        padding: 6px 12px;
        /* Added some padding */
        line-height: 26px;
        /* Aligns text vertically */
    }

    .select2-selection select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #eedbdb;
        border-radius: 10px;
        font-size: small;
        font-weight: 500;
        height: 45px;
        /* Increased height */
        padding: 6px 12px;
        /* Added some padding */
        line-height: 26px;
        /* Aligns text vertically */
    }

    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f1fbf3;
        cursor: default;
    }

    /* Optional: Ensure the arrow aligns properly */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }
</style>
<style>
    .phone-input-container {
        position: relative;
    }

    .variable-tag {
        background-color: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 2px 6px;
        margin: 2px;
        display: inline-block;
        color: #1565c0;
        font-weight: 500;
    }

    .phone-preview {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 8px 12px;
        margin-top: 8px;
        border: 1px dashed #dee2e6;
        min-height: 38px;
    }

    .insert-btn {
        background-color: #4a6cf7;
        color: white;
        border: none;
        transition: all 0.3s;
    }

    .insert-btn:hover {
        background-color: #3b5be3;
        transform: translateY(-2px);
    }

    .url-input-container {
        position: relative;
    }

    .url-preview {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 8px 12px;
        margin-top: 8px;
        border: 1px dashed #dee2e6;
        min-height: 38px;
        font-family: monospace;
    }

    .url-preview .variable-tag {
        background-color: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 2px 6px;
        margin: 2px;
        display: inline-block;
        color: #1565c0;
        font-weight: 500;
        font-family: inherit;
    }
</style>
