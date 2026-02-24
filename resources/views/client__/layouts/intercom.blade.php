

<script>
    window.intercomSettings = {
        api_base: "https://api-iam.intercom.io",
        app_id: '{{ env('INTER_APP_ID') }}',
        user_id: <?php echo json_encode(auth()->user()->id); ?>, // IMPORTANT: Replace "user.id" with the variable you use to capture the user's ID
        name: <?php echo json_encode(auth()->user()->name); ?>, // IMPORTANT: Replace "user.name" with the variable you use to capture the user's name
        email: <?php echo json_encode(auth()->user()->email); ?>, // IMPORTANT: Replace "user.email" with the variable you use to capture the user's email address
        // avatar: {
        //     "type": "avatar",
        //     "image_url": '{{ 'https://www.gravatar.com/avatar/' . md5(auth()->user()->email) }}'
        // },
        // phone: '{{ auth()->user()->phone }}',
        // company: {
        //     company_id: {{ auth()->user()->company_id }},
        //     name: '{{ auth()->user()->name_company }}',
        // },
        created_at: "<?php echo strtotime(auth()->user()->created_at); ?>", // IMPORTANT: Replace "user.createdAt" with the variable you use to capture the user's sign-up date
    };
</script>

<script>
    (function() {
        var w = window;
        var ic = w.Intercom;
        if (typeof ic === "function") {
            ic('reattach_activator');
            ic('update', w.intercomSettings);
        } else {
            var d = document;
            var i = function() {
                i.c(arguments);
            };
            i.q = [];
            i.c = function(args) {
                i.q.push(args);
            };
            w.Intercom = i;
            var l = function() {
                var s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'https://widget.intercom.io/widget/t28h1ev5';
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            };
            if (document.readyState === 'complete') {
                l();
            } else if (w.attachEvent) {
                w.attachEvent('onload', l);
            } else {
                w.addEventListener('load', l, false);
            }
        }
    })();
</script>

<script>
    window.intercomSettings = {
        api_base: "https://api-iam.intercom.io",
        app_id: '{{ env('INTER_APP_ID') }}',
        email: '{{ auth()->user()->email }}',
        user_id: {{ auth()->user()->id }},
        name: '{{ auth()->user()->name }}',

        avatar: {
            "type": "avatar",
            "image_url": '{{ 'https://www.gravatar.com/avatar/' . md5(auth()->user()->email) }}'
        },
        // phone: '{{ auth()->user()->phone }}',
        // company: {
        //     company_id: {{ auth()->user()->company_id }},
        //     name: "{{ auth()->user()->name_company }}",
        //     created_at: 1394531169,
        //     monthly_spend: 49,
        //     plan: "Pro",
        //     size: 85,
        //     website: "http://example.com",
        //     industry: "Manufacturing"
        // },
        created_at: "<?php echo strtotime(auth()->user()->created_at); ?>",
        user_hash: '{{ hash_hmac('sha256', auth()->user()->id, env('INTER_ID')) }}',
        language_override: 'es',
        hide_default_launcher: true,
    };
</script>
<script>
    (function() {
        var w = window;
        var ic = w.Intercom;
        if (typeof ic === "function") {
            ic('reattach_activator');
            ic('update', w.intercomSettings);
        } else {
            var d = document;
            var i = function() {
                i.c(arguments);
            };
            i.q = [];
            i.c = function(args) {
                i.q.push(args);
            };
            w.Intercom = i;
            var l = function() {
                var s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'https://widget.intercom.io/widget/t28h1ev5';
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            };
            if (document.readyState === 'complete') {
                l();
            } else if (w.attachEvent) {
                w.attachEvent('onload', l);
            } else {
                w.addEventListener('load', l, false);
            }
        }
    })();
</script>
