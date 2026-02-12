<?php
if (!defined('ROOT_DIR')) { exit ("Error, wrong way to file.<a href=\"/\">Go to main</a>."); }

$arr_error = [];

if (version_compare(PHP_VERSION, REQUIREMENTS['MIN_PHP'], '<')) {
    $arr_error[] = "PHP >= " . REQUIREMENTS['MIN_PHP'] . " требуется, обнаружено " . PHP_VERSION;
}

$sapi = php_sapi_name();
if (!in_array($sapi, REQUIREMENTS['ALLOW_SAPI'], true)) {
    $arr_error[] = "Unsupported PHP SAPI: " . $sapi . " Allowed: " . implode(', ', REQUIREMENTS['ALLOW_SAPI']);
}

$server = $_SERVER['SERVER_SOFTWARE'] ?? '';
if (stripos($server, 'Apache') !== false) {
    if (preg_match('~Apache/([\d.]+)~i', $server, $m)) {
        $apacheVersion = $m[1];
        if (version_compare($apacheVersion, REQUIREMENTS['MIN_APACHE'], '<')) {
            $arr_error[] = "Apache version must be minimum " . REQUIREMENTS['MIN_APACHE'] . ", detected: " . $apacheVersion;
        }
    } else {
        $arr_error[] = "Unable to detect Apache version from SERVER_SOFTWARE: " . $server;
    }
}

if (count($arr_error) > 0){
    $exist_error = true;
?>
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-15" role="alert">
        <div class="flex-fill mr-10">
            <p class="mb-0">Your system configuration is not compatible with this engine. Please update your environment to meet the minimum requirements before continuing.</p>
        </div>
        <div class="flex-00-auto">
            <i class="fa fa-fw fa-2x fa-exclamation-triangle"></i>
        </div>
    </div>
    <ul class="list-group push">
    <?php
    foreach ($arr_error as $err){
        echo '<li class="list-group-item  justify-content-between align-items-center font-w600">
        '.$err.'
    </li>';
    }
?>
    </ul>
    <hr>
<?php
}
unset($arr_error);
?>