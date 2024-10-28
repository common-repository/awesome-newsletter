;(function ($) {
    $(document).ready(function () {

        $("#choose_option").change(function() {
            var choose_option = $(this).val();
            if( choose_option == 'post' ) {
                $(".show_post_option").show('slow');
                $(".show_custom_message_option").hide();
            } else if ( choose_option == 'custom-message' ) {
                $(".show_custom_message_option").show('slow');
                $(".show_post_option").hide();
            } else {
                $(".show_custom_message_option").hide();
                $(".show_post_option").hide();
            }
        })

        // check all checkbox
        $(".aw_choose_all").click(function(){
            $('.aw_check_me').not(this).prop('checked', this.checked);
        });        
        
        // send email form
        $("#aw_form_send_message").on('submit', function (e) {    
            tinyMCE.triggerSave();            
            var nonce       =   $("#aw_nonce_field").val();
            var form_data   =   $(this).serialize()+'&s='+nonce+'&action=aw_send_message_action';
            $.ajax({
                type        :   'POST',
                url         :   urls.ajaxurl,
                dataType    :   'html',
                data        :   form_data,
                beforeSend : function () {
                    $("#aw_send_message").val('Please wait...');
                },
                success: function ( result ) {
                    $("#aw_send_message").val('Send Message');
                    $("#aw_form_result").html( result );
                }
                
            });          

            return false;
        });

        // settings tab form
        $("#aw_settings").on('submit', function (e) {            

            var nonce       =   $("#aw_settings_tab").val();
            var form_data   =   $(this).serialize()+'&s='+nonce+'&action=aw_settings_tab';            
            $.ajax({
                type        :   'POST',
                url         :   urls.ajaxurl,
                dataType    :   'html',
                data        :   form_data,
                beforeSend : function () {
                    $("#aw_settings_btn").val('Please wait...');
                },
                success: function ( result ) {
                    $("#aw_settings_btn").val('Save options');
                    $("#aw_form_result").html( result );
                }
                
            });          

            return false;
        });

        // Email list tab
        $(".aw_remove_email").on('click', function (e) {            
            e.preventDefault();
            var nonce       =   $(this).data("nonce");
            var email_id    =   $(this).data("id");
            var that        =   $(this);

            $.ajax({
                type        :   'POST',
                url         :   urls.ajaxurl,
                dataType    :   'json',
                data        :   {
                    s   :   nonce, 
                    id  :   email_id,
                    action : 'aw_email_list_tab',
                },
                beforeSend : function () {                    
                    that.text('Please wait...');
                },
                success: function ( result ) {
                    if ( result.deleted == 'Removed.' ) {
                        that.text('Removed');
                        that.closest('tr').css('background-color', '#ffcfcf');
                        setTimeout(function () {
                            that.closest('tr').remove();
                        }, 3000 );
                    } else {
                        that.text('Remove');
                    }      
                                        
                }
                
            });          

            return false;
        });

    });

})(jQuery);