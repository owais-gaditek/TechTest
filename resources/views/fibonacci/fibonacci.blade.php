<!-- resources/views/fibonacci.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fibonacci Sequence</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>
    <div class="fibonacci-container">
        <div class="fibonacci-header">
            <h1>Calculate Fibonacci Sequence</h1>
        </div>
        <form class="fibonacci-form" method="POST" action="{{ route('fibonacci.calculate') }}">
            @csrf
            <label for="n">Enter a number:</label>
            <input type="number" name="n" id="n" min="1" required>
            <button type="submit">Calculate</button>
        </form>
        <div class="fibonacci-results"></div>
    </div>
    <script src="{{ mix('js/fibonacci.js') }}"></script>
</body>

</html>