<?php

if (! defined ( 'ROOT_DIR' )) {
    exit ( "Error, wrong way to file.<a href=\"/\">Go to main</a>.");
}

$search = array(
    "%API_KEY%",
    '%DB_HOST%',
    '%DB_NAME%',
    '%DB_USER%',
    '%DB_PASSWORD%',
    '%ADMIN_LOGIN%',
    '%ADMIN_PASSWORD%',
);
$replace = array(
    $_POST['API_KEY'],
    $_POST['DB_HOST'],
    $_POST['DB_NAME'],
    $_POST['DB_USER'],
    $_POST['DB_PASSWORD'],
    $_POST['ADMIN_LOGIN'],
    $_POST['ADMIN_PASSWORD'],
);


if (file_exists(ROOT_DIR.'/Config.php.example')) {

    include(ROOT_DIR . '/Helpers/function_helper.php');

    $configContent = file_get_contents(ROOT_DIR . '/Config.php.example');
    $configContent = str_replace($search, $replace, $configContent);

    try {

        $configExamplesDir = ROOT_DIR . '/Library/configs/examples';
        $configsDir = ROOT_DIR . '/Library/configs';

        $files = glob($configExamplesDir . '/*.json');

        foreach ($files as $file) {
            $filename = basename($file);
            $target = $configsDir . '/' . $filename;

            if (!copy($file, $target)) {
                throw new Exception("Failed to copy " . $filename . " to configs folder.");
            }

            if (!chmod($target, 0640)) {
                throw new Exception("Failed to set permissions for " . $filename);
            }

        }

        atomic_write(ROOT_DIR . '/Config.php', $configContent);
        echo ('
            <div class="alert alert-success d-flex align-items-center justify-content-between mb-15" role="alert">
                <div class="flex-fill mr-10">
                    <p class="mb-0">Config.php created!</p>
                </div>
                <div class="flex-00-auto">
                    <i class="fa fa-fw fa-2x fa-check"></i>
                </div>
            </div>
        ');

    } catch(Exception $e) {
        $write_config = false;
        echo '
            <div class="alert alert-warning d-flex align-items-center justify-content-between mb-15" role="alert">
                <div class="flex-fill mr-10">
                    <p class="mb-0">' . $e->getMessage() . '</p>
                </div>
                <div class="flex-00-auto">
                    <i class="fa fa-fw fa-2x fa-pencil-square-o"></i>
                </div>
            </div>
        ';
    }

} else {
$write_config = false;
    ?>
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-15" role="alert">
        <div class="flex-fill mr-10">
            <p class="mb-0">Config.php.example file was not found</p>
        </div>
        <div class="flex-00-auto">
            <i class="fa fa-fw fa-2x fa-neuter"></i>
        </div>
    </div>
<?php
}
?>