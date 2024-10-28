<?php 
//  Provide a public-facing view for the plugin

class awesome_Newsletter_Public_Display {

	public function aw_newsletter_form ( $title, $placeholder ) {
        ?>
        <div class="single-widget widget-newsletter">
            <h5 class="widget-title">
                <?php echo esc_html ( $title ); ?>
            </h5>
            <div id="aw_form_result"></div>
            <form action="#" id="submit_newsletter">
                <input type="text" name="aw_email" id="aw_email" placeholder="<?php echo esc_attr( $placeholder ); ?>">
                <button type="submit" id="aw_submit">
                    <i class="icofont icofont-paper-plane"></i>
                </button>
                <?php wp_nonce_field( 'aw_action', 'aw_nonce_field' ) ?>
            </form>
        </div> 
        <?php
    }
}