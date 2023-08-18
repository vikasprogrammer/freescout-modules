/**
 * Module's JavaScript.
 */
function initPowerPackSettings(powerPack){
    alert(powerPack);
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,    
    function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
// if ( typeof logoImageKb !== 'undefined') {
//     if(logoImageKb){
//         $('.navbar-brand-with-text').html(logoImageKb);
//     }
// }
if ( typeof logoImageKb !== 'undefined') {
    if(logoImageKb){
        $('#app > nav > div > div.navbar-header > a').html(logoImageKb);
    }
}
var eupLoadTicketForm = (function() {
    var original_eupLoadTicketForm =  eupLoadTicketForm;
    return function() {
        original_eupLoadTicketForm();
        var emailValue = decodeURIComponent(getUrlVars()['email']);
        var messageValue = decodeURIComponent(getUrlVars()['message']);
        var nameValue = decodeURIComponent(getUrlVars()['name']);
        
        (async() => {
            // console.log("waiting for variable");
            while(!document.getElementsByName("email")) // define the condition as you like
                await new Promise(resolve => setTimeout(resolve, 1000));
            // console.log("variable is defined");
            if(emailValue !== "undefined") 
                document.getElementsByName("email")[0].value = emailValue;
            
            if(messageValue !== "undefined") 
                document.getElementsByName("message")[0].value = messageValue + "\n";

            if(nameValue !== "undefined") 
                document.getElementsByName("name")[0].value = nameValue;

        })();
        // console.log("above code doesn't block main function stack");
    }
})();