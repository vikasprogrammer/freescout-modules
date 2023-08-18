(function () {
    var module_routes = [
    {
        "uri": "mailbox\/envato-setting\/ajax-html\/{action}",
        "name": "mailboxes.envato_setting.ajax_html"
    },
    {
        "uri": "envato-setting\/ajax-admin",
        "name": "mailboxes.envato_setting.ajax_admin"
    },
    {
        "uri": "envato-setting\/ajax",
        "name": "mailboxes.envato_setting.ajax"
    },
    {
        "uri": "envato-setting\/ajax-search",
        "name": "mailboxes.envatoSetting.ajax_search"
    }
];

    if (typeof(laroute) != "undefined") {
        laroute.add_routes(module_routes);
    } else {
        contole.log('laroute not initialized, can not add module routes:');
        contole.log(module_routes);
    }
})();