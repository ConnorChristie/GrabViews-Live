<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');

    $referrals = $mainSystem->videoAction->fetch_all_assoc("SELECT username,join_date FROM users WHERE referral='" . $user['id'] . "'");

    $current_page = 0;
    $total = count($referrals);
    $limit = 5;
    $pages = ceil($total / $limit);

    if (isset($_GET['p']))
    {
        $page = $_GET['p'];

        if (is_numeric($page)) {
            if ($page < 0) {
                $page = 0;
            } else if ($page > $pages) {
                $page = $pages;
            }

            $current_page = $page;
        }
    }

    $offset = $current_page * $limit;
    $end = min(($offset + $limit), $total);

    $referrals_to_display = array();

    for ($i = $offset; $i < $end; $i++) {
        array_push($referrals_to_display, $referrals[$i]);
    }

    if ($current_page - 1 >= 0 && $current_page - 1 == 0)	{
        $back_link = '<a href="/?page=Referrals" style="float: left;" tabindex="0" class="previous paginate_button" id="DataTables_Table_0_previous"><</a>';
    } else if ($current_page - 1 >= 0)	{
        $back_link = '<a href="/?page=Referrals&p=' . ($current_page - 1) . '" style="float: left;" tabindex="0" class="previous paginate_button" id="DataTables_Table_0_previous"><</a>';
    } else	{
        $back_link = '<a style="float: left; cursor: default;" tabindex="0" class="previous paginate_button" id="DataTables_Table_0_previous"><</a>';
    }

    if ($current_page + 1 < $pages)	{
        $forward_link = '<a href="/?page=Referrals&p=' . ($current_page + 1) . '" tabindex="0" class="previous paginate_button" id="DataTables_Table_0_previous">></a>';
    } else	{
        $forward_link = '<a style="cursor: default;" tabindex="0" class="previous paginate_button" id="DataTables_Table_0_previous">></a>';
    }

    $creditsLink = "You and your friend earn <span class='label' style='color: #b9b9b9;'><b>100</b></span>";

    if ($user['group'] != 2)	{
        $creditsLink = "You earn <span class='label' style='color: #b9b9b9;'><b>200</b></span> credits and your friend earns <span class='label' style='color: #b9b9b9;'><b>100</b></span>";
    }
?>

<div class="g_8" style="float: left;">
    <div class="widget_header">
        <h4 class="widget_header_title">Referrals</h4>
    </div>
    <div class="widget_contents" style="font-size: 12pt;">
        <span class="label">Your referral URL is: <span class="label" style="color: #b9b9b9;"><b>http://<?php echo WEBSITE_URL; ?>/?ref=<?php echo $user['id']; ?></b></span></span>
        <br />
        <span class="label"><?php echo $creditsLink; ?> credits if they sign up with your referral link!</span>
    </div>
</div>
<div class="g_4" style="float: right;">
    <div class="widget_header">
        <h4 class="widget_header_title">My Referrals</h4>
    </div>
    <div class="widget_contents">
        <?php
        foreach ($referrals_to_display as $arr) {
            $name = $arr['username'];
            $max = 18;

            if (strlen($name) > $max)
            {
                $name = substr($name, 0, $max - 2);
                $name .= "...";
            }

            ?>
            <div style="float: left; background-color: white; border: 1px solid #DBDBDB; width: 36px;">
                <img style="margin: 0 0 0 0; width: 36px; height: 36px;" src="img/user_avatar.png" alt="user_avatar" class="user_avatar" />
                <br style="clear: both;"/>
            </div>
            <div style="float: left; margin-left: 10px; margin-top: -2px;">
                <span style="font-size: 14pt;" class="label"><?php echo $name; ?></span>
                <br />
                <span class="label"><b>Member since:</b> <?php echo date("M j, Y", $arr['join_date']); ?></span>
            </div>
            <br style="clear: both;"/>
            <br />
        <?php
        }
        if ($total == 0) { ?>
            <div style="margin-left: 21px; margin-top: 17px;">
                <span style="font-size: 14pt;" class="label">You have no referrals</span>
            </div>
        <?php } ?>
        <?php if ($total > $limit) { ?>
            <div class="dtPagination" style="float: left;">
                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $back_link; ?>
                    <span>
                        <a style="cursor: default;" tabindex="0" class="paginate_active"><?php echo $current_page + 1; ?></a>
                    </span>
                    <?php echo $forward_link; ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($total > 0) { ?>
            <div style="float: right; margin-top: 17px;">
                <span class="label" style="font-size: 11pt;"><?php echo number_format($total); ?> Referrals</span>
            </div>
        <?php } ?>
        <br style="clear: both;"/>
    </div>
</div>