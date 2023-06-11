<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Zadanie rekrutacyjne</title>

</head>
<body>
    <h1>Witam na mojej stronie</h1>
    <?= $tableMarkUp ?>
    <h1>Cos</h1>
    <?= $tableMarkUpLatestConversion ?>
   

    <form action="HomeController.php" method="POST">
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