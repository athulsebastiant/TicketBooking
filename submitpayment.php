<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,PUT,PATCH,DELETE');
header("Content-Type: application/json");
header("Accept: application/json");
header('Access-Control-Allow-Headers:Access-Control-Allow-Origin,Access-Control-Allow-Methods,Content-Type');

if (isset($_POST['action']) && $_POST['action'] = 'payOrder') {


    $razorpay_test_key = ''; //Your Test Key
    $razorpay_test_secret_key = ''; //Your Test Secret Key



    $razorpay_key = $razorpay_test_key;

    $authAPIkey = "Basic " . base64_encode($razorpay_test_key . ":" . $razorpay_test_secret_key);
    /*Array
(
    [oid] => 667817fa2b387
    [rp_payment_id] => pay_OQCplXW5n7q5qF
    [rp_signature] => 5cf1c6f145e6e3d4a91afbbee31440c3e1eb89327822f6626af96ce59bb4af5e
)*/

    // Set transaction details
    $order_id = $_POST['order_id'];

    $billing_name = $_POST['billing_name'];
    $billing_mobile = $_POST['billing_mobile'];
    $billing_email = $_POST['billing_email'];
    $shipping_name = $_POST['shipping_name'];
    $shipping_mobile = $_POST['shipping_mobile'];
    $shipping_email = $_POST['shipping_email'];
    $paymentOption = $_POST['paymentOption'];
    $payAmount = $_POST['payAmount'];
    error_log("Initial payAmount: " . $payAmount);
    $note = "Payment of amount Rs. " . $payAmount;

    $postdata = array(
        "amount" => (int)($payAmount * 100),
        "currency" => "INR",
        "receipt" => $note,
        "notes" => array(
            "notes_key_1" => $note,
            "notes_key_2" => ""
        )
    );

    error_log("Amount sent to Razorpay: " . $postdata['amount']);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postdata),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: ' . $authAPIkey
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $orderRes = json_decode($response);
    error_log("Razorpay response: " . print_r($orderRes, true));
    if (isset($orderRes->id)) {

        $rpay_order_id = $orderRes->id;

        $dataArr = array(
            'amount' => $payAmount,

            'description' => "Pay bill of Rs. " . $payAmount,
            'rpay_order_id' => $rpay_order_id,
            'name' => $billing_name,
            'email' => $billing_email,
            'mobile' => $billing_mobile
        );
        error_log("Amount sent to frontend: " . $dataArr['amount']);
        echo json_encode(['res' => 'success', 'order_number' => $order_id, 'userData' => $dataArr, 'razorpay_key' => $razorpay_key]);
        exit;
    } else {
        echo json_encode(['res' => 'error', 'order_id' => $order_id, 'info' => 'Error with payment']);
        exit;
    }
} else {
    echo json_encode(['res' => 'error']);
    exit;
}
