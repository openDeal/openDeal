(function($) {
	$.countdown.regionalOptions['en'] = {
		// The display texts for the counters
		labels: ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'],
		// The display texts for the counters if only one
		labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],

		// The compact texts for the counters
		compactLabels: ['y', 'm', 'w', 'd', 'h', 'm', 's'],

		whichLabels: null, // Function to determine which labels to use
		timeSeparator: ':', // Separator for time periods
		isRTL: false // True for right-to-left languages, false for left-to-right
	};
	$.countdown.setDefaults($.countdown.regionalOptions['en']);
})(jQuery);
