
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="{$INI['sn']['sn']}">
    <head>
        <meta http-equiv=content-type content="text/html; charset=UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
            <title>Coupon-{$coupon['id']}		</title>
    </head>
    <style type="text/css">
        body{ background:#fff;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;}
        *{ margin: 0 auto;}
        #ecard{ width: 90%; max-width:660px; clear:both; border:2px solid #45aeea; margin-top:40px;padding:15px;border-radius:15px;box-shadow:0 0 15px #45aeea;background: rgba(69, 174, 234, 0.1);}
        #econ{ width:100%; margin:0 auto; margin-bottom:10px; overflow:hidden;}
        #etop{ min-height:80px; border-bottom:1px solid #45aeea;}
        #logo{ width:280px; height:80px; float:left;}
        #logo img {height:75px;}
        #logo img {max-width: 100%;}
        #welcome{ color: #008fd5; float:left; font-weight:bold; font-size:26px; margin-top:20px; text-align:right; width:320px;}
        #teamtitle{ width:620px; text-align:left; font-size:20px; font-weight:bold; margin-top:8px; margin-bottom:10px;color:#008fd5; }
        #main{ width:620px; margin-bottom:20px;}
        #mleft{ float:left; width:320px; line-height:150%; }
        #name{ font-size:20px; font-weight:bold; margin-top:10px;}
        #scan{ font-size:14px; width:150px;margin-top:20px;float:left;}
        #scanimg { font-size:15px;width:115px;float:left; margin-bottom:15px}
        #relname{ font-size:14px; padding-left:8px;}
        #coupon{ margin-top:20px; font-size:26px; font-family:"bold"; font-weight:bold; text-align:left;}
        #coupon p { line-height:120%; }
        #mright{ float:right; width:300px;}
        #notice{font-size:14px;padding-top:8px;}
        #notice ul{ margin:0px; list-style:none; padding-left:0px;}
        #notice ul li{ line-height:26px;}
        #server{ background-color:#fff; width:620px; height:20px; font-size:14px; color:#008fd5; margin-top:20px; line-height:20px; text-align:center; clear:both;padding:10px;border-radius:10px;font-weight: bold;}


        .clearfix:before,
        .clearfix:after {
            content: " ";
            display: table;
        }
        .clearfix:after{
            clear: both;
        }

        #teamtitle small {
            display:block;
        }

        @media print { 
            .noprint{display:none;}
        }
    </style>

    <body>
        <div id="ecard">
            <div id="econ">
                <!--top -->
                <div id="etop" class="clearfix">
                    <div id="logo"><img src="http://opendeal/image/data/logo.png" /></div>
                    <div id="welcome"># {$coupon['id']} </div>
                </div>
                <!--endtop -->
                <div id="teamtitle"><span>{$team['product']}</span><small>{$team['option']}</small></div>
                <!--main -->
                <div id="main">

                    <div id="mleft">
                        <div id="name">${_('Expires on:')}</div>
                        <div id="relname">${date('Y-m-d', $coupon['expire_time'])}</div>
                        <div id="name">${_('How to use this:')}</div>
                        <div id="notice">
                            <ol>
                                <li>${_('Print')} {$INI['system']['couponname']}</li>
                                <li>${_('Call')} {$partner['title']} ${_('on')} {$partner['phone']}</li>
                                <li>${_('Mention')} {$INI['system']['couponname']} ${_('and Enjoy')}</li>
                            </ol>
                        </div>


                    </div>
                    <!--right -->
                    <div id="mright">
                        <div id="scanimg"><img src="/qrcode.php?id={$coupon['id']}&sec={$coupon['secret']}&partname={$partname}&partnum={$partner['phone']}" alt="{$coupon['id']}" /></div>
                        <div id="scan">${_('Scan this code to save the businesses details to your phone.')}</div>



                        <div style="padding-left:180px; margin-top:15px;">
                            <small>${_('Code#:')}{$coupon['secret']}</small>
                        </div>



                    </div>

                    <div style="clear:both;"></div>
                </div>
                <!--endmain -->

                <div id="server">Locations List</div>

            </div>

        </div>

        <div class="noprint" style="text-align:center; margin:20px;">
            <button style="padding:10px 20px; font-size:16px; cursor:pointer;" onclick="window.print();">${_('Print')} {$INI['system']['couponname']}</button></div>
    </body></html>
