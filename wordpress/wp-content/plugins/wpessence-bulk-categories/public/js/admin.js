jQuery(document).ready(function($) {
	$('.show-categories-example').click(function() {
		$('#bac-categories').val(
			  'Cities/Amsterdam\n'
			+ 'Cities/London\n'
			+ 'Cities/Paris\n'
			+ 'People/A\n'
			+ 'People/B\n'
			+ 'People/C\n'
			+ 'People/D\n'
			+ 'People/C/B/F\n'
			+ 'Great\n'
			+ 'Capitals/Africa/South Africa\n'
			+ 'Capitals/"Africa"/Egypt\n'
			+ 'Fantastic\n'
			+ 'Awesome\n'
			+ 'Hardware/"Audio/video"/Top Products\n'
			+ 'Hardware/Computers\n'
			+ 'Brilliant\n'
			+ $('#bac-categories').val());
		
		$('#bac-categories-slugs').val(
			  'amsterdam\n'
			+ 'london\n'
			+ 'Paris\n'
			+ 'letter-a\n'
			+ 'letter-bb\n'
			+ '\n'
			+ '\n'
			+ 'F\n'
			+ 'good\n'
			+ 'south-africa\n'
			+ 'egypt\n'
			+ 'fantastico\n'
			+ '\n'
			+ 'top-products\n'
			+ 'pcs\n'
			+ 'brilliant\n'
			+ $('#bac-categories-slugs').val());
		
		return false;
	});
	
	$('textarea#bac-categories, textarea#bac-categories-slugs').scroll(function() {
		$(this).css('background-position', '0px ' + (5 - $(this).scrollTop()) + 'px');
	});
	
	$('textarea#bac-categories').linedtextarea();
	$('textarea#bac-categories-slugs').linedtextarea({
		additionalCssClasses_wrapper: 'categories-slugs-wrapper'
	});
	
	$('#bac-taxonomy').change(function() {
		var taxonomy = $(this).val();
		
		$('#ajax-loading').css('visibility', 'visible');
		
		$.post(WPEBC_Ajax.ajaxurl, { action: 'jwsr_loadtaxonomydropdown', 'taxonomy': taxonomy}, function(data) {
			$('#top-parent-category').html(data);
			$('#ajax-loading').css('visibility', 'hidden');
		}, 'html');
	});
});