<x-mail::message>
# Welcome to LCBAConnect+!

Hello {{ $userName }},

Congratulations! Your account has been **approved** by our administrators.

You can now access the full features of **LCBAConnect+**, including:
- Connect with fellow LCBA alumni worldwide
- Discover personalized job opportunities
- Attend exclusive alumni events
- Share your success stories and insights

<x-mail::button :url="$loginUrl">
Login to Your Account
</x-mail::button>

We're excited to have you as part of the LCBA alumni community!

Best regards,<br>
The LCBAConnect+ Team
</x-mail::message>
