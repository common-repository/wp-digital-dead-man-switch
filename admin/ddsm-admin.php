<?php

$ddg_ddsm_users = get_option('tz_DDSM_users');// get users list


$tab_action = "mail";
$tab_first_selected = true;
if (isset($_GET['tab']) && !empty($_GET['tab']) && $_GET['tab']=="page" ) {
    $tab_action = $_GET['tab'];
    $tab_first_selected = false;
}

?>
<style type="text/css">
 .optiontable th{ text-align:left; }
 #wpfooter{position:relative;}
</style>

<div class="wrap">
    
    <h2><?php echo  __('Digital Deads Man Switch (DDSM) Options', 'DDSM')?></h2>
    <br />

    <div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
        
        <ul role="tablist" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
	    <li aria-expanded="true" aria-selected="true" aria-labelledby="ui-id-1" aria-controls="tabs-1" tabindex="0" role="tab" class="ui-state-default ui-corner-top <?php echo ($tab_first_selected===true?"ui-tabs-active ui-state-active":"");  ?>">
                <a id="ui-id-1" tabindex="-1" role="presentation" class="ui-tabs-anchor" href="<?php echo 'options-general.php?page=ddsm.php&tab=mail'; ?>">
                    Mail
                </a>
            </li>
	    <li aria-expanded="true" aria-selected="true" aria-labelledby="ui-id-2" aria-controls="tabs-2" tabindex="-1" role="tab" class="ui-state-default ui-corner-top <?php echo ($tab_first_selected===false?"ui-tabs-active ui-state-active":"");  ?>">
                <a id="ui-id-2" tabindex="-1" role="presentation" class="ui-tabs-anchor" href="options-general.php?page=ddsm.php&tab=page">
                    Page
                </a>
            </li>
        </ul>
        
        <div id="tabs-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false" style="display: block;">
            <?php
            $inc_file = dirname(__FILE__)."/tabs/tab-".$tab_action.".inc.php";
            if (!file_exists($inc_file)){
                $inc_file = dirname(__FILE__)."/tabs/tab-error.inc.php";
            }
            require_once($inc_file);
            
           
            ?>
        </div>
    </div> 

</div>
<div style="clear:both"></div>
