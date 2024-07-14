<?php
// Define your API key and base URL for the exchange rate API
$apiKey = '5bb239f952e22c458a47098a';
$apiUrl = 'https://v6.exchangerate-api.com/v6/' . $apiKey . '/latest/';

$currencies = ['CZK', 'MKD', 'EUR', 'USD'];
$exchangeRates = [];

// Function to fetch exchange rates for a given base currency
function fetch_exchange_rates($baseCurrency, $apiUrl) {
    $url = $apiUrl . $baseCurrency;

    // Use cURL to fetch the data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => $error_msg];
    }
    
    curl_close($ch);

    // Decode the JSON response
    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseCurrency = $_POST['currency'];
    $amount = floatval($_POST['amount']);

    // Fetch exchange rates for the selected base currency
    $exchangeRatesData = fetch_exchange_rates($baseCurrency, $apiUrl);

    if (isset($exchangeRatesData['error'])) {
        $error = $exchangeRatesData['error'];
    } else {
        $exchangeRates = $exchangeRatesData['conversion_rates'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .result {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 bg-white">
        <h1 class="text-center">Currency Converter</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="currency">Select currency:</label>
                <select class="form-control" name="currency" id="currency" required>
                    <?php foreach ($currencies as $currency): ?>
                        <option value="<?php echo $currency; ?>"><?php echo $currency; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Enter amount:</label>
                <input class="form-control" type="number" step="0.01" name="amount" id="amount" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Convert</button>
        </form>

        <?php if (isset($exchangeRates) && !empty($exchangeRates)): ?>
            <div class="result mt-4">
                <h2 class="text-center">Conversion Results:</h2>
                <ul class="list-group">
                    <?php foreach ($currencies as $currency): ?>
                        <?php if ($currency !== $baseCurrency): ?>
                            <li class="list-group-item">
                                <?php 
                                $convertedAmount = $amount * $exchangeRates[$currency];
                                echo '<b>' . number_format($amount, 2, ',', '.') . ' ' . $baseCurrency . '</b> = ' . number_format($convertedAmount, 2, ',', '.') . ' ' . $currency; 
                                ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($error)): ?>
            <p class="text-danger text-center">Error: <?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
