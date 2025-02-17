<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Звіт</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Звіт по файлах та посиланнях</h1>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Параметр</th>
            <th>Значення</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><strong>Загальна кількість файлів</strong></td>
            <td>{{ $totalFiles }}</td>
        </tr>
        <tr>
            <td><strong>Кількість видалених файлів</strong></td>
            <td>{{ $deletedFiles }}</td>
        </tr>
        <tr>
            <td><strong>Загальна кількість одноразових посилань</strong></td>
            <td>{{ $totalLinks }}</td>
        </tr>
        <tr>
            <td><strong>Кількість використаних одноразових посилань</strong></td>
            <td>{{ $usedLinks }}</td>
        </tr>
        <tr>
            <td><strong>Загальна кількість переглядів файлів</strong></td>
            <td>{{ $totalViews }}</td>
        </tr>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
