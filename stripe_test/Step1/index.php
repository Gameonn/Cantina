<?php 

require_once("../../php_include/db_connection.php");
/*if($_POST){

$a=json_encode($_POST);

$sth=$conn->prepare('Insert into payment values(DEFAULT,:json)');
$sth->bindValue('json',$a);
try{$sth->execute();}
catch(Exception $e){echo $e->getMessage();}

}
*/
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Wilde Things</title>
    <link rel="stylesheet" href="../public/css/normalize.css">
    <link rel="stylesheet" href="../public/css/style.css">
  </head>
  <body>
  <div id="container">
<?php
  require_once('../stripe/lib/Stripe.php');
  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );

  Stripe::setApiKey($stripe['secret_key']);

  if ($_POST) {
    $charge = Stripe_Charge::create(array(
      'card'     => $_POST['stripeToken'],
      'amount'   => 53500,
      'currency' => 'usd'
    ));

    $wildeQuotes = array(
      "A little sincerity is a dangerous thing, and a great deal of it is absolutely fatal.",
      "Always forgive your enemies; nothing annoys them so much.",
      "America is the only country that went from barbarism to decadence without civilization in between.",
      "I think that God in creating Man somewhat overestimated his ability.",
      "I am not young enough to know everything.",
      "Fashion is a form of ugliness so intolerable that we have to alter it every six months.",
      "Most modern calendars mar the sweet simplicity of our lives by reminding us that each day that passes is the anniversary of some perfectly uninteresting event.",
      "Scandal is gossip made tedious by morality."
      );

    echo "<h1>Here's your quote!</h1>";
    echo "<h2>".$wildeQuotes[array_rand($wildeQuotes)]."</h2>";
  }
  else
  { ?>
    <h2>Wilde Things</h2>
    <h3>Purchase a quote by Oscar Wilde today! Only $535! Limited supply and going fast, buy now!!</h3>

    <form action="index.php" method="post">
      <script src="https://button.stripe.com/v1/button.js" class="stripe-button"
        data-key="<?php echo $stripe['publishable_key']; ?>"
        data-amount=53500
        data-description="One Wilde quote"
        data-label="Buy"></script>
    </form>
  <?php
  }
?>
  </div><!-- #container -->
  </body>
</html>