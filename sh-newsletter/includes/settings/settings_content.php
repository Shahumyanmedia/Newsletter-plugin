<?php
global $wpdb;
$base = $wpdb->prefix.'sh_newsletter_settings';

if($_POST['submit']){


    $upEmTitle = $_POST['emailTitle'];
    $upTitleColor = $_POST['TitleColor'];
    $upBorderColor = $_POST['BorderColor'];
    $uptextCol = $_POST['textColor'];
    $upBgCol = $_POST['BackgroundColor'];
    $upSignatureColor = $_POST['SignatureColor'];
    if($_POST['SignatureColor'] || $_FILES['LogoMAil']['name']) {
        if ($_FILES['LogoMAil']['name']) {
            $upLogo = $_FILES['LogoMAil']['name'];
        } else {
            $template = $wpdb->get_results(
                "
                SELECT *
                FROM $base
                "
            );
            foreach ($template as $set) {
                if ($set->LogoMAil)
                    $upLogo = $set->LogoMAil;
            }
        }
        if ($_POST['Signature']) {
            $upSignature = $_POST['Signature'];
        } else {
            $template = $wpdb->get_results(
                "
                SELECT *
                FROM $base
                "
            );
            foreach ($template as $set) {
                if ($set->Signature)
                    $upSignature = $set->Signature;
            }
        }
    }




    $ert = $wpdb->update(
        $base,
        array(
            'emailTitle' => $upEmTitle,
            'TitleColor' => $upTitleColor,
            'BorderColor' => $upBorderColor,
            'textColor' => $uptextCol,
            'BackgroundColor' => $upBgCol,
            'LogoMAil' => $upLogo,
            'Signature' => $upSignature,
            'SignatureColor' => $upSignatureColor
        ),
        array(
            'id' =>1
        )
    );
    $uploaddir = SH_NEWSLETTER_DIR.'uploads/';
    $uploadfile = $uploaddir . basename($_FILES['LogoMAil']['name']);
    if($_FILES['LogoMAil']){
        move_uploaded_file($_FILES['LogoMAil']['tmp_name'], $uploadfile);
    }
}

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
        $TitleColor = 'black';
    if($set->BorderColor)
        $BorderColor = $set->BorderColor;
    else
        $BorderColor = 'black';
    if($set->textColor)
        $textColor = $set->textColor;
    else
        $textColor = 'black';
    if($set->BackgroundColor)
        $BackgroundColor = $set->BackgroundColor;
    else
        $BackgroundColor = 'white';
    if($set->Signature)
        $Signature = $set->Signature;
    else
        $Signature = 'This is Your Signature';
    if($set->SignatureColor)
        $SignatureColor = $set->SignatureColor;
    else
        $SignatureColor = 'grey';
    if($set->LogoMAil)
        $logo = $set->LogoMAil;
}
?>

<div class="settings">
    <h1>Email Template Settings</h1>
    <form enctype="multipart/form-data" action="" method="post" >
        <div id="changes">
            <div class="oneBox box">
                <label for="emailTitle" >Email title</label>
                <input type="text" value="<?php echo $emailTitle ?>" name="emailTitle" id="emailTitle" placeholder="My site" >
            </div>
            <div class="Box box">
                <label for="TitleColor" >Title color</label>
                <input type="color" value="<?php echo $textColor ?>" name="TitleColor" id="TitleColor" >
            </div>
            <div class="Box box">
                <label for="BorderColor" >Border color</label>
                <input type="color" value="<?php echo $textColor ?>" name="BorderColor" id="BorderColor" >
            </div>
            <div class="twoBox box">
                <label for="textColor" >Text color</label>
                <input type="color" value="<?php echo $textColor ?>" name="textColor" id="textColor" >
            </div>
            <div class="threeBox box">
                <label for="BackgroundColor" >Background color</label>
                <input type="color" value="<?php echo $BackgroundColor ?>" name="BackgroundColor" id="BackgroundColor" >
            </div>
            <div class="fourBox box">
                <label for="Logo" >Logo</label>
                <input id="Logo" type="file" name="LogoMAil"  placeholder="Upload File">
            </div>
            <div class="fiveBox box">
                <label for="Signature" >Signature</label>
                <textarea id="Signature" name="Signature"  <!--placeholder="<?php //echo $Signature; ?>" -->></textarea>
            </div>
            <div class="sixBox box">
                <label for="SignatureColor" >Signature color</label>
                <input type="color" value="<?php echo $SignatureColor ?>" name="SignatureColor" id="SignatureColor" >
            </div>
            <div class="lastBox box">
                <input type="submit" value="Save" class="button button-primary" name="submit">
            </div>
        </div>
        <div id="preview">
            <div style="background-color: <?php echo $BackgroundColor; ?>; border-color: <?php echo $BorderColor; ?>" class="previewMain">
                <div class="Logo">
                    <?php if($logo)
                        echo '<div class="yourLogo"><img src="'.plugins_url().'/sh-newsletter/uploads/'.$logo.'"/></div>';
                    else
                        echo '<div class="defaultLogo">This is Logo area</div>';?>
                    <h1 style="color:<?php echo $TitleColor; ?>" class="previewTitle"><?php echo $emailTitle; ?></h1>
                </div>
                <div style="color:<?php echo $textColor; ?>" class="text">
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                    <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
                <div class="signature">
                    <p style="color:<?php echo $SignatureColor; ?>" class="SignatureType" >
                       <?php echo $Signature; ?>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
