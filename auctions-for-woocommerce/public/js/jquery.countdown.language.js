/* http://keith-wood.name/countdown.html
 */
(function($) {
	$.wc_auctions_countdown.regionalOptions['us'] = {
		labels: [wc_auctions_language_data .labels.Years, wc_auctions_language_data .labels.Months, wc_auctions_language_data .labels.Weeks, wc_auctions_language_data .labels.Days, wc_auctions_language_data .labels.Hours, wc_auctions_language_data .labels.Minutes, wc_auctions_language_data .labels.Seconds],
		labels1: [wc_auctions_language_data .labels1.Year, wc_auctions_language_data .labels1.Month, wc_auctions_language_data .labels1.Week, wc_auctions_language_data .labels1.Day, wc_auctions_language_data .labels1.Hour, wc_auctions_language_data .labels1.Minute, wc_auctions_language_data .labels1.Second],
		
		compactLabels: [wc_auctions_language_data .compactLabels.y, wc_auctions_language_data .compactLabels.m, wc_auctions_language_data .compactLabels.w, wc_auctions_language_data .compactLabels.d],
		whichLabels: function(amount) {
			return (amount == 1 ? 1 : (amount >= 2 && amount <= 4 ? 2 : 0));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.wc_auctions_countdown.setDefaults($.wc_auctions_countdown.regionalOptions['us']);
})(jQuery);
