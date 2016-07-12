<?php

class SH_new{

    public function __construct(){
        // Ajax for Register
        add_action('wp_ajax_nopriv_register', [$this, 'register']);
        add_action('wp_ajax_register', [$this, 'register']);
        // Ajax for mail sender
        add_action('wp_ajax_nopriv_sendMail', [$this, 'sendMail']);
        add_action('wp_ajax_sendMail', [$this, 'sendMail']);
        // Ajax for admin where user registered
        add_action('wp_ajax_nopriv_userAdminMail', [$this, 'userAdminMail']);
        add_action('wp_ajax_userAdminMail', [$this, 'userAdminMail']);
        // Ajax for deleting subscriber
        add_action('wp_ajax_nopriv_delete_mail_person', [$this, 'delete_mail_person']);
        add_action('wp_ajax_delete_mail_person', [$this, 'delete_mail_person']);
        // Ajax for user unsubscribe
        add_action('wp_ajax_nopriv_unsubscribe', [$this, 'unsubscribe']);
        add_action('wp_ajax_unsubscribe', [$this, 'unsubscribe']);

        global $wpdb;
        $this->db = $wpdb;
        $this->newsletter_db_version = '1.0';
        $this->base = $this->db->prefix.'sh_newsletter_settings';
        $this->table_name = $this->db->prefix . "sh_newsletter";
    }

    function Template(){
        $template = $this->db->get_results(
            "
            SELECT *
            FROM $this->base
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
        return [
            'emailTitle' => $emailTitle,
            'TitleColor' => $TitleColor,
            'BorderColor' => $BorderColor,
            'textColor' => $textColor,
            'BackgroundColor' => $BackgroundColor,
            'Signature' => $Signature,
            'SignatureColor' => $SignatureColor,
            'logo' => $logo,
        ];
    }

    /**
     * Plugin activation
     */
    public function sh_newsletter_activation(){
        if($this->db->get_var("show tables like '$this->table_name'") != $this->table_name) {

            $sql = "CREATE TABLE " . $this->table_name . " (
                          id mediumint(9) NOT NULL AUTO_INCREMENT,
                          time date NOT NULL,
                          name tinytext NOT NULL,
                          email text NOT NULL,
                          keygen text NOT NULL,
                          UNIQUE KEY id (id)
                        );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            $name = "Mr. Test";
            $email = "example@gmail.com";
            $this->db->insert( $this->table_name, array( 'time' => current_time('mysql'), 'name' => $name, 'email' => $email ) );

            add_option("newsletter_db_version", $this->newsletter_db_version);

        }

        if($this->db->get_var("show tables like '$this->base'") != $this->base) {
            $sql2 = "CREATE TABLE " . $this->base . " (
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
            $this->db->insert($this->base, array('emailTitle' => get_option( 'blogname' ), 'textColor' => '#23282d', 'BackgroundColor' => 'white','Signature'=>'This is Your Signature', 'BorderColor'=>'#23282d','TitleColor'=>'#23282d' ));
        }
    }

    /**
     * Plugin Deactivation
     */
    public function sh_newsletter_deactivation() {

    }

    /**
     * User Subscribed Mail Register
     */
    public function register(){
        $symbol = ['0','1','2','3','4','5','6','7','8','9','q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9'];
        $lic = '';
        for ($i=0; $i<32; $i++):
            $rand_symbol = $symbol[rand(0,45)];
            $lic = $lic.$rand_symbol;
        endfor;
        if($this->db->get_var("show tables like '$this->table_name'") == $this->table_name) {
            if($_POST['name'] && $_POST['name'] != '')
            {
                $name = $_POST['name'];
                if($_POST['email'] && $_POST['email'] != '')
                {
                    $email = $_POST['email'];
                    $user = $this->db->get_results(
                        "
                    SELECT *
                    FROM $this->table_name
                    "
                    );
                    $base_email = '';
                    foreach($user as $us){
                        $base_email .= $us -> email;
                    }
                    if(strpos($base_email, $email) == false && strpos($base_email, $email) !== 0) {
                        $this->db->insert($this->table_name, array('time' => current_time('Y-m-d'), 'name' => $name, 'email' => $email, 'keygen' => $lic));
                        echo 'Thank you for subscribing';
                    }
                    else
                        echo '<span style="color:red">Your Email was subscribed</span>';
                    exit;
                }
            }
        }
    }

    /**
     *
     */
    public function sendMail(){
        function processText($text) {
            $text = strip_tags($text);
            $text = trim($text);
            $text = htmlspecialchars($text);
            return $text;
        }
        //Use template params
        $template = $this->Template();

        $imgArr = $_POST['images'];
        $attachments = [];
        foreach($imgArr as $img)
        {
            $attachments[].=SH_NEWSLETTER_DIR . 'uploads/'.$img;
        }

        $subject = $_POST['sub'];

        $from = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';

        $tos = $_POST['mails'];
        add_filter( 'wp_mail_content_type', 'set_content_type' );
        function set_content_type( $message ) {
            return 'text/html';
        }
        foreach($tos as $to ) {
            $user_now = $this->db->get_results(
                "
            SELECT *
            FROM $this->table_name WHERE email = '".$to."'
            "
            );
            $header = "From: ".$from."\r\n";
            $header .= "Reply-To: ".$from."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            foreach($user_now as $keys){
                $keygen = $keys -> keygen;
                $message = '';
                $message.= '<div style="background-color: '.$template['BackgroundColor'].'; border-color: '.$template['BorderColor'].';padding: 15px;border-width: 2px;border-style: solid;width:800px;margin:auto">';
                $message .= '<div style="margin-bottom: 20px;display: table;width: 100%">';
                $message .= '<div style="width: 200px;max-width: 300px; float: left;">';
                $message .= '<img style="width:100%;height:auto" src="'.plugins_url().'/sh-newsletter/uploads/'.$template['logo'].'"/>';
                $message .= '</div>';
                $message .= '<h1 style="color:'.$template['TitleColor'].';margin:0;margin-top: 63px; padding-left: 250px; ">'.$template['emailTitle'].'</h1>';
                $message .= '</div>';
                $message .= '<div style="padding: 15px;color:'.$template['textColor'].'">';
                $message .= nl2br($_POST['body']);
                $message .= '</div>';
                $message .= '<div style="padding:30px 15px;color:'.$template['SignatureColor'].'">';
                $message .= $template['Signature'];
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

    /**
     *
     */
    public function userAdminMail() {

        $name = $_POST['name'];
        $email = $_POST['email'];
        $user_now = $this->db->get_results(
            "
            SELECT *
            FROM $this->table_name WHERE email = '".$email."'
            "
        );


        foreach($user_now as $info){
            $register_date = $info->time;
            $keygen = $info->keygen;
            $userID = $info->id;
        };
        if($register_date == current_time('Y-m-d')){

            //Use template params
            $template = $this->Template();
            $from = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
            $header = "From: ".$from."\r\n";
            $header .= "Reply-To: ".$from."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $subject = 'Welcome '.get_option( 'blogname' ) ;

            add_filter( 'wp_mail_content_type', 'set_content_type' );
            function set_content_type( $message ) {
                return 'text/html';
            }
            $message = '';
            $message.= '<div style="background-color: '.$template['BackgroundColor'].'; border-color: '.$template['BorderColor'].';padding: 15px;border-width: 2px;border-style: solid;width:800px;margin:auto">';
            $message .= '<div style="margin-bottom: 20px;display: table;width: 100%">';
            $message .= '<div style="width: 200px;max-width: 300px; float: left;">';
            $message .= '<img style="width:100%;height:auto" src="'.plugins_url().'/sh-newsletter/uploads/'.$template['logo'].'"/>';
            $message .= '</div>';
            $message .= '<h1 style="color:'.$template['TitleColor'].';margin:0;margin-top: 63px; padding-left: 250px; ">'.$template['emailTitle'].'</h1>';
            $message .= '</div>';
            $message .= '<div style="padding:30px 15px;color:'.$template['SignatureColor'].'">';
            $message .= $template['Signature'];
            $message .= '</div>';
            $message .= '<div>';
            $message .= '<p>';
            $message .= '<a style="color: #909090;text-decoration: none;padding: 20px;" href="'.get_home_url().'/?key='.$keygen.'">Unsubscribe</a>';
            $message .= '</p>';
            $message .= '</div>';
            $message.= '</div>';
            wp_mail($email, $subject, $message, $header);
            /*Send mail admin*/
            $subjectAdmin = 'New Subscriber';
            $messageAdmin ='';
            $messageAdmin.= '<div style="background-color: '.$template['BackgroundColor'].'; border-color: '.$template['BorderColor'].';padding: 15px;border-width: 2px;border-style: solid;width:800px;margin:auto">';
            $messageAdmin .= '<div style="margin-bottom: 20px;display: table;width: 100%">';
            $messageAdmin .= '<div style="width: 200px;max-width: 300px; float: left;">';
            $messageAdmin .= '<img style="width:100%;height:auto" src="'.plugins_url().'/sh-newsletter/uploads/'.$template['logo'].'"/>';
            $messageAdmin .= '</div>';
            $messageAdmin .= '<h1 style="color:'.$template['TitleColor'].';margin:0;margin-top: 63px; padding-left: 250px; ">'.$template['emailTitle'].'</h1>';
            $messageAdmin .= '</div>';
            $messageAdmin .= '<div style="padding: 15px;color:'.$template['textColor'].'">';
            $messageAdmin .= '<h2 style="color: ' . $template['textColor']. '">You have a new subscriber</h2>';
            $messageAdmin .= '<p style="color: ' . $template['textColor']. '">Name - '.$name.'</p>';
            $messageAdmin .= '<p style="color: ' . $template['textColor ']. '">Email - <span style="color: ' . $template['textColor ']. ';">'.$email.'</span></p>';
            $messageAdmin .= '</div>';
            $messageAdmin .= '<div>';
            $messageAdmin .= '<p>';
            $messageAdmin .= '<a style="color: #909090;text-decoration: none;padding: 20px;" href="'.get_home_url().'/wp-admin/admin.php?page=sh_newsletter/">Admin Panel</a>';
            $messageAdmin .= '</p>';
            $messageAdmin .= '</div>';
            $messageAdmin.= '</div>';
            $toAdmin = get_bloginfo('admin_email');
            wp_mail($toAdmin, $subjectAdmin, $messageAdmin, $header);
        }

        exit;
    }
    public function delete_mail_person() {

        $ids = $_POST['id'];

        foreach($ids as $id )
        {
            $this->db->query(
                $this->db->prepare(
                    "DELETE FROM $this->table_name WHERE id= %d", array(
                        $id
                    )
                ));
        }

        exit;
    }
    public function unsubscribe(){
        $userKey = $_POST['key'];

        $this->db->query(
            $this->db->prepare(
                "DELETE FROM $this->table_name WHERE keygen = '".$userKey."'"
            ));
        echo 'You cancelled a subscription to our site';

        exit;
    }

}
new SH_new;
