<?php
if (!defined('INDEX'))
	exit('No direct script access allowed');

class IpnListener {
    private $mysqli = null;

    public $force_ssl_v3 = true;

    public $follow_location = false;

    public $use_ssl = true;

    public $use_sandbox = false;

    public $timeout = 30;

    private $post_data = array();
    private $post_uri = '';
    private $response_status = '';
    private $response = '';

    const PAYPAL_HOST = 'www.paypal.com';
    const SANDBOX_HOST = 'www.sandbox.paypal.com';

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    protected function curlPost($encoded_data) {
        if ($this->use_ssl)		{
            $uri = 'https://' . $this->getPaypalHost() . '/cgi-bin/webscr';
            $this->post_uri = $uri;
        } else		{
            $uri = 'http://' . $this->getPaypalHost() . '/cgi-bin/webscr';
            $this->post_uri = $uri;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/../api_cert_chain.crt");
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($this->force_ssl_v3)		{
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }

        $this->response = curl_exec($ch);
        $this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($this->response === false || $this->response_status == '0')		{
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
    }

    private function getPaypalHost()	{
        if ($this->use_sandbox) return self::SANDBOX_HOST;
        else return self::PAYPAL_HOST;
    }

    public function getPostUri()	{
        return $this->post_uri;
    }

    public function getResponse()	{
        return $this->response;
    }

    public function getResponseStatus()	{
        return $this->response_status;
    }

    public function processSubIpn($post_data = null)
    {
        $id = explode(",", str_replace(" User ID: ", "", $_POST['item_name']))[1];

        $stmt = $this->mysqli->prepare("SELECT `group` FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($user_group);
        $stmt->fetch();
        $stmt->close();

        try {
            $result = $this->processIpnData($post_data);

            $id = explode(",", str_replace(" User ID: ", "", $_POST['item_name']))[1];
            $upgrade = explode(",", str_replace(" User ID: ", "", $_POST['item_name']))[0];

            $total_cal = 0;
            $pot_credits = 0;
            $credits = 0;

            $group = 2;
            $subscr_id = $_POST['subscr_id'];

            switch ($upgrade) {
                case "Level 1 Premium":
                    $total_cal = 5;
                    $group = 3;
                    $pot_credits = 10000;
                    break;
                case "Level 2 Premium":
                    $total_cal = 15;
                    $group = 4;
                    $pot_credits = 50000;
                    break;
                case "Level 3 Premium":
                    $total_cal = 30;
                    $group = 5;
                    $pot_credits = 100000;
                    break;
            }

            $priority = 0;
            $one = 1;

            if ($result) {
                if ($stmt = $this->mysqli->prepare("SELECT id FROM paypal_transactions WHERE type = 'sub' AND txn_id = ?")) {
                    $stmt->bind_param('s', isset($_POST['txn_id']) ? $_POST['txn_id'] : $one);
                    $stmt->execute();
                    $stmt->store_result();

                    if($stmt->num_rows == 0) {
                        $stmt->close();

                        if ($_POST['receiver_email'] == PAYPAL_EMAIL) {
                            if ($_POST['txn_type'] == "subscr_payment") {
                                $total_pay = intval($_POST['mc_gross']);

                                if ($_POST['payment_status'] == "Completed") {
                                    if ($total_cal == $total_pay) {
                                        $status = "VERIFIED";
                                        $priority = 0;

                                        $credits = $pot_credits;
                                    } else {
                                        $status = "MONEY";
                                        $priority = 2;
                                    }
                                } else {
                                    $status = "PAYMENT";
                                    $priority = 5;
                                }
                            } else if ($_POST['txn_type'] == "subscr_cancel") {
                                $status = "CANCEL";
                                $priority = 0;

                                $subscr_id = "00000000000000000";
                            } else if ($_POST['txn_type'] == "subscr_eot") {
                                $status = "EOT";
                                $priority = 0;

                                $subscr_id = "00000000000000000";
                            } else if ($_POST['txn_type'] == "subscr_signup") {
                                $status = "SIGNUP";
                                $priority = 0;
                            } else {
                                $status = "TXN_TYPE";
                                $priority = 5;

                                $subscr_id = "00000000000000000";
                            }
                        } else {
                            $status = "EMAIL";
                            $priority = 4;
                        }
                    } else {
                        $stmt->close();

                        $status = "DUPLICATE";
                        $priority = 3;
                    }
                }
            } else if (!$result) {
                $status = "INVALID";
                $priority = 1;
            }

            $total_cal .= ".00";
            $resolved = $priority == 0 ? 1 : 0;

            $_POST['payment_date'] = strtotime($_POST['payment_date']);
            $_POST['subscr_date'] = strtotime($_POST['subscr_date']);

            if ($_POST['txn_type'] == "subscr_payment") {
                $stmt = $this->mysqli->prepare("INSERT INTO paypal_transactions (type, priority, resolved, user_id, subscr_id, txn_type, txn_id, payment_status, payer_email, payer_status, status, item_name, item_number, `group`, total, mc_gross, `date`) VALUES ('sub', ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('iiissssssssiisss', $priority, $resolved, $id, $_POST['subscr_id'], $_POST['txn_type'], $_POST['txn_id'], $_POST['payment_status'], $_POST['payer_email'], $_POST['payer_status'], $status, $_POST['item_name'], $_POST['item_number'], $group, $total_cal, $_POST['mc_gross'], $_POST['payment_date']);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $this->mysqli->prepare("INSERT INTO paypal_transactions (type, priority, resolved, user_id, subscr_id, txn_type, payer_email, payer_status, status, item_name, item_number, `group`, total, `date`) VALUES ('sub', ?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('iiissssssiiss', $priority, $resolved, $id, $_POST['subscr_id'], $_POST['txn_type'], $_POST['payer_email'], $_POST['payer_status'], $status, $_POST['item_name'], $_POST['item_number'], $group, $total_cal, $_POST['subscr_date']);
                $stmt->execute();
                $stmt->close();
            }

            if ($status != "VERIFIED" && $status != "SIGNUP") {
                $group = 2;
            }

            if ($user_group >= 2) {
                $stmt = $this->mysqli->prepare("UPDATE users SET `group`=?, subscr_id=?, credits=credits+? WHERE id=?");
                $stmt->bind_param('isii', $group, $subscr_id, $credits, $id);
                $stmt->execute();
                $stmt->close();
            }

            if ($priority <= 4 && $priority != 0 && $user_group >= 2) {
                $suspend_msg = "Your account has been suspended because of a suspicious paypal transaction, which we are further investigating";

                $stmt = $this->mysqli->prepare("UPDATE users SET `group`=1, subscr_id='00000000000000000', suspend_msg=? WHERE id=?");
                $stmt->bind_param('si', $suspend_msg, $id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) { }
    }

    public function processBuyIpn($post_data = null)
    {
        $id = str_replace("Bundle, User ID: ", "", $_POST['item_name']);

        $stmt = $this->mysqli->prepare("SELECT `group` FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($group);
        $stmt->fetch();
        $stmt->close();

        try {
            $result = $this->processIpnData($post_data);

            $total_cal = 0;
            $total_credits = 0;

            $priority = 0;

            foreach (explode(",", $_POST['item_number']) as $item) {
                if ($item != "") {
                    if ($item == "1") {
                        $total_cal = $total_cal + 1;
                        $total_credits = $total_credits + 1000;
                    } else if ($item == "2") {
                        $total_cal = $total_cal + 5;
                        $total_credits = $total_credits + 5000;
                    } else if ($item == "3") {
                        $total_cal = $total_cal + 10;
                        $total_credits = $total_credits + 10000;
                    } else if ($item == "4") {
                        $total_cal = $total_cal + 18;
                        $total_credits = $total_credits + 20000;
                    } else if ($item == "5") {
                        $total_cal = $total_cal + 40;
                        $total_credits = $total_credits + 50000;
                    } else if ($item == "6") {
                        $total_cal = $total_cal + 70;
                        $total_credits = $total_credits + 100000;
                    } else if ($item == "7") {
                        $total_cal = $total_cal + 300;
                        $total_credits = $total_credits + 500000;
                    }
                }
            }

            if ($result) {
                if ($stmt = $this->mysqli->prepare("SELECT id FROM paypal_transactions WHERE type = 'buy' AND txn_id = ?")) {
                    $stmt->bind_param('s', $_POST['txn_id']);
                    $stmt->execute();
                    $stmt->store_result();

                    if($stmt->num_rows == 0) {
                        $stmt->close();

                        if ($_POST['payment_status'] == "Completed" && $_POST['receiver_email'] == PAYPAL_EMAIL) {
                            $total_pay = intval($_POST['mc_gross']);

                            if ($total_cal == $total_pay) {
                                $status = "VERIFIED";
                                $priority = 0;

                                if ($group >= 2) {
                                    $stmt = $this->mysqli->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
                                    $stmt->bind_param('ii', $total_credits, $id);
                                    $stmt->execute();
                                    $stmt->close();
                                }
                            } else {
                                $status = "MONEY";
                                $priority = 2;
                            }
                        } else if ($_POST['payment_status'] == "Completed" && $_POST['receiver_email'] != PAYPAL_EMAIL) {
                            $status = "EMAIL";
                            $priority = 4;
                        } else if ($_POST['payment_status'] != "Completed" && $_POST['receiver_email'] == PAYPAL_EMAIL) {
                            $status = "PAYMENT";
                            $priority = 5;
                        } else {
                            $status = "BAD";
                        }
                    } else {
                        $stmt->close();

                        $status = "DUPLICATE";
                        $priority = 3;
                    }
                }
            } else if (!$result) {
                $status = "INVALID";
                $priority = 1;
            }

            $total_cal .= ".00";
            $resolved = $priority == 0 ? 1 : 0;

            $_POST['payment_date'] = strtotime($_POST['payment_date']);

            $stmt = $this->mysqli->prepare("INSERT INTO paypal_transactions (type, priority, resolved, user_id, txn_id, payment_status, payer_email, payer_status, status, item_name, item_number, credits, total, mc_gross, `date`) VALUES ('buy', ?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('iiisssssssisss', $priority, $resolved, $id, $_POST['txn_id'], $_POST['payment_status'], $_POST['payer_email'], $_POST['payer_status'], $status, $_POST['item_name'], $_POST['item_number'], $total_credits, $total_cal, $_POST['mc_gross'], $_POST['payment_date']);
            $stmt->execute();
            $stmt->close();

            if ($priority <= 4 && $priority != 0 && $group >= 2) {
                $suspend_msg = "Your account has been suspended because of a suspicious paypal transaction, which we are further investigating";

                $stmt = $this->mysqli->prepare("UPDATE users SET `group`=1, subscr_id='00000000000000000', suspend_msg=? WHERE id=?");
                $stmt->bind_param('si', $suspend_msg, $id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) { }
    }

    public function processIpnData($post_data = null)
    {
        $encoded_data = 'cmd=_notify-validate';

        if ($post_data === null) {
            if (!empty($_POST)) {
                $this->post_data = $_POST;
                $encoded_data .= '&' . file_get_contents('php://input');
            } else {
                throw new Exception("No POST data found.");
            }
        } else {
            $this->post_data = $post_data;

            foreach ($this->post_data as $key => $value) {
                $encoded_data .= "&$key=" . urlencode($value);
            }
        }

        $this->curlPost($encoded_data);

        if (strpos($this->response_status, '200') === false) {
            throw new Exception("Invalid response status: " . $this->response_status);
        }

        if (strpos($this->response, "VERIFIED") !== false) {
            return true;
        } elseif (strpos($this->response, "INVALID") !== false) {
            return false;
        } else {
            throw new Exception("Unexpected response from PayPal.");
        }
    }

    public function requirePostMethod() {
        if ($_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Allow: POST', true, 405);
            throw new Exception("Invalid HTTP request method.");
        }
    }
}
?>