<x-mail::message>
@if($approved)
# Your Job Posting is Live!

Congratulations! Your job posting **"{{ $jobTitle }}"** has been approved and is now visible to all LCBA alumni.

<x-mail::button :url="$jobUrl">
View Your Job Posting
</x-mail::button>

Alumni can now discover and apply for this position through the LCBAConnect+ platform.

@else
# Job Posting Status Update

Your job posting **"{{ $jobTitle }}"** has been reviewed.

Unfortunately, it did not meet our posting guidelines at this time. Please review our job posting policy and feel free to resubmit with the necessary adjustments.

If you have questions, please contact our support team.
@endif

Best regards,<br>
The LCBAConnect+ Team
</x-mail::message>
