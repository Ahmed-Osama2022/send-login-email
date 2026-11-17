<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laptop Opened Time</title>
</head>

<body style="margin:0;padding:24px;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
         style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e4e4e7;border-radius:8px;">
    <tr>
      <td style="padding:24px;">
        <h1 style="margin:0 0 16px;font-size:18px;color:#18181b;">Laptop Opened</h1>

        <p style="margin:0 0 12px;font-size:14px;line-height:20px;">
          Hello{{ $recipientName ? ' ' . $recipientName : '' }},
        </p>

        <p style="margin:0 0 12px;font-size:14px;line-height:20px;">
          Your laptop opened at:
          <strong>{{ $currentTime->format('l, F j, Y \a\t g:i:s A') }}</strong>.
        </p>

        <p style="margin:0;font-size:13px;line-height:20px;color:#52525b;">
          Timezone: {{ $currentTime->timezoneName }} ({{ $currentTime->format('P') }})<br>
          ISO&nbsp;8601: {{ $currentTime->toIso8601String() }}
        </p>
      </td>
    </tr>
  </table>
</body>

</html>
