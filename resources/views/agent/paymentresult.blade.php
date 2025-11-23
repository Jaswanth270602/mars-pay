<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $status === 'success' ? 'Payment Successful' : 'Payment Failed' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: {{ $status === 'success' ? '#f6fff6' : '#fff6f6' }};
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            text-align: center;
            padding: 40px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        .card h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: {{ $status === 'success' ? '#28a745' : '#dc3545' }};
        }
        .card p {
            font-size: 16px;
            color: #444;
        }
        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 24px;
            font-size: 15px;
            border-radius: 8px;
            text-decoration: none;
            background: {{ $status === 'success' ? '#28a745' : '#dc3545' }};
            color: white;
            transition: 0.3s;
        }
        .btn:hover {
            background: {{ $status === 'success' ? '#218838' : '#c82333' }};
        }
    </style>
</head>
<body>
<div class="card">
    @if ($status === 'success')
        <h1>Payment Successful!</h1>
        <p>Thank you! Your payment has been processed successfully.</p>
    @else
        <h1>Payment Failed</h1>
        <p>Oops! Something went wrong. Please try again.</p>
    @endif
    <a href="/" class="btn">Go Back</a>
</div>
</body>
</html>
