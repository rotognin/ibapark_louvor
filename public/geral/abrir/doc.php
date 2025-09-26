<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivo Anexo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        iframe {
            border: none;
            width: 100%;
            height: 100vh;
        }
    </style>
</head>

<body>
    <<?php echo $_GET['tag']; ?> src="/geral/documento/readfile.php?file=<?php echo urlencode($_GET['arquivo']); ?>"></<?php echo $_GET['tag']; ?>>
</body>

</html>