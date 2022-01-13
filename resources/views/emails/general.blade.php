<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="x-apple-disable-message-reformatting">

<title>{{ $appName }}</title>

<style>
table, td, div, h1, p {font-family: Arial, sans-serif;}
</style>
</head>

<body style="margin:0;padding:0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tbody>
<tr>
<td align="center">
<table cellspacing="0" cellpadding="0" border="0" width="600" align="center" style="max-width:600px">
<tbody>
<tr>
<td>
<table style="background-color:transparent;border:0px;font-size:16px;font-family:Arial,helvetica,sans-serif;line-height:150%;color:#4a6375" cellspacing="0" cellpadding="0" bgcolor="transparent" width="100%">
<tbody>
<tr>
<td align="center" valign="top">
<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
<td style="padding:0px">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr> 
<td align="center" valign="top">
<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
<td align="left" valign="top">
<table cellspacing="0" cellpadding="0" style="width:100%">
<tbody>
<tr>
<td valign="top" style="width:100%">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%"><tbody>
<tr>
<td>
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="text-align:center;background-color:#eff3f6;border:1px solid #eff3f6;min-width:100%">
<tbody>
<tr>
<td style="padding:0px" align="center">
<table width="100%" cellspacing="0" cellpadding="0" role="presentation">
<tbody>
<tr>
<td align="center">
<a href="{{ config('app.url') }}" title="{{ $appName }}" target="_blank">
<img src="{{ asset('uploads/application/application_logo.png') }}" alt="{{ $appName }}" height="40" width="150" style="display:block;height:40px;width:150px;text-align:center;padding:30px 20px" class="CToWUd">
</a>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%">
<tbody>
<tr>
<td style="padding:0px 0px 20px">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:#121212;min-width:100%">
<tbody>
<tr>
<td style="padding:0px">
<table cellspacing="0" cellpadding="0" role="presentation" style="width:100%">
<tbody>
<tr>
<td>

</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%">
<tbody>
<tr>
<td style="padding:48px 0px 0px">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:transparent;min-width:100%">
<tbody>
<tr>
<td style="padding:0px">
<table cellspacing="0" cellpadding="0" role="presentation" style="width:100%">
<tbody>
<tr>
<td>
<table cellspacing="0" cellpadding="0" role="presentation" style="width:100%">
<tbody>
<tr>
<td valign="top" style="width:100%;padding-bottom:0px">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:transparent;border:0px solid transparent;min-width:100%">
<tbody>
<tr>
<td style="padding:0px">
<p style="text-align:center;font-size:14px;line-height:16px;color:#5f7d95;margin:0;padding:0">
<span style="font-family:Arial,Helvetica,sans-serif">Sent with </span>
<img alt="Sent with love by Freepik" height="14" src="https://ci6.googleusercontent.com/proxy/3RsTDe_REyoJReeTQHZk0LceFdxVOeynng-g6UXqEnV40CT39ur8KD2TnWSozamlFpN6Ld7ot5I1KxZ2dH0-E9dTpQD6XebkAxkV9G6I5qqq20beS6YHSBw881A1F7lnnEECyo8zDVZD6PruNU8DdYq1_JZfg-os5Kg=s0-d-e1-ft#https://image.email-freepik.com/lib/fe3111717564047f7c1d74/m/1/585cd126-f1bc-4504-88f1-ec80e64f7622.png" style="display:inline-block;padding:0px 2px;text-align:center;height:14px;width:16px;border:0px" width="16" class="CToWUd"><span style="font-family:Arial,Helvetica,sans-serif"> by {{ $appName }}</span>
</p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
<table cellspacing="0" cellpadding="0" role="presentation" style="width:100%">
<tbody>
<tr>
<td valign="top" style="width:100%;padding-top:0px">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:transparent;border:0px;min-width:100%"><tbody><tr><td style="padding:24px 0px">
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tbody>
<tr>

@foreach ($officialMediaItems as $officialMediaItem)

<td>

<a href="{{ $officialMediaItem->link }}" alt="Follow {{ $appName }} on {{ $officialMediaItem->name }}" style="margin-top:0;margin-bottom:0;margin-left:8px;margin-right:8px;padding:0;display:block" target="_blank">
<img src="{{ $officialMediaItem->logo }}" alt="Follow {{ $appName }} on {{ $officialMediaItem->name }}" width="34" style="margin:0;padding:0;display:block;width:34px;height:34px" class="CToWUd">
</a>

</td>
 
@endforeach

{{-- 
<td>
<a href="https://click.email-freepik.com/?qs=88960593bdc79b37e99344d3f06264527fce59191377a1ba3e1b74dbfe37e1277abd18e10b0bf4c0f10835a34b81be1774c76ca2f6b75c49f378d4ba37993c98" alt="Follow Freepik on Twitter" style="margin-top:0;margin-bottom:0;margin-left:8px;margin-right:8px;padding:0;display:block" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/?qs%3D88960593bdc79b37e99344d3f06264527fce59191377a1ba3e1b74dbfe37e1277abd18e10b0bf4c0f10835a34b81be1774c76ca2f6b75c49f378d4ba37993c98&amp;source=gmail&amp;ust=1642073540244000&amp;usg=AOvVaw0xcq2zMQs8P6tm520lmGTl">
<img src="https://ci4.googleusercontent.com/proxy/OlIdcFmIYrYloj0423U_hiSn4qnpCz7iQfuUwAyLnknWmQH5hkAtJmm9kwq_loXsHpxWaciZU-pmMp_ky8grI5PgjKlNJG3mo-RDByCyCrY3kJMtRcUUmbXB7XkXI5IMJkwfj6AMeIknN5uJxnb1a65DF-nOBrl13kQ=s0-d-e1-ft#https://image.email-freepik.com/lib/fe3111717564047f7c1d74/m/1/e1d18e9a-175d-4389-bf25-98465613c721.png" alt="Follow Freepik on Twitter" width="34" style="margin:0;padding:0;display:block;width:34px;height:34px" class="CToWUd">
</a>
</td> 
--}}
{{-- 
<td>
<a href="https://click.email-freepik.com/?qs=88960593bdc79b3768605942d72c56926502b0f11cf5b0ad4f4ce6ddd02ada8963bfb4dcc20e6fec50ebfbbf3e3a86c90d1dd51ef1a08677fb10d16deeea9fc8" alt="Follow Freepik on Pinterest" style="margin-top:0;margin-bottom:0;margin-left:8px;margin-right:8px;padding:0;display:block" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/?qs%3D88960593bdc79b3768605942d72c56926502b0f11cf5b0ad4f4ce6ddd02ada8963bfb4dcc20e6fec50ebfbbf3e3a86c90d1dd51ef1a08677fb10d16deeea9fc8&amp;source=gmail&amp;ust=1642073540244000&amp;usg=AOvVaw30ak2cEotu5RQcO68UIrG-">
<img src="https://ci6.googleusercontent.com/proxy/b92Ot7Hv-fV11D__7NznT7dzK6oFvERbKb2_-CWc2LZYtvlUD1S042NV8r82ov4eQw1Busrq1Jx4SZq_xx1aRY0H5eG5pcXusB1D-cUhDWGLWt_QldYJx0mBegVCrzkfYCNzDvuVe7HHIEX2Mki2EUlJovoFkyU-2To=s0-d-e1-ft#https://image.email-freepik.com/lib/fe3111717564047f7c1d74/m/1/d04710f5-beeb-4c8c-b884-2b4a20a758a5.png" alt="Follow Freepik on Pinterest" width="34" style="margin:0;padding:0;display:block;width:34px;height:34px" class="CToWUd">
</a>
</td> 
--}}
{{-- 
<td>
<a href="https://click.email-freepik.com/?qs=88960593bdc79b377c8d1d467966aa18a271196905bee6426b9586ae94763960b7f2b669fe42be3030f5379bd3f12a1499086139f8e0cb31ccbf0830fbe92b1a" alt="Follow Freepik on Instagram" style="margin-top:0;margin-bottom:0;margin-left:8px;margin-right:8px;padding:0;display:block" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/?qs%3D88960593bdc79b377c8d1d467966aa18a271196905bee6426b9586ae94763960b7f2b669fe42be3030f5379bd3f12a1499086139f8e0cb31ccbf0830fbe92b1a&amp;source=gmail&amp;ust=1642073540244000&amp;usg=AOvVaw1Y98SxmJumH7hCkQB7ehSL">
<img src="https://ci3.googleusercontent.com/proxy/H1mBXyrIKNAteHb-MaZ-euO4a9r3JWeftdIfudU0Dpk3WAIuf04iWTb3hEhCFRh6S0JyhbfJApTkjojIpnFbAJM-YC-vj3vFLvR8BxuZ6AO0xXUlLlqLbuu-4MvITCP52jb5mZTT9FA3W7I9ZB4QJ7s7SeJqFkgLhiI=s0-d-e1-ft#https://image.email-freepik.com/lib/fe3111717564047f7c1d74/m/1/1d431f80-e160-4a92-82ce-f4fb1c0f33cd.png" alt="Follow Freepik on Instagram" width="34" style="margin:0;padding:0;display:block;width:34px;height:34px" class="CToWUd">
</a>
</td> 
--}}
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%">
<tbody>
<tr>
<td align="center" style="padding:0px 0px 50px">
<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="text-align:center;background-color:transparent;min-width:100%">
<tbody>
<tr>
<td style="padding:0px 30px" align="center">
<p style="font-size:13px;line-height:200%;text-align:center;max-width:400px;margin:0 auto">
<span style="font-size:13px;line-height:19px;font-family:Arial,Helvetica,sans-serif;color:#5f7d95">
{{ $officialCopyrightMessage }}
</span>
</p>
<p style="font-size:13px;line-height:200%;text-align:center;max-width:400px;margin:5px auto 20px">
<span style="font-size:13px;line-height:19px;font-family:Arial,Helvetica,sans-serif;color:#5f7d95">
{!! $officialContactAddress !!}
</span>
</p>
{{-- 
<p style="font-size:13px;line-height:16px;text-align:center;max-width:450px;margin:auto">
<span style="font-size:13px;line-height:19px;font-family:Arial,Helvetica,sans-serif;color:#5f7d95">Message sent to <span style="color:#1273eb;text-decoration:none">
<a href="mailto:chutneyhouseuk@gmail.com" target="_blank">chutneyhouseuk@gmail.com</a></span>. If you don’t want to receive this type of message, please 
<a href="https://click.email-freepik.com/profile_center.aspx?qs=65f60d161c238c23b13c902faf49dafe66c7468a27c59f19f021710bed92a47ceed3a7e6b03ba50e9c848b3bacfe59ac94556fac937497991e9a24cb21eca7b43ff84e21679166aa" style="color:#5f7d95;text-decoration:underline" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/profile_center.aspx?qs%3D65f60d161c238c23b13c902faf49dafe66c7468a27c59f19f021710bed92a47ceed3a7e6b03ba50e9c848b3bacfe59ac94556fac937497991e9a24cb21eca7b43ff84e21679166aa&amp;source=gmail&amp;ust=1642073540244000&amp;usg=AOvVaw35LSlcZsUD4_57z0sVMp2C">update
</a> your preferences 
<a href="https://click.email-freepik.com/subscription_center.aspx?qs=65f60d161c238c239edbdef565633a9e08ae1582453b80ce39982f5958e9d6a8099964b8cb22982c36cbb8608d95317494a87663de9aed03320eb3a99cb173a7433e90238c0ed3b7" style="color:#5f7d95;text-decoration:underline" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/subscription_center.aspx?qs%3D65f60d161c238c239edbdef565633a9e08ae1582453b80ce39982f5958e9d6a8099964b8cb22982c36cbb8608d95317494a87663de9aed03320eb3a99cb173a7433e90238c0ed3b7&amp;source=gmail&amp;ust=1642073540244000&amp;usg=AOvVaw0PktE3Vpmxia3F1VsZRCL0">here
</a> or 
<a href="https://click.email-freepik.com/unsub_center.aspx?qs=65f60d161c238c239edbdef565633a9e08ae1582453b80ce39982f5958e9d6a82807fad4b56c45fa880a53bed4b299a7a13fe5294a159aef17010a7958258c0d2ace8ede8c7a41e9" style="color:#5f7d95;text-decoration:underline" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://click.email-freepik.com/unsub_center.aspx?qs%3D65f60d161c238c239edbdef565633a9e08ae1582453b80ce39982f5958e9d6a82807fad4b56c45fa880a53bed4b299a7a13fe5294a159aef17010a7958258c0d2ace8ede8c7a41e9&amp;source=gmail&amp;ust=1642073540245000&amp;usg=AOvVaw1HSPoNj_E8d2binJ0taq6C">unsubscribe</a> from this list.</span>
</p>  
--}} 
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</body>
</html>