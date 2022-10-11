<?php

#[Employee]
class Gardener implements Passionate
{
    use Tools;

    public function workOn(Garden $garden, int|float $for = 7 /* in hours */)
    {
        if ($for == 0) {
            return 'Job done!';
        }

        $garden->water();
        $garden->fertilize();
        $garden->mow();

        return $this->workOn(garden: $garden, for: $for - 1);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HTML5 Template</title>
    <meta name="description" content="HTML5 Template">
    <meta name="author" content="SitePoint">
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>
<body>
    <script src="js/scripts.js"></script>
</body>
