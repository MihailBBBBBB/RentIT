<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PDF Download</title>
</head>
<body>
    <h2>Test PDF Download</h2>
    <form method="get" action="getPDF.php">
        <label for="id">Res_id:</label>
        <input type="number" id="id" name="id" min="1" required>
        <button type="submit">Get PDF</button>
</form>
</body>
</html>