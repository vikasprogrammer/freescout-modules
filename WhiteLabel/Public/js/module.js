/**
 * Module's JavaScript.
 */
$('#app > div.footer').text(copyright);
$('#app > nav > div > div.navbar-header > a > img').attr('src', logo);
$('#app > nav > div > div.navbar-header > a').css({"color":"#fff","padding-top":"15px"});
$('#app > div.content > div > div.heading').text('Dashboard');
$('#app > div.layout-2col > div.content-2col > div.row-container.form-container > div > div > form > div:nth-child(11) > div > p').text(' ');
$('#app > div.layout-2col > div.content-2col > div.row-container.form-container > div > div > form > div:nth-child(11) > div > p').text('Add "Powered by '+brandText+'" footer text to the outgoing emails to invite more developers to the project and make application better.');
$('#app > div.layout-2col > div.content-2col > div.row-container.form-container > div > div > form > div:nth-child(11) > div > p').attr('data-content',' ');
$('#app > div.layout-2col > div.content-2col > div.row-container.form-container > div > div > form > div:nth-child(3) > div > div > i').attr('data-content','This number is not visible to customers. It is only used to track conversations within '+brandText);