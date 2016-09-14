<?php

use Elevator\Config;
use Elevator\Helper;
use Elevator\SystemState\SystemStateFactory;

error_reporting(E_ALL);
ini_set('display_errors', 1);
    
define('BASE_PATH', realpath(dirname(__FILE__).'/..'));

require __DIR__ . '/../vendor/autoload.php';

/*spl_autoload_register(function($class){
    $parts = explode('\\', $class);

    if (array_shift($parts) !== 'Elevator') {
        throw new Exception(sprintf('Unable to load class %s', $class));
    }

    $filename = BASE_PATH . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $parts) . '.php';

    require $filename;
});*/

$config = new Config;
$systemStateFactory = new SystemStateFactory();
$systemState = $systemStateFactory->create();
    
$helper = new Helper($config, $systemState);

?><!DOCTYPE html>
<html lang="en">
<head>
    <title>Elevator</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="main.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
    <!--
    <script src="jquery.min.js"></script>
    <script src="autobahn.js"></script>
    <script src="https://autobahn.s3.amazonaws.com/autobahnjs/latest/autobahn.js"></script>
    -->
    <script src="main.js"></script>
    <script>
        $(document).ready(function() {
            initButton.init();
            $('.elevator, .waypoints').addClass('disabled');
            client.loadState();
        });
    </script>
</head>
<body>

<?php echo $helper->renderInitButtons();?>
<?php echo $helper->renderElevators();?>

<?php echo $helper->renderWaypointsTable(); ?>
</body>
</html>
