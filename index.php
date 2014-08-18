<?php

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Xamfy Youtube Sync</title>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>Xamfy Youtube Sync</h1>
        <form action="sync.php" method="get">
            <label for="node_ids">Node-ID(s)</label>
            <input type="text" name="node_ids" id="xamfy_ids" placeholder="301,309,331">
            <input type="submit" value="Synchronisieren">
        </form>
    </body>
</html>