(function () {
    var module_routes = [
    {
        "uri": "mailbox\/auto-signature\/ajax-html\/{action}",
        "name": "mailboxes.auto_signature.ajax_html"
    },
    {
        "uri": "mailbox\/auto-signature\/ajax",
        "name": "mailboxes.auto_signature.ajax"
    }
];

    if (typeof(laroute) != "undefined") {
        laroute.add_routes(module_routes);
    } else {
        contole.log('laroute not initialized, can not add module routes:');
        contole.log(module_routes);
    }
})();