@component('mail::message')


<table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#000000;">
  <tr>
    <td width="50%" valign="middle" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">

      <div class="column" style="width:100%;display:inline-block;vertical-align:middle;">
        <table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#333333;">
          <tr>
            <td class="text-center" style="text-align:left;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;">
              <p style="color:#999999;margin:0;font-style:normal;font-variant:normal;font-weight:normal;font-size:14px;font-family:Tahoma, Arial, sans-serif;line-height:normal;">
              {{$title}}
              </p>
            </td>
          </tr>
        </table>
      </div>

    </td>
  </tr>
</table>


<table align="center" style="background:#ffffff; width:100%;">
  <tr>
    <td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;">

      <table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#000000;">
        <tr>
          <td width="50%" valign="middle" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">

            <div class="column" style="width:100%;max-width:300px;display:inline-block;vertical-align:middle;">
              <table width="100%" style="float:left; border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#000000;">
                <tr>
                  <td class="text-center" style="text-align:left;padding-top:20px;padding-bottom:20px;padding-right:10px;padding-left:10px;">
                    <a href="https://u-marketing.org/?utm_source=mailletter" target="_blank" style="text-decoration:none;"><img src="https://u-marketing.org/public/images/logomail.png" alt="U-marketing" width="167" style="border-width:0;display:inline-block;"></a>
                  </td>
                </tr>
              </table>
            </div>

          </td>
          <td width="50%" valign="middle" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">

            <div class="column" style="width:100%;max-width:300px;display:inline-block;vertical-align:middle;">
              <table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#000000;">
                <tr>
                  <td class="text-center" style="width:100%;padding-top:20px;padding-bottom:20px;padding-right:10px;padding-left:10px;text-align:right;">

<a href="https://cp.u-marketing.org/sms/index" target="_blank" style="background-color:#a1d308;border:1px solid #f5fadf;border-radius:20px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:15px;font-weight:normal;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">СОЗДАТЬ РАССЫЛКУ</a>

                  </td>
                </tr>
              </table>
            </div>

          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>


<table align="center" style="background:#ffffff;     width: 100%;">
  <tr>
    <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;">
      <div class="column" style="width:100%;max-width:100%;display:inline-block;vertical-align:top;">
        <table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#000000;">
          <tr>
            <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;width:100%;">
              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="height:238px;">
                <tr>
                  <td valign="top">

                    <img src="{{$image?'https://cp.u-marketing.org/images/notifications/'.$image:''}}">


                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>

<table align="center" style="background:#ffffff;     width: 100%;">

  <tr>
    <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;">
      <div class="column" style="width:100%;max-width:100%;display:inline-block;vertical-align:top;">
        <table width="100%" style="border-spacing:0;border-collapse:collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;color:#333333;">

          <tr>
            <td style="padding-top:10px;padding-bottom:10px;padding-right:20px;padding-left:20px;width:100%;text-align:left;">
              <p style="padding:30px 0 10px 0; color:#444444;margin:0;font-style:normal;font-variant:normal;font-weight:normal;font-size:14px;font-family:Tahoma, Arial, 'Helvetica Neue', Helvetica, sans-serif;line-height:22px;text-align:left; font-weight:bold;">

              <?php
     echo (html_entity_decode(nl2br($message)));
              ?>

              </p>

            </td>
          </tr>









        </table>
      </div>
    </td>
  </tr>
</table>

@endcomponent
