<x-mail::message>
# New User Registration - Verification Required

Hello Admin,

A new user has registered on **LCBAConnect+** and is pending verification.

**User Details:**
- **Name:** {{ $userName }}
- **Email:** {{ $userEmail }}
- **Program:** {{ $userProgram }}
- **Batch:** {{ $userBatch }}

Please review and verify this registration in the Alumni Management dashboard.

<x-mail::button :url="env('FRONTEND_URL', 'http://localhost:5173') . '/alumni-management?tab=verification'">
Review Verification Request
</x-mail::button>

Thank you for maintaining the integrity of the LCBA alumni network.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
