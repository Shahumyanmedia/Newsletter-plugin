<div class="sh_newsletter">
<!--    <div class="allUnActive">-->
<!--        <div class="progressTop">-->
<!--            <img alt="progress register" src="--><?php //echo plugins_url();?><!--/sh-newsletter/sources/img/progress_bar.gif" />-->
<!--        </div>-->
<!--    </div>-->
    <div class="error">Database error</div>
    <h1>SH Newsletter Send Mail</h1>
    <div class="EmailBox">
        <?php
        global $wpdb;
        $base = $wpdb->prefix.'sh_newsletter';
        $user = $wpdb->get_results(
            "
                    SELECT *
                    FROM $base
                    "
        );
        echo
        '<h2 class="leftTitle">Registered persons list</h2>
        <table>
            <tbody>
                <tr class="top">
                    <td width="11.7%">
                    <input class="checking" name="select" type="checkbox" value="0" id="checkAll" >ID
                    </td>
                    <td width="41.2%">Name</td>
                    <td>Email</td>
                </tr>
            </tbody>
        </table>
        <div class="content">
            <table>
                <tbody>
                    ';
        foreach($user as $us){
            $adID = $us -> id;
            $adName = $us -> name;
            $adEmail = $us -> email;

            $screen = '';
            $screen .= '<tr class="standard">';
            $screen .= '<td width="10%">';
            $screen .= '<input class="checking" name="select" type="checkbox" value="0" id="'.$adID.'" >';
            $screen .= $adID;
            $screen .= '</td>';
            $screen .= '<td width="40%" class="name">';
            $screen .= $adName;
            $screen .= '</td>';
            $screen .= '<td style="border-right: 0" width="30%" class="email">';
            $screen .= $adEmail;
            $screen .= '</td>';
            $screen .= '</tr>';
            echo  $screen;
        }
        echo ' </tbody>
            </table>
        </div>';
        echo '<input type="button" class="addEmail button button-secondary" value="Add to receivers" disabled="disabled">';
        echo '<input type="button" class="delete button button-secondary" value="Delete" disabled="disabled">';

        ?>
        <p class="helpText"><i>Select members you want to send E-mail and press “Add to receivers” button.</i></p>
    </div>

    <div class="EmailSendBox">
        <h2 class="rightTitle">New Mail</h2>
        <i>Select members you want to send E-mail, write subject and your message, add attached file and press send button</i>
        <div class="sendMails">
            <div class="MailsHeight"></div>
        </div>
        <div class="content">
            <input placeholder="Subject" type="text" id="subject"/>
            <textarea id="recommend" rows="10" cols="45">

            </textarea>
            <form enctype="multipart/form-data" action="" method="post" >
                <div class="leftBot">
                    <!-- Upload Button-->
                    <div id="upload" class="button button-secondary">Attach file</div>
                    <!--List Files-->
                    <ul id="files" >
                        <li>Attached Files</li>
                    </ul>
                </div>
                <div class="rightBot">
                    <input data-path="<?php echo(SH_NEWSLETTER_DIR . 'uploads/') ?>" value="Send" id="SendMail" class="button button-primary" type="button">
                </div>
                <div id="status" >
                    <div class="progress">
                        <img alt="progress register" src="<?php echo plugins_url();?>/sh-newsletter/sources/img/progress_bar.gif" />
                    </div>
                    <p class="helpText"><i></i></p>
                </div>
            </form>
            <div class="answer"></div>
        </div>
    </div>

    <?php
    $uploaddir = SH_NEWSLETTER_DIR.'uploads/';
    $file = $uploaddir . basename($_FILES['uploadfile']['name']);
    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
        echo "success";
    }
    ?>
</div>