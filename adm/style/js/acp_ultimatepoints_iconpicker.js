(function ($) {

	'use strict';

	$(function () {
		// Are we in settings or in update
		var settings = $('#acp_points_main').length ? true : false || $('#acp_bank').length ? true : false || $('#acp_lottery').length ? true : false;

		// Our icon input field
		var $input = settings ? $('.points-icon') : $('#update_icon');

		// Initiate the fontawesome-iconpicker
		$input.iconpicker({
			animation: false,
			collision: true,
			placement: 'bottom',
			hideOnSelect: true,
			selectedCustomClass: 'points-icon-selected',
		});

		// If an icon is selected, update the preview icon
		if (!settings) {
			$input.on('iconpickerSelect', function (event) {
				$(this).siblings('label').html(event.iconpickerItem.context.innerHTML).children('i').addClass('fa-lg');
			});
		}
	});

})(jQuery);
