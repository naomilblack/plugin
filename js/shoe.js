function shoe_rating_vote(ID, type)
{
	var data = {
		action: 'shoe_rating_add_vote',
		postid: ID,
		type: type
	};

	jQuery.post(shoe_rating_ajax.ajax_url, data, function(response) {

		var container = '#shoe-rating-' + ID;
		
		var object = jQuery(container);
		
		jQuery(container).html('');
		
		jQuery(container).append(response);
		
				
		jQuery(object).removeClass('shoe-rating-container');
		jQuery(object).attr('id', '');

	});
}