<?php
/**
 * Tab action for Mail options
 */


if (isset($_POST['ddg_ddsm_update']) || isset($_POST['ddg_ddsm_update_mails']) || isset($_POST['ddg_ddsm_reset'])) {
    echo ' <div class="updated"><p><strong>'._e('Options updated succesfully.', 'DDSM');        
    if ($_POST['ddg_ddsm_act'] == 'on') {  echo '<br />'.__('Plugin activated - Now go and edit your messages and options below!', 'DDSM');   }
    if (isset($_POST['ddg_ddsm_reset'])) { echo '<br /> '.__('Timestamps and sent mails status reset.', 'DDSM'); }
    echo '</strong></p></div>';
}

if (isset($_POST['ddg_ddsm_reset'])) {
    update_option('tz_DDSM_ll_'.$current_user->user_login, time());
    update_option('tz_DDSM_step1_'.$current_user->user_login, false);
    update_option('tz_DDSM_step2_'.$current_user->user_login, false);
    update_option('tz_DDSM_step3_'.$current_user->user_login, false);
}


if (isset($_POST['ddg_ddsm_update'])) {
    if ($_POST['ddg_ddsm_act'] == 'on') {
        // If plugin was just enabled (through options menu, not activated in plugins page)
        update_option('tz_DDSM_active_'.$current_user->user_login, true); //then set the "activated" option to true. this enables the rest of the menu, and the plugin functionality.
        $ddg_ddsm_users = get_option('tz_DDSM_users');
        if ( !is_array($ddg_ddsm_users) ) {
            $ddg_ddsm_users = array($current_user->user_login);
        }
        else {
            if (!in_array($current_user->user_login, $ddg_ddsm_users)) {
                $ddg_ddsm_users[] = $current_user->user_login;
            }
        }
    }
    else { 
        update_option('tz_DDSM_active_'.$current_user->user_login, false);
        $counter = 1;
        while ( $counter <= count($ddg_ddsm_users) ) {
            if ($ddg_ddsm_users[($counter - 1)] == $current_user->user_login) $ddg_ddsm_users[($counter - 1)] = ''; //remove from userlist on deactivation
            $counter = $counter + 1;
        }
    }
    update_option('tz_DDSM_users', $ddg_ddsm_users);
}



echo '<form method="post">';
echo '<p><label><input type="checkbox" name="ddg_ddsm_act"';
if (get_option('tz_DDSM_active_'.$current_user->user_login) == true) {
    // all options are user specific. will this work in MU?
    echo 'checked="checked"';
}
echo ' />'.__('Enable Digital Deads Man Switch (DDSM) plugin functionality', 'DDSM').'</label></p>';
if (get_option('tz_DDSM_active_'.$current_user->user_login) == true) {
    // Most of what comes next only loads if plugin is enabled for the logged in user.
    $options = get_option('tz_DDSM_options_'.$current_user->user_login); // load the options from DB
    if ( !is_array($options) )
        $options = array('email'=>$current_user->user_email, 'email_other'=>'', 'name'=>$current_user->display_name, 'interval1'=>'2', 'interval2'=>'1', 'interval3'=>'1'); // Default values. Getting self email from profile, but can be changed, also to multiple addresses.
    if (isset($_POST['tz_more'])) {
        // If hit "update options" button when seeing all options

        if ($_POST['ddg_ddsm_email'] == '') {
            $options['email'] = $current_user->user_email;
            //No blank self email allowed - will result in false deaths.
        }else{ 
            $options['email'] = strip_tags(stripslashes($_POST['ddg_ddsm_email']));
        }
        if ($_POST['ddg_ddsm_name'] == '') {
            $options['name'] = $current_user->display_name;
            //No blank name allowed - we want the recepient to know who's sending this
        }else { 
            $options['name'] = strip_tags(stripslashes($_POST['ddg_ddsm_name']));
        }
        $options['email_other'] = strip_tags(stripslashes($_POST['ddg_ddsm_email_other']));
        $options['interval1'] = strip_tags(stripslashes($_POST['ddg_ddsm_int1']));
        $options['interval2'] = strip_tags(stripslashes($_POST['ddg_ddsm_int2']));
        $options['interval3'] = strip_tags(stripslashes($_POST['ddg_ddsm_int3']));
        update_option('tz_DDSM_options_'.$current_user->user_login, $options); //write it all to DB
    }


    $optionsm = get_option('tz_DDSM_optionsm_'.$current_user->user_login); // load the messages from DB
    if ( !is_array($optionsm) ) //default values
        $optionsm = array('email1'=>__('This email is automatically sent to you because you did not login at your blog for two weeks. If you will not login within the next week, the system will assume the worst and will take further actions to send your preset details to the person(s) you chose to have those sent to.', 'DDSM')
                          , 'email2'=>__('The text here will be sent to you in case you have not logged in to the system after receiving the first email. On the same time the following email will be sent to whoever you chose to receive your personal details.', 'DDSM')
                          , 'email3'=>__('The text here will be sent to your chosen person(s) once you do not log in to the system after receiving the first notice. Write it carefully - you sadly might not be alive at this point.', 'DDSM')
                          , 'email4'=>__('This is the final text that will be sent to your chosen person(s) after you do not log in to the system for a month (by default). Here you should write passwords and logins to your blog, domain management panel, web management panel, secret bank account numbers, and whatever information you feel should be left after you have gone. Again, write this carefully! By this point, it is assumed you are deceased for several weeks.', 'DDSM')
                          , 'subject1'=>__('This is the email subject', 'DDSM')
                          , 'subject2'=>__('This is the email subject', 'DDSM')
                          , 'subject3'=>__('This is the email subject', 'DDSM')
                          , 'subject4'=>__('This is the email subject', 'DDSM')
                          );
    if (isset($_POST['ddg_ddsm_update_mails'])) {
        // If user hits Update Messages button
        $optionsm['email1'] = strip_tags(stripslashes($_POST['ddg_ddsm_msg1']));
        $optionsm['email2'] = strip_tags(stripslashes($_POST['ddg_ddsm_msg2']));
        $optionsm['email3'] = strip_tags(stripslashes($_POST['ddg_ddsm_msg3']));
        $optionsm['email4'] = strip_tags(stripslashes($_POST['ddg_ddsm_msg4']));
        $optionsm['subject1'] = strip_tags(stripslashes($_POST['ddg_ddsm_subj1']));
        $optionsm['subject2'] = strip_tags(stripslashes($_POST['ddg_ddsm_subj2']));
        $optionsm['subject3'] = strip_tags(stripslashes($_POST['ddg_ddsm_subj3']));
        $optionsm['subject4'] = strip_tags(stripslashes($_POST['ddg_ddsm_subj4']));

        update_option('tz_DDSM_optionsm_'.$current_user->user_login, $optionsm); // write them all to DB
    }
    //The html forms
    echo '<fieldset class="options">
<input type="hidden" name="tz_more" value="1" />
<table class="optiontable">
    <tbody>
        <tr valign="top">
            <th scope="row">'.__('Your Email(s): ', 'DDSM').' '.__('(seperate multiple addresses with commas)', 'DDSM').'</th>
            <td><input style="direction:ltr;" type="text" name="ddg_ddsm_email" value="' . $options['email'] . '" size="50"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">'.__('Email(s) to receive your electronic will: ', 'DDSM').' '.__('(seperate multiple addresses with commas)', 'DDSM').'</th>
            <td><input style="direction:ltr;" type="text" name="ddg_ddsm_email_other" value="' . $options['email_other'] . '" size="50"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">'.__('Your name: (will appear as sender)', 'DDSM').'</th>
            <td><input type="text" name="ddg_ddsm_name" value="' . $options['name'] . '" size="50"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">'.__('Interval 1: (time between last login into the system and first warning mail)', 'DDSM').'</th>
            <td><select name="ddg_ddsm_int1" size="1">';
$tz_int = 1;
while ($tz_int < 11) {
    echo '<option '.($options['interval1']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
    $tz_int++;
}
echo '</select> '.__('Weeks', 'DDSM').'</td>
        </tr>
        <tr>
            <th scope="row">'.__('Interval 2: (time between first warning mail to seconds warning mail)', 'DDSM').'</th>
            <td><select name="ddg_ddsm_int2" size="1">';
$tz_int = 1;
while ($tz_int < 11) {
    echo '<option '.($options['interval2']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
    $tz_int++;
}
echo '</select> '.__('Weeks', 'DDSM').'
            </td>
        </tr>
        <tr>
            <th scope="row">'.__('Interval 3: (time between the second warning mail to the moment the system assumes you are no longer alive or functioning', 'DDSM').'</th>
            <td><select name="ddg_ddsm_int3" size="1">';
$tz_int = 1;
while ($tz_int < 11) {
    echo '<option '.($options['interval3']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
    $tz_int++;
}
echo '</select> '.__('Weeks', 'DDSM').' </td></tr></tbody></table><p></p></fieldset>';
}

echo '<div class="submit">';
echo '<input type="submit" name="ddg_ddsm_update" value="'._e('Update Options', 'DDSM').' &raquo;" />';
echo '</div>';
echo '</form>';


if (get_option('tz_DDSM_active_'.$current_user->user_login) == true) {
    
    echo '<div class="wrap"><h2>'.__('Write your informative emails here <small>(blank emails will not be sent)</small>', 'DDSM').'</h2><form method="post">';
    echo '<h3>'.sprintf(__('Message 1: <small>(will be sent to you after %s weeks of inactivity)</small>', 'DDSM'), $options['interval1']).'</h3><br />';
    echo '<input type="text" name="ddg_ddsm_subj1" value="' . $optionsm['subject1'] . '" size="50"/>';
    echo '<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="ddg_ddsm_msg1" >'.$optionsm['email1'].'</textarea>';
    echo '<h3>'.sprintf(__('Message 2: <small>(will be sent to you after %s more weeks of inactivity)</small>', 'DDSM'), $options['interval2']).'</h3><br />';
    echo '<input type="text" name="ddg_ddsm_subj2" value="' . $optionsm['subject2'] . '" size="50"/>';
    echo '<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="ddg_ddsm_msg2" >'.$optionsm['email2'].'</textarea>';
    echo '<h3>'.__('Message 3: <small>(will be sent to the email address(es) you chose to receive your details, at the same time as message 2)</small>', 'DDSM').'</h3><br />';
    echo '<input type="text" name="ddg_ddsm_subj3" value="' . $optionsm['subject3'] . '" size="50"/>';
    echo '<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="ddg_ddsm_msg3" >'.$optionsm['email3'].'</textarea>';
    echo '<h3>'.sprintf(__('Message 4: <small>(will be sent to your chosen email address(es) after %s more weeks of inactivity)</small>', 'DDSM'), $options['interval3']).'</h3><br />';
    echo '<input type="text" name="ddg_ddsm_subj4" value="' . $optionsm['subject4'] . '" size="50"/>';
    echo '<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="ddg_ddsm_msg4" >'.$optionsm['email4'].'</textarea>';
    echo '<br />';
    echo '<div class="submit">';
    echo '<input type="submit" name="ddg_ddsm_update_mails" value="'.__('Update Messages', 'DDSM').' &raquo;" /></div>';
    echo '</form></div>';
    echo '<div class="wrap">';
    echo '<form method="post">';
    echo '<div class="submit">';
    echo '<p>'.__('This button will reset last login timestamps and sent mail status. Use it in case you got the first warning email.', 'DDSM').'</p>';
    echo '<input type="submit" name="ddg_ddsm_reset" value="'.__('Reset', 'DDSM').' &raquo;" /></div>';
    echo '</form></div>';
    
}
echo '</div>';

?>
