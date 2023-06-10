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

    <form action="HomeModel.php" method="POST">
        <label for="amountFrom">Amount:</label>
        <input type="text" id="amountFrom" name="amount">

        <label for="currencyFrom">Currency:</label>
        <select id="currencyFrom" name="currency">
            <?php foreach ($currencies as $currency): ?>
                <option value="<?php echo $currency['code']; ?>"><?php echo $currency['currency']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="amountTo">Amount:</label>
        <input type="text" id="amountTo" name="amount">

        <label for="currencyTo">Currency:</label>
        <select id="currencyTo" name="currency">
            <?php foreach ($currencies as $currency): ?>
                <option value="<?php echo $currency['code']; ?>"><?php echo $currency['currency']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Send">
    </form>
</body>
</html>