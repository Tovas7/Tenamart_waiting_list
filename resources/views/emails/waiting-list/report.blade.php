
# Weekly TenaMart Waiting List Report

Hello Admin,

Here is your weekly summary of the TenaMart Waiting List:

## Overall Statistics

* **Total Signups:** {{ $stats['total_signups'] }}

## Signups by Source

@if(!empty($stats['signups_by_source']))
<x-mail::table>
| Source          | Count |
| :-------------- | :---- |
@foreach($stats['signups_by_source'] as $source)
| {{ $source['signup_source'] ?? 'N/A' }} | {{ $source['count'] }} |
@endforeach
</x-mail::table>
@else
No signups by source data available.
@endif

## Daily Signup Trends (Last 30 Days)

@if(!empty($stats['daily_signup_trends_last_30_days']))
<x-mail::table>
| Date       | Signups |
| :--------- | :------ |
@foreach($stats['daily_signup_trends_last_30_days'] as $date => $count)
| {{ $date }} | {{ $count }} |
@endforeach
</x-mail::table>
@else
No daily signup trend data available.
@endif

## Peak Signup Days

@if(!empty($stats['peak_signup_days']))
<x-mail::table>
| Date       | Signups |
| :--------- | :------ |
@foreach($stats['peak_signup_days'] as $date => $count)
| {{ $date }} | {{ $count }} |
@endforeach
</x-mail::table>
@else
No peak signup days data available.
@endif

Thanks,
{{ config('app.name') }}
</x-mail::message>
