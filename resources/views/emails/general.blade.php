<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
</head>

<title>{{ ucfirst($generalSettings->app_name) }} Mail</title>

<style type="text/css"></style>

<body> 
  <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background-color: #eff3f6">
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
                                                          <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%">
                                                            <tbody>
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
                                                                                  <a href="{{ config('app.url') }}" title="{{ ucfirst($generalSettings->app_name) }}" target="_blank">
                                                                                    <img src="{{asset('system/logo.png')}}" alt="{{ ucfirst($generalSettings->app_name) }} Logo" height="40" width="150" style="display:block;height:40px;width:150px;text-align:center;padding:30px 20px" class="CToWUd">
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
                                                                <td>
                                                                  <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="min-width:100%">
                                                                    <tbody>
                                                                      <tr>
                                                                        <td style="padding:0px 0px 20px">
                                                                          <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:#ffffff;min-width:100%">
                                                                            <tbody>
                                                                              <tr>
                                                                                <td style="padding:54px 40px 60px">
                                                                                  {!! $body !!}
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
                                                                        <td style="padding:0px"><table cellspacing="0" cellpadding="0" role="presentation" style="width:100%">
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
                                                                                              <td style="padding:0px"><p style="text-align:center;font-size:14px;line-height:16px;color:#5f7d95;margin:0;padding:0">
                                                                                                <span style="font-family:Arial,Helvetica,sans-serif">Sent with 
                                                                                                </span>
                                                                                                <img alt="Sent with love by {{ ucfirst($generalSettings->app_name) }}" height="14" src="https://ci6.googleusercontent.com/proxy/3RsTDe_REyoJReeTQHZk0LceFdxVOeynng-g6UXqEnV40CT39ur8KD2TnWSozamlFpN6Ld7ot5I1KxZ2dH0-E9dTpQD6XebkAxkV9G6I5qqq20beS6YHSBw881A1F7lnnEECyo8zDVZD6PruNU8DdYq1_JZfg-os5Kg=s0-d-e1-ft#https://image.email-freepik.com/lib/fe3111717564047f7c1d74/m/1/585cd126-f1bc-4504-88f1-ec80e64f7622.png" style="display:inline-block;padding:0px 2px;text-align:center;height:14px;width:16px;border:0px" width="16" class="CToWUd"><span style="font-family:Arial,Helvetica,sans-serif"> by {{ ucfirst($generalSettings->app_name) }}</span>
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
                                                                                      <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="background-color:transparent;border:0px;min-width:100%">
                                                                                        <tbody>
                                                                                          <tr>
                                                                                            <td style="padding:24px 0px">
                                                                                              <table border="0" cellpadding="0" cellspacing="0" align="center">
                                                                                                <tbody>
                                                                                                  <tr>

                                                                                                    @foreach ($generalSettings->medias as $media)
                                                                                                    <td>
                                                                                                      <a href="{{ $media->link }}" alt="Follow {{ ucfirst($generalSettings->app_name) }} on {{ ucfirst($media->name) }}" style="margin-top:0;margin-bottom:0;margin-left:8px;margin-right:8px;padding:0;display:block" target="_blank">
                                                                                                        <img src="{{ asset($media->logo) }}" alt="Follow {{ ucfirst($generalSettings->app_name) }} on {{ ucfirst($media->name) }}" width="34" style="margin:0;padding:0;display:block;width:34px;height:34px" class="CToWUd">
                                                                                                      </a>
                                                                                                    </td>
                                                                                                    @endforeach

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
                                                                        <span style="font-size:13px;line-height:19px;font-family:Arial,Helvetica,sans-serif;color:#5f7d95">{{ ucfirst($generalSettings->copyright_message) }}</span>
                                                                      </p>
                                                                      <p style="font-size:13px;line-height:200%;text-align:center;max-width:400px;margin:5px auto 20px">
                                                                        <span style="font-size:13px;line-height:19px;font-family:Arial,Helvetica,sans-serif;color:#5f7d95">{!! ucfirst($generalSettings->official_contact_address) !!}</span>
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
