<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;

class Sendgrid
{
	
	
	public static function redumption_tpl($option){
		
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html data-editor-version="2" class="sg-campaigns" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" /><!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" /><!--<![endif]-->
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
      table {border-collapse: collapse;}
      table, td {mso-table-lspace: 0pt;mso-table-rspace: 0pt;}
      img {-ms-interpolation-mode: bicubic;}
    </style>
    <![endif]-->
    <style type="text/css">
      body, p, div {font-family: arial; font-size: 14px; } body {color: #333; } body a {color: #1188E6; text-decoration: none; } p { margin: 0; padding: 0; } table.wrapper {width:100% !important; table-layout: fixed; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -moz-text-size-adjust: 100%; -ms-text-size-adjust: 100%; } img.max-width {max-width: 100% !important; } .column.of-2 {width: 50%; } .column.of-3 {width: 33.333%; } .column.of-4 {width: 25%; } @media screen and (max-width:480px) {.preheader .rightColumnContent, .footer .rightColumnContent {text-align: left !important; } .preheader .rightColumnContent div, .preheader .rightColumnContent span, .footer .rightColumnContent div, .footer .rightColumnContent span {text-align: left !important; } .preheader .rightColumnContent, .preheader .leftColumnContent {font-size: 80% !important; padding: 5px 0; } table.wrapper-mobile {width: 100% !important; table-layout: fixed; } img.max-width {height: auto !important; } a.bulletproof-button {display: block !important; width: auto !important; font-size: 80%; padding-left: 0 !important; padding-right: 0 !important; } .columns {width: 100% !important; } .column {display: block !important; width: 100% !important; padding-left: 0 !important; padding-right: 0 !important; } } </style>
    <!--user entered Head Start-->
     <!--End Head user entered-->
  </head>
  <body>
    <center class="wrapper" data-link-color="#1188E6" data-body-style="font-size: 14px; font-family: arial; color: #333; background-color: #f4F4F4;">
      <div class="webkit">
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper" bgcolor="#f4F4F4">
          <tr>
            <td valign="top" bgcolor="#f4F4F4" width="100%">
              <table width="100%" role="content-container" class="outer" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td width="100%">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td>
                          <!--[if mso]>
                          <center>
                          <table><tr><td width="600">
                          <![endif]-->
                          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width:600px;" align="center">
                            <tr>
                              <td role="modules-container" style="padding: 50px 50px 50px 50px; color: #333; text-align: left;" bgcolor="#ffffff" width="100%" align="left">
    <table class="module preheader preheader-hide" role="module" data-type="preheader" border="0" cellpadding="0" cellspacing="0" width="100%"
           style="display: none !important; mso-hide: all; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
      <tr>
        <td role="module-content">
          <p></p>
        </td>
      </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns">
      <tr>
        <td style="padding:0px 0px 0px 0px;background-color:#ffffff;" bgcolor="#ffffff">
      <!--[if mso]>
      <table width="49%" align="left"><tr><td>
      <![endif]-->
      <table style="padding: 0px 0px 0px 0px;"
          align="left"
          valign="top"
          height="100%"
          class="column column-0 of-2 empty">
        <tr>
          <td class="columns--column-content">
    <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="left">
          <!--[if mso]>
    <center>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed;">
        <tr>
          <td width="100%" valign="top">
  <![endif]-->
            <img class="max-width" width="100%" height="auto" border="0" style="display:block;color:#000000;text-decoration:none;font-family:Helvetica, arial, sans-serif;font-size:16px;max-width:96px !important;width:100% !important;height:auto !important;" src="https://marketing-image-production.s3.amazonaws.com/uploads/9b8061dbcd19766432d7b7c6eac1776b823ef741fba9ad4a0872672e93d2558a4a0ca483a66f8e2143e57a44fcaa1c33fd4d93be29177c3449c2584f0112bbed.png" alt="Food Talk">
<!--[if mso]>
  </td>
    </tr>
      </table>
        </center>
<![endif]-->
        </td>
      </tr>
    </table>
          </td>
        </tr>
      </table>
      <!--[if mso]>
      </td></tr></table>
      </center>
      <![endif]-->
      <!--[if mso]>
      <table width="49%" align="left"><tr><td>
      <![endif]-->
      <table style="padding: 0px 0px 0px 0px;"
          align="left"
          valign="top"
          height="100%"
          class="column column-1 of-2 empty">
        <tr>
          <td class="columns--column-content">
          </td>
        </tr>
      </table>
      <!--[if mso]>
      </td></tr></table>
      </center>
      <![endif]-->
        </td>
      </tr>
    </table>
    <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;"
            height="100%"
            valign="top"
            bgcolor="">
            <div><span style="font-family:arial,helvetica,sans-serif;"><span style="font-size:20px;">Redemption alert at '.$option['restaurant_name'].'</span></span></div>
        </td>
      </tr>
    </table>
    <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;"
            height="100%"
            valign="top"
            bgcolor="">
            <div>There was a new offer redemption at '.$option['restaurant_name'].', '.$option['area'].'. Please find the details about the same below.&nbsp;</div>
<div>&nbsp;</div>
<div>Redemption id: <strong>'.$option['redeem_id'].'</strong></div>
<div>&nbsp;</div>
<div>
<div>
<table border="0" cellpadding="0" cellspacing="0" style="width:500px;">
	<tbody>
		<tr>
			<td>Name of the restaurant</td>
			<td>
      <div>'.$option['restaurant_name'].', '.$option['area'].'</div>
			</td>
		</tr>
		<tr>
			<td>Type of offer</td>
      <td>'.$option['offer'].'</td>
		</tr>
		<tr>
			<td>Number of coupons used</td>
      <td>'.$option['coupon_count'].'</td>
		</tr>
		<tr>
			<td>Date of redemption</td>
      <td>'.$option['date'].'</td>
		</tr>
		<tr>
			<td>Time of redemption</td>
			<td>
      <div>'.$option['time'].'</div>
			</td>
		</tr>
	</tbody>
</table>
</div>
<div>&nbsp;</div><div>&nbsp;</div>
<div>If there are any questions related to this redemption, please contact our support: <strong>contact@foodtalkindia.com</strong>&nbsp;</div>
</div>
        </td>
      </tr>
    </table>
                              </td>
                            </tr>
                          </table>
                          <!--[if mso]>
                          </td></tr></table>
                          </center>
                          <![endif]-->
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </center>
  </body>
</html>'; 
		
	}
	

	public static function sendMail($email, $subject , $body, $from = 'privilege@foodtalkindia.com', $cc= false , $contentType='text/html'){
		
		$request['personalizations'][]  = array(
				'subject'=> $subject,
				'to'=> array(array('email'=>$email))
		);
		
		$request['from'] = array('email'=>$from, 'name'=>'Food Talk Privilege');
	
		$request['content'] = array(array(
				'type'=>$contentType,
				'value'=>$body
				
		));
		
		return self::request('mail/send', json_encode($request));
		
	}
	
	public static final function request($api, $body = '', $method = 'POST'){
		
		$sendgridKey = getenv('SENDGRID_KEY');
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.sendgrid.com/v3/".$api,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_HTTPHEADER => array(
						"authorization: Bearer $sendgridKey",
						"cache-control: no-cache",
						"content-type: application/json",
				),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) {
			echo "cURL Error #:" . $err;
			return false;
		} else {
			return json_decode($response, true);
		}
		
	}
}