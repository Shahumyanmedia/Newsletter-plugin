<?php
/*create database in activation*/
function sh_newsletter_activation() {

    global $newsletter_db_version;
    $newsletter_db_version = "1.0";

    global $wpdb;
    global $newsletter_db_version;

    $table_name = $wpdb->prefix . "sh_newsletter";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
                          id mediumint(9) NOT NULL AUTO_INCREMENT,
                          time bigint(11) DEFAULT '0' NOT NULL,
                          name tinytext NOT NULL,
                          email text NOT NULL,
                          keygen text NOT NULL,
                          UNIQUE KEY id (id)
                        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $name = "Mr. Test";
        $email = "example@gmail.com";
        $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $name, 'email' => $email ) );

        add_option("newsletter_db_version", $newsletter_db_version);

    }

    $table_name_two = $wpdb->prefix . "sh_newsletter_settings";
    if($wpdb->get_var("show tables like '$table_name_two'") != $table_name_two) {
        $sql2 = "CREATE TABLE " . $table_name_two . " (
                      id mediumint(9) NOT NULL AUTO_INCREMENT,
                      emailTitle text NOT NULL,
                      TitleColor text NOT NULL,
                      BorderColor text NOT NULL,
                      textColor text NOT NULL,
                      BackgroundColor text NOT NULL,
                      LogoMAil text NOT NULL,
                      Signature text NOT NULL,
                      SignatureColor text NOT NULL,
                      UNIQUE KEY id (id)
                    );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql2);
        $wpdb->insert($table_name_two, array('emailTitle' => get_option( 'blogname' ), 'textColor' => '#23282d', 'BackgroundColor' => 'white','Signature'=>'This is Your Signature', 'BorderColor'=>'#23282d','TitleColor'=>'#23282d' ));
    }

}

function sh_newsletter_deactivation() {

}
//register subscribed email
function register(){
    global $wpdb;
    $symbol=array('0','1','2','3','4','5','6','7','8','9','q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9');
    for ($i=0; $i<32; $i++):
        $rand_symbol=$symbol[rand(0,45)];
        $lic=$lic.$rand_symbol;
    endfor;
    $table_name = $wpdb->prefix . "sh_newsletter";
    if($wpdb->get_var("show tables like '$table_name'") == $table_name) {
        if($_POST['name'] && $_POST['name'] != '')
        {
            $name = $_POST['name'];
            if($_POST['email'] && $_POST['email'] != '')
            {
                $email = $_POST['email'];
                $user = $wpdb->get_results(
                    "
                    SELECT *
                    FROM $table_name
                    "
                );
                $base_email = '';
                foreach($user as $us){
                    $base_email .= $us -> email;
                }
                if(strpos($base_email, $email) == false && strpos($base_email, $email) !== 0) {
                    $rows_affected = $wpdb->insert($table_name, array('time' => current_time('mysql'), 'name' => $name, 'email' => $email, 'keygen' => $lic));
                    echo 'Thank you for subscribing';
                }
                else
                    echo '<span style="color:red">Your Email was subscribed</span>';
                exit;
            }
        }
    }
}
add_action('wp_ajax_nopriv_register', 'register');
add_action('wp_ajax_register', 'register');

function sendMail(){
    function processText($text) {
        $text = strip_tags($text);
        $text = trim($text);
        $text = htmlspecialchars($text);
        return $text;
    }
    global $wpdb;
    $base = $wpdb->prefix.'sh_newsletter_settings';
    $template = $wpdb->get_results(
        "
            SELECT *
            FROM $base
            "
    );
    foreach($template as $set) {
        if($set->emailTitle)
            $emailTitle = $set->emailTitle;
        else
            $emailTitle = get_bloginfo();
        if($set->TitleColor)
            $TitleColor = $set->TitleColor;
        else
            $TitleColor = '#23282d';
        if($set->BorderColor)
            $BorderColor = $set->BorderColor;
        else
            $BorderColor = '#23282d';
        if($set->textColor)
            $textColor = $set->textColor;
        else
            $textColor = '#23282d';
        if($set->BackgroundColor)
            $BackgroundColor = $set->BackgroundColor;
        else
            $BackgroundColor = 'white';
        if($set->Signature)
            $Signature = $set->Signature;
        else
            $Signature = 'This is Signature';
        if($set->SignatureColor)
            $SignatureColor = $set->SignatureColor;
        else
            $SignatureColor = '#23282d';
        if($set->LogoMAil)
            $logo = $set->LogoMAil;
        else
            $logo = get_bloginfo();
    }


    /*Send step*/

    $imgArr = $_POST['images'];
    $attachments = [];
    foreach($imgArr as $img)
    {
        $attachments[].=SH_NEWSLETTER_DIR . 'uploads/'.$img;
    }

    $subject = $_POST['sub'];

    $from = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';

    $tos = $_POST['mails'];
    $table_name_now = $wpdb->prefix . "sh_newsletter";
    add_filter( 'wp_mail_content_type', 'set_content_type' );
    function set_content_type( $message ) {
        return 'text/html';
    }
    foreach($tos as $to ) {
        $user_now = $wpdb->get_results(
            "
            SELECT *
            FROM $table_name_now WHERE email = '".$to."'
            "
        );
        $header = "From: ".$from."\r\n";
        $header .= "Reply-To: ".$from."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        if($img)
        {
            $file_type = array(".bmp", ".gif", ".jpg", "jpeg", ".png");
            $content = chunk_split(base64_encode(file_get_contents($attachments)));
            $uid = md5(uniqid(time()));
            $header .= "--".$uid."\r\n";
            $header .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8". "\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
            $header .= "This is a multi-part message in MIME format. \r\n";
            $header .= "--".$uid."\r\n";
            $header .= "Content-Type: ".$file_type."; name=\"".$img."\"\r\n";
            $header .= "Content-Transfer-Encoding: base64\r\n";
            $header .= "Content-Disposition: attachment; filename=\"".$img."\"\r\n";
            $header .= $content."\r\n";

        }else{
            $header .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";
        }
        foreach($user_now as $keys){
            $keygen = $keys -> keygen;





            $message = '';
            $message.= '<div style="background-color: '.$BackgroundColor.'; border-color: '.$BorderColor.';padding: 15px;border-width: 2px;border-style: solid;width:800px;margin:auto">';
            $message .= '<div style="margin-bottom: 20px;display: table;width: 100%">';
            $message .= '<div style="width: 200px;max-width: 300px; float: left;">';
            $message .= '<img style="width:100%;height:auto" src="'.plugins_url().'/sh-newsletter/uploads/'.$logo.'"/>';
            $message .= '</div>';
            $message .= '<h1 style="color:'.$TitleColor.';margin:0;margin-top: 63px; padding-left: 250px; ">'.$emailTitle.'</h1>';
            $message .= '</div>';
            $message .= '<div style="padding: 15px;color:'.$textColor.'">';
            $message .= nl2br($_POST['body']);
            $message .= '</div>';
            $message .= '<div style="padding:30px 15px;color:'.$SignatureColor.'">';
            $message .= $Signature;
            $message .= '</div>';
            $message .= '<div>';
            $message .= '<p>';
            $message .= '<a style="color: #909090;text-decoration: none;padding: 20px;" href="'.get_home_url().'/?key='.$keygen.'">Unsubscribe</a>';
            $message .= '</p>';
            $message .= '</div>';
            $message.= '</div>';

            $base_email = '';
            $base_email .= $keys -> email;
            if(strpos($base_email, $to) !== false) {
                wp_mail($to, $subject, $message, $header, $attachments);
            }else
            {
                echo('<p>'.$to.' isn`t correct email!</p>');
                $wrong = true;
            }
        }
    }

    if(!$wrong) {
        echo('<p style="color:green">Your message has been sent.</p>');
    }
    exit;

}

add_action('wp_ajax_nopriv_sendMail', 'sendMail');
add_action('wp_ajax_sendMail', 'sendMail');


function delete_mail_person() {

    global $wpdb;
    $ids = $_POST['id'];
    $table_name = $wpdb->prefix . "sh_newsletter";

    foreach($ids as $id )
    {
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE id= %d", array(
                    $id
                )
            ));
    }

    exit;
}

add_action('wp_ajax_nopriv_delete_mail_person', 'delete_mail_person');
add_action('wp_ajax_delete_mail_person', 'delete_mail_person');


function unsubscribe(){
    global $wpdb;
    $userKey = $_POST['key'];
        $table_name = $wpdb->prefix . "sh_newsletter";

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM $table_name WHERE keygen = '".$userKey."'"
                ));
    echo 'You cancelled a subscription to our site';

    exit;
}
add_action('wp_ajax_nopriv_unsubscribe', 'unsubscribe');
add_action('wp_ajax_unsubscribe', 'unsubscribe');

?>