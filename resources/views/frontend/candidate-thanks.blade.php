@extends('frontend.layout.master')

@section('content')
<main class="main" style="text-align:center; padding:50px;">
    <h2>Thank you for applying!</h2>
    <p>Click the button below to share your application on WhatsApp:</p>

    <a id="waShareBtn" href="{{ $waUrl }}" target="_blank" 
       style="display:inline-block; margin-top:20px; padding:14px 25px; background:#024430; color:#ffe7bb; border-radius:6px; font-weight:600; text-decoration:none; font-size:16px;">
       Share on WhatsApp
    </a>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const waBtn = document.getElementById("waShareBtn");

    // Optional: auto-open WhatsApp after 2 seconds
    setTimeout(() => {
        waBtn.click();
    }, 2000);
});
</script>
@endsection


