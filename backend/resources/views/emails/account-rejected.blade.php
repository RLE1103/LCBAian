<x-mail::message>
# Registration Status Update

Hello {{ $userName }},

Thank you for your interest in joining **LCBAConnect+**.

Unfortunately, we were unable to verify your registration at this time.

**Reason:**  
{{ $reason }}

If you believe this is an error or would like to provide additional information, please contact our support team.

<x-mail::button :url="'mailto:' . $contactEmail">
Contact Support
</x-mail::button>

Best regards,<br>
The LCBAConnect+ Team
</x-mail::message>
