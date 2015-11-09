<?php
	if (!defined('INDEX'))
		exit('No direct script access allowed');

	if (isset($_GET['paypal'])) { ?><div class="g_12"><div class="success iDialog">Your order is currently being processed, you should see your credits arrive within 24 hours</div></div><?php } ?>

<?php
    if (!isset($_SESSION['cart']))	{
        $_SESSION['cart'] = "";
    }

    if (isset($_POST['submit']))	{
        if ($_POST['submit'] == "Add to Cart")		{
            $_SESSION['cart'] .= $_POST['credit'] . ",";
        } else if ($_POST['submit'] == "Remove")		{
            $_SESSION['cart'] = replaceFirst($_SESSION['cart'], $_POST['id'] . ",", "");
        } else if ($_POST['submit'] == "Clear All")		{
            $_SESSION['cart'] = "";
        }
    }

    $cart = array();
    $paypal_cart = array();
    $total = 0;

    foreach (explode(",", $_SESSION['cart']) as $item)	{
        if ($item != "")		{
            if ($item == "1")			{
                $cart[] = "1:1,000 Credits:$1.00 USD";
                $paypal_cart[] = "1";
                $total = $total + 1;
            } else if ($item == "2")			{
                $cart[] = "2:5,000 Credits:$5.00 USD";
                $paypal_cart[] = "2";
                $total = $total + 5;
            } else if ($item == "3")			{
                $cart[] = "3:10,000 Credits:$10.00 USD";
                $paypal_cart[] = "3";
                $total = $total + 10;
            } else if ($item == "4")			{
                $cart[] = "4:20,000 Credits:$18.00 USD";
                $paypal_cart[] = "4";
                $total = $total + 18;
            } else if ($item == "5")			{
                $cart[] = "5:50,000 Credits:$40.00 USD";
                $paypal_cart[] = "5";
                $total = $total + 40;
            } else if ($item == "6")			{
                $cart[] = "6:100,000 Credits:$70.00 USD";
                $paypal_cart[] = "6";
                $total = $total + 70;
            } else if ($item == "7")			{
                $cart[] = "7:500,000 Credits:$300.00 USD";
                $paypal_cart[] = "7";
                $total = $total + 300;
            }
        }
    }

    function replaceFirst($input, $search, $replacement)	{
        $pos = stripos($input, $search);
        if($pos === false)		{
            return $input;
        } else		{
            $result = substr_replace($input, $replacement, $pos, strlen($search));
            return $result;
        }
    }
?>

<div class="g_12">
    <div class="widget_header">
        <h4 class="widget_header_title">Earn Credits</h4>
    </div>

    <div class="widget_contents noPadding">
        <table class="tables">
            <tbody>
                <tr>
                    <td><img style="margin-left: 4px; vertical-align: middle;" src="/img/chrome_logo.png" width="50" height="50" /></td>
                    <td><a href="/Downloads/GrabViews Viewer.crx" target="_blank"><b><span class="label">Download</span></b></a> our GrabViews Chrome Viewer extension<br />then drag and drop it into <b>chrome://extensions/</b><br />(It will say that it's not able to install, but go to your downloads and the file will be there)</td>
                    <td><a href="/Downloads/GrabViews Viewer.crx" target="_blank"><b><span class="label">Download</span></b></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="g_12">
	<div class="widget_header">
		<h4 class="widget_header_title">Purchase Credits</h4>
	</div>
	
	<div class="widget_contents noPadding">
        <form method="post" action="" id="selection">
            <div class="g_12">
                <input tabindex="0" type="submit" name="submit" value="Add to Cart" class="submitIt simple_buttons" style="float: right; margin-top: 6px;" />

                <div style="float: right; margin-top: 5px;">
                    <select name="credit" class="simple_form">
                        <option value="1">1,000 Credits $1.00 USD</option>
                        <option value="2">5,000 Credits $5.00 USD</option>
                        <option value="3">10,000 Credits $10.00 USD</option>
                        <option value="4">20,000 Credits $18.00 USD</option>
                        <option value="5">50,000 Credits $40.00 USD</option>
                        <option value="6">100,000 Credits $70.00 USD</option>
                        <option value="7">500,000 Credits $300.00 USD</option>
                    </select>
                </div>

                <span class="label" style="float: right; margin-right: 15px; margin-top: 12px;">Credits:</span>
            </div>
        </form>
        <form id="paypal_form" action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="post" style="display: none;">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL; ?>">
            <input type="hidden" name="item_name" value="Bundle, User ID: <?php echo $user['id']; ?>">
            <input type="hidden" name="item_number" value="<?php echo implode(",", $paypal_cart); ?>">
            <input type="hidden" name="custom" value="mixed_products">
            <input type="hidden" name="amount" value="<?php echo $total; ?>">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="notify_url" value="http://<?php echo WEBSITE_URL; ?>/?paypal=paypal_ipn_buy">
            <input type="hidden" name="lc" value="US">
            <input type="hidden" name="bn" value="PP-BuyNowBF">
            <input type="hidden" name="cancel_return" value="http://<?php echo WEBSITE_URL; ?>/?page=Get+Credits">
            <input type="hidden" name="return" value="http://<?php echo WEBSITE_URL; ?>/?page=Get+Credits&paypal=success">
        </form>

        <table class="dDDatatable tables">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Cost</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($cart as $item) {
                        $arr = explode(":", $item);
                        $num = $arr[0];
                        $name = $arr[1];
                        $price = $arr[2];
                        ?>

                        <tr>
                            <td><b><?php echo $name; ?></b></td>
                            <td><b><?php echo $price; ?></b></td>
                            <td><b><form method="POST" action=""><input type="hidden" name="id" value="<?php echo $num; ?>" /><input type="submit" name="submit" value="Remove" style="outline: none;" class="submitIt simple_buttons" /></form></b></td>
                        </tr>

                        <?php
                    }
                ?>

                <tr>
                    <td style="text-align: right;"><b>Total:</b></td>
                    <td><b>$<?php echo $total; ?>.00 USD</b></td>
                    <td><b><form method="POST" action=""><input type="submit" name="submit" value="Clear All" style="outline: none;" class="submitIt simple_buttons" /></form></b></td>
                </tr>
            </tbody>
        </table>
	</div>
</div>

<script type="text/javascript">
    $(function() {
        $("#selection input").first().blur();

        $(".dDDatatable").dataTable({
            "bSort": false,
            "sDom": "<'dtTop'><'dtTables't><'dtBottom'>",
            "oLanguage": {
                "sLengthMenu": "Show entries _MENU_"
            },
            "fnInitComplete": function(){
                $(".dtShowPer select").uniform();
                $(".dtFilter input").addClass("simple_field").css({
                    "width": "auto",
                    "margin-left": "15px"
                });
                $(".dtBottom").append("<input id='paypal_checkout' style='float: right; margin-top: 11px;' type='submit' name='submit' value='Paypal Checkout' class='submitIt simple_buttons' />");
                $("#paypal_checkout").click(function() {
                    $("#paypal_form").submit();
                });
            }
        });
    });
</script>