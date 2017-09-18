<?php
require 'loadTest.php';
$results = doTest($_GET['test']);
$title = getTitle($_GET['test']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Skiddle SDK test</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <link href="styles/dist/styles.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<header class="container">
    <h1>Skiddle SDK Tester&trade;</h1>
</header>
<nav class="container">
    <ul class="listless">
        <li><a href="index.php">Home</a></li>
        <?php if ($_GET['test'] != 1): ?>
            <li><a href="result.php?test=<?php echo $_GET['test'] - 1; ?>">Previous test</a></li>
        <?php endif; ?>
        <?php if ($_GET['test'] != 8): ?>
            <li><a href="result.php?test=<?php echo $_GET['test'] + 1; ?>">Next test</a></li>
        <?php endif; ?>
    </ul>
</nav>
<main class="container">

    <?php if($debugInfo): ?>
        <div role="debug-info">
            <p>Debug info:</p>
            <?php echo $debugInfo; ?>
        </div>
    <?php endif; ?>

    <h3><?php echo $title; ?></h3>

    <?php if (!is_object($results) || !isset($results->results)): ?>
        <div role="error-info">
            <p><?php echo $results; ?></p>
        </div>
    <?php else: ?>
        <ul class="resultlist listless">
            <?php if($_GET['test'] == 7):
                //test 7 is a single listing
                $result = $results->results;
            ?>
                <li>
                    <img src="<?php echo $result->largeimageurl; ?>"/>
                    <div>
                        <h3><?php echo $result->eventname; ?></h3>
                        <p class="sub">
                            <time datetime="<?php echo $result->date; ?>"><?php echo date('l, jS F Y', strtotime($result->date)); ?></time>
                            | <?php echo $result->venue->name; ?>, <?php echo $result->venue->town; ?>  </p>
                        <p><?php echo $result->description; ?></p>
                        <p><a href="<?php echo $result->link; ?>" target="_blank">Find out more</a></p>
                    </div>
                </li>
            <?php else: ?>
            <?php foreach ($results->results as $result): ?>
                <li>
                    <img src="<?php echo $result->largeimageurl; ?>"/>
                    <div>
                        <h3><?php echo $result->eventname; ?></h3>
                        <p class="sub">
                            <time datetime="<?php echo $result->date; ?>"><?php echo date('l, jS F Y', strtotime($result->date)); ?></time>
                            | <?php echo $result->venue->name; ?>, <?php echo $result->venue->town; ?>  </p>
                        <p><?php echo $result->description; ?></p>
                        <p><a href="<?php echo $result->link; ?>" target="_blank">Find out more</a></p>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</main>

<footer class="container">
    <p>Made with <span class="red">♥</span> by <a href="https://www.skiddle.com">Skiddle</a></p>
</footer>

</body>
</html>