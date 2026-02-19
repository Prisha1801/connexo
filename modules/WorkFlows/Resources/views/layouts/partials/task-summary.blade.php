@if ($taskType === 'create_contact')
    <p><strong>Create Contact Configuration:</strong></p>
    @if (isset($config['phone_variable']) && $config['phone_variable'])
        <p>Phone: Variable: {{ $config['phone_variable'] }}</p>
    @elseif (isset($config['phone_static']) && $config['phone_static'])
        <p>Phone: Static: {{ $config['phone_static'] }}</p>
    @endif
    
    @if (isset($config['name_variable']) && $config['name_variable'])
        <p>Name: Variable: {{ $config['name_variable'] }}</p>
    @elseif (isset($config['name_static']) && $config['name_static'])
        <p>Name: Static: {{ $config['name_static'] }}</p>
    @endif
    
    @if (isset($config['add_groups']) && count($config['add_groups']) > 0)
        <p>Add to Groups: {{ count($config['add_groups']) }} selected</p>
    @endif
    
    @if (isset($config['remove_groups']) && count($config['remove_groups']) > 0)
        <p>Remove from Groups: {{ count($config['remove_groups']) }} selected</p>
    @endif
    
    @if (isset($config['tags']) && $config['tags'])
        <p>Tags: {{ $config['tags'] }}</p>
    @endif
    
    @if (isset($config['add_custom_fields']) && $config['add_custom_fields'])
        <p>Custom Fields: {{ isset($config['custom_fields']) ? count($config['custom_fields']) : 0 }} added</p>
    @endif
@elseif ($taskType === 'call_api')
    <!-- API call summary -->
@elseif ($taskType === 'send_whatsapp')
    <!-- WhatsApp summary -->
@endif