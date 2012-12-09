$(function() {
	myHeight = $('#nav');
	myHeight.hide();
});

function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
		'<li><a href="'+base+'../index.html">Documentation home</a></li>' +
		'</ul>' +

		'<h3>Basic Info</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'<h3>Installation</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'<h3>Introduction</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +
		
		'<h3>Tutorial</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +

		'<h3>General Topics</h3>' +
		'<ul>' +
		'<li><a href="'+base+'general/themes.html">Themes</a></li>' +
		'</ul>' +

		'<h3>Additional Resources</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>Class Reference</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>Driver Reference</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'<h3>Helper Reference</h3>' +
		'<ul>' +
//		'<li><a href="'+base+'sample/sample.html">Sample</a></li>' +
		'</ul>' +

		'</td></tr></table>');
}