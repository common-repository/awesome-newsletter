;(function ($) {
    $(document).ready(function () {        
        $("#aw_submit").on('click', function (e) {
            var aw_email = $("#aw_email").val();
            var nonce = $("#aw_nonce_field").val();

            $.ajax({
				type 		: 	'POST',
				url 		: 	urls.ajaxurl,
				dataType 	: 	'html',
				data 		: 	{
					action 		: 	"aw_action",
                	aw_email 	: 	aw_email,
                	s 			: 	nonce
				},
				beforeSend : function () {
					$("#aw_submit").html('...');
				},
				success: function ( result ) {
					$("#aw_submit").html('<i class="icofont icofont-paper-plane"></i>');					
					$("#aw_form_result").html( result );
				}
				
            });          

            return false;
        });
    });
})(jQuery);