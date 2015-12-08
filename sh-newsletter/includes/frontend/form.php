<form autocomplete="off" id="SH_newsletter"  method="post">
    <div class="newsltr">Subscribe for newsletters!</div>
    <div class="input_place">
        <div class="form_txt">
            We will glad to send you info about our events and featured news. Leave your e-mail and name here for subscription!
        </div>
        <input type="text" name="name" placeholder="Name Surname"/>
        <input type="email" name="email" placeholder="Email"/>
        <input type="button" name="submit"  value="Subscribe">
        <div class="progress">
            <img alt="progress register" src="<?php echo plugins_url();?>/sh-newsletter/sources/img/progress_bar.gif" />
        </div>
        <div class="answerThankYou"></div>
        <div class="errorBase">Database error</div>
        <div class="errorEmail">Email is not valid</div>
        <div class="emptyName">Name is empty</div>
        <div class="emptyEmail">Email is empty</div>
    </div>

</form>