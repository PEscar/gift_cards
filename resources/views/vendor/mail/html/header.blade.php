<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<div>
  <img style="vertical-align:middle" src="{{ asset('favicon.ico') }}" height="100px" >
  <span style="">{{ $slot }}</span>
</div>
@endif
</a>
</td>
</tr>
