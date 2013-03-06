<? class fundingRedirect{


function post(){

global $user;
global $dbh;

$uuid = $_POST['goalUUID'];

$sth = $dbh->prepare("SELECT id FROM goals where uuid='$uuid' limit 1");
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$goal = new Goal($result['id']);

$project = new Project($goal->projectID);



$amount = $_POST['amount'];
$appFee = $amount * .04;
$recipientID = $project->wePayAccountID;
echo $recipientID;
$short_description = "Funding For Openfire Goal: " . $goal->name . " (Project: ". $project->title . ")";
$type = "DONATION";
$mode = "regular";
$redirect_uri = "https://" . $_SERVER['SERVER_NAME'] . "/fundingComplete/" . $goal->uuid . "/" . $user->uuid . "/" . $_POST['rewardUUID'] . "/" . $amount;


    // change to useProduction for live environments
    WePay::useStaging(WEPAY_CLIENT_ID, WEPAY_CLIENT_SECRET);

    $wepay = new WePay($project->wePayAccessToken);

    // create the checkout
    $response = $wepay->request('checkout/create', array(
        'account_id'        => $project->wePayAccountID,
        'amount'            => $amount,
        'short_description' => $short_description,
        'type'              => $type,
        'auto_capture' => FALSE,
        'mode' => $mode,
        'app_fee' => $appFee,
        'fee_payer' => "Payee",
        'redirect_uri' => $redirect_uri
    ));

    header("Location: " . $response->checkout_uri);


}


} ?>