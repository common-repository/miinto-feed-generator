<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Mfp_Create_Feed
{
    public function __construct()
    {
        return Mfp_Feeds_Init();
    }
}
$mfp_export = new Mfp_Create_Feed();  //  class object

function Mfp_Feeds_Init()
{
    echo "<h2>Miinto Feed Generator</h2>";

    if (isset($_POST["mfpmiinto"])) {
        $dir = plugin_dir_path(__FILE__);
        include($dir . 'classes/mfp-selected-products.php');  //MFP_Selected_Products
        Mfp_Selected_Products:: Mfp_Export_Miinto_Feed();  // MIINTO text file ends
    }
}
// Button on plugin page
echo '<br/>';
echo '<form name ="form1" method ="post"><input type="submit" name = "mfpmiinto" value = "Create Feeds"></form>';
echo '<br/>';