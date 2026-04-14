// Custom JS Child Theme
document.addEventListener("DOMContentLoaded", function() {
	if (typeof window.jQuery === "undefined") {
		return;
	}

	const $ = window.jQuery.noConflict(),
		win = $(window).width(),
		d = new Date(),
		n = d.getFullYear(),
		page = $("html, body"),
		pageUrl = window.location.href,
		i = 0;

	window.wpThemeChild = window.wpThemeChild || {};
	window.wpThemeChild.viewportWidth = win;
	window.wpThemeChild.currentYear = n;
	window.wpThemeChild.pageUrl = pageUrl;

	// $('.foo__copy em').text(n).addClass('foo__copy-year');
});
