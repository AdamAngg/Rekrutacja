
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="public/style.css">
    <title>Currency conversion</title>

</head>
<body style="margin:4vw; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    <div style="display:flex; justify-content:center;">
    <div>
    <h1 style="text-align:center;">Available currencies</h1>
        <?= $tableMarkUp ?>
    </div>
    </div>
    <div style="display:flex; justify-content:center;">
    <div>
    <h1 style="text-align:center;">Latest conversions</h1>
    <?= $tableMarkUpLatestConversion ?>
    </div>
</div>

    <form action="HomeController.php" method="POST" style="grid-column: span 2; display:flex; justify-content:center;">
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount">

        <label for="currencyFrom">From:</label>
        <select id="currencyFrom" name="currencyFrom">

            <?php foreach ($currencies as $currency): ?>
                <option value="<?php echo $currency['mid'].'|'.$currency['id']?>"><?php echo $currency['code']."-".$currency['currency']; ?></option>
            <?php endforeach; ?>

        </select>

        
        <label for="currencyTo">To:</label>
        <select id="currencyTo" name="currencyTo">

            <?php foreach ($currencies as $currency): ?>
                <option value="<?php echo $currency['mid'].'|'.$currency['id']?>"><?php echo $currency['code']."-".$currency['currency']; ?></option>
            <?php endforeach; ?>

        </select>

        <input type="submit" value="Send">
    </form>
    <?= $error ?>
</body>
</html>