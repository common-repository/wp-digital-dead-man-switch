<?php
$ddsm_page_default = get_option('ddsm_page_default');

?>
<form method="post" action="options.php">
    <?php settings_fields( 'ddsm-settings-group' ); ?>
    <?php do_settings_sections( 'ddsm-settings-group' ); ?>
    <table border="0">
        <tr>
            <td>Default page:</td>
            <td>
                <select name="ddsm_page_default">
                    <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option> 
                    <?php 
                    $pages = get_pages(Array(
                        'post_status' => 'draft'
                    )); 
                    foreach ( $pages as $page ) {
  	                $option = '<option value="' . $page->ID  . '"  '.($page->ID == $ddsm_page_default ? "selected='selected'" : "").'>';
	                $option .= $page->post_title;
	                $option .= '</option>';
	                echo $option;
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label>
                    <p><i>Draft page to be set as homepage after email #4.</i></p>
                </label>
                <?php  submit_button(); ?>                
            </td>
        </tr>        
    </table>
</form>
