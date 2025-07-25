<?php
session_start();

function convertToSwahiliWords($number) {
    if ($number == 0) {
        return 'sifuri';
    }

    $units = ['', 'moja', 'mbili', 'tatu', 'nne', 'tano', 'sita', 'saba', 'nane', 'tisa'];
    $teens = ['kumi', 'kumi na moja', 'kumi na mbili', 'kumi na tatu', 'kumi na nne',
              'kumi na tano', 'kumi na sita', 'kumi na saba', 'kumi na nane', 'kumi na tisa'];
    $tens = ['', 'ishirini', 'thelathini', 'arobaini', 'hamsini', 'sitini', 'sabini', 'themanini', 'tisini'];
    $hundreds = ['', 'mia moja', 'mia mbili', 'mia tatu', 'mia nne',
                 'mia tano', 'mia sita', 'mia saba', 'mia nane', 'mia tisa'];
    $thousands = ['', 'elfu moja', 'elfu mbili', 'elfu tatu', 'elfu nne',
                  'elfu tano', 'elfu sita', 'elfu saba', 'elfu nane', 'elfu tisa'];
    $millions = ['', 'milioni moja', 'milioni mbili', 'milioni tatu', 'milioni nne',
                 'milioni tano', 'milioni sita', 'milioni saba', 'milioni nane', 'milioni tisa'];

    $words = [];
    $num = (int)$number;

    // Millions
    if ($num >= 1000000) {
        $millionsPart = (int)($num / 1000000);
        if ($millionsPart < count($millions)) {
            $words[] = $millions[$millionsPart];
        }
        $num %= 1000000;
    }

    // Thousands
    if ($num >= 1000) {
        $thousandsPart = (int)($num / 1000);
        if ($thousandsPart < count($thousands)) {
            $words[] = $thousands[$thousandsPart];
        } else {
            $words[] = convertToSwahiliWords($thousandsPart) . ' elfu';
        }
        $num %= 1000;
    }

    // Hundreds
    if ($num >= 100) {
        $hundredsPart = (int)($num / 100);
        if ($hundredsPart < count($hundreds)) {
            $words[] = $hundreds[$hundredsPart];
        }
        $num %= 100;
    }

    // Tens and Units
    if ($num > 0) {
        if ($num < 10) {
            $words[] = $units[$num];
        } elseif ($num < 20) {
            $words[] = $teens[$num - 10];
        } else {
            $tensPart = (int)($num / 10);
            $unitsPart = $num % 10;
            $words[] = $tens[$tensPart - 1];
            if ($unitsPart > 0) {
                $words[] = 'na ' . $units[$unitsPart];
            }
        }
    }

    return implode(' na ', $words);
}

// Process POST and redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $_SESSION['amount'] = $amount;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// After redirect
$swahiliWords = '';
$formattedAmount = '';
if (isset($_SESSION['amount'])) {
    $amount = $_SESSION['amount'];
    $formattedAmount = number_format($amount, 2, '.', ',') . '/=';
    $swahiliWords = convertToSwahiliWords((int)round($amount));
    unset($_SESSION['amount']); // clear for next load
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money to Swahili Words Converter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Money to Swahili Words Converter</h1>

        <form method="post" action="">
            <div class="form-group">
                <label for="amount">Enter Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" required>
            </div>
            <button type="submit" class="btn">Convert</button>
        </form>

        <?php if (!empty($swahiliWords)) : ?>
            <div class="result"><br>
                <h3>Conversion Result:</h3><br>
                <p><?= $formattedAmount ?> , Shilingi <?= $swahiliWords ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
