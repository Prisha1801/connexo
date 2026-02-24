 <style>
     /* Disabled state styling */
     [disabled] {
         opacity: 0.6;
         cursor: not-allowed;
         pointer-events: none;
     }

     .btn[disabled] {
         background-color: #f5f8fa;
         color: #b5b5c3;
         border-color: #f5f8fa;
     }

     /* Cancellation bar styling */
     .cancellation-bar {
         position: sticky;
         top: 0;
         z-index: 1000;
         padding: 15px;
         text-align: center;
         font-weight: bold;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
     }

     /* Add smooth transitions for address fields */
     #address-fields {
         transition: all 0.3s ease;
     }

     .order-instructions-card {
         border-left: 4px solid #3699FF;
     }

     .ki-note:before {
         content: "\e9f3";
     }

     /* Custom styling */
     .card {
         box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
         border: none;
     }

     .card-header {
         border-bottom: 1px solid #eff2f5;
     }

     .symbol img {
         width: 50px;
         height: 50px;
         object-fit: cover;
         border-radius: 0.475rem;
     }

     .badge-light-info {
         background-color: #f1faff;
         color: #009ef7;
     }

     .badge-light-success {
         background-color: #e8fff3;
         color: #50cd89;
     }

     .badge-light-danger {
         background-color: #fff5f8;
         color: #f1416c;
     }

     .modal-content {
         border-radius: 0.475rem;
     }

     #shipping-form .form-control-solid,
     #payment-form .form-control-solid {
         background-color: #f5f8fa;
     }

     .bg-light-primary {
         background-color: #f1faff;
     }

     #order-status-select {
         max-width: 250px;
     }

     #status-message .alert {
         padding: 0.75rem 1.25rem;
         margin-bottom: 0;
     }

     /* Lock icon styling */
     .badge .fas {
         font-size: 0.8em;
         vertical-align: middle;
     }

     .reason-btn {
         flex: 1 0 calc(33.333% - 0.5rem);
         min-width: 120px;
         text-align: center;
         padding: 0.5rem;
         white-space: normal;
         height: auto;
         transition: all 0.3s ease;
     }

     .reason-btn:hover,
     .reason-btn.active {
         background-color: #3699ff;
         color: white !important;
         border-color: #3699ff;
     }

     .print-option.active {
         border-color: #009ef7;
         background-color: #f1faff;
     }

     .modal-content {
         border-radius: var(--kt-border-radius);
         border: none;
         box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
     }

     .modal-header {
         padding: 1.25rem 1.5rem;
         border-bottom: 1px solid var(--kt-gray-200);
     }

     .modal-body {
         background-color: var(--kt-gray-100);
         max-height: 60vh;
         overflow-y: auto;
     }

     .modal-footer {
         border-top: 1px solid var(--kt-gray-200);
         padding: 1.25rem 1.5rem;
     }

     .product-card {
         transition: all 0.3s ease;
         border-radius: var(--kt-border-radius);
         background-color: white;
     }

     .product-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
     }

     .product-thumb-container {
         padding-top: 100%;
         /* 1:1 aspect ratio */
         position: relative;
     }

     .product-thumb {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         object-fit: cover;
         border-top-left-radius: var(--kt-border-radius);
         border-top-right-radius: var(--kt-border-radius);
     }

     .add-btn {
         width: 28px;
         height: 28px;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.2s ease;
         z-index: 2;
     }

     .add-btn:hover {
         transform: scale(1.1);
         box-shadow: 0 4px 8px rgba(54, 153, 255, 0.3);
     }

     .form-control-solid {
         background-color: var(--kt-gray-100);
         border-color: var(--kt-gray-200);
         transition: all 0.2s ease;
     }

     .form-control-solid:focus {
         background-color: white;
         border-color: var(--kt-primary);
         box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.2);
     }

     @media (max-width: 576px) {
         .reason-btn {
             flex: 1 0 calc(50% - 0.5rem);
         }
     }
 </style>
