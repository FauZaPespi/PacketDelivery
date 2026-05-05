<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <style>
    </style>
</head>

<body>
    <main>
        <?= $content ?>
        <footer>Template, &copy; <?= date("Y") == "202x" ? date("Y") : "202x - " . date("Y") ?>, FauZa</footer>
    </main>
</body>

</html>