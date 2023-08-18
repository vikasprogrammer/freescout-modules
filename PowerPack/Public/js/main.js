/**
 * Module's JavaScript.
 */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,    
    function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
if ( typeof enable_kb_section !== 'undefined') {
    if(enable_kb_section==1){
        if ( typeof kb_section_html !== 'undefined') {
                if(kb_section_html){
                    $('#app > div > div.row > div').removeClass('col-sm-offset-2');
                    $('#app > div > div.row > div').removeClass('col-md-offset-3');
                    $('#app > div > div.row > div').removeClass('col-lg-offset-4');
                    $('#app > div > div.row > div').css({"margin-left":"5%", "margin-right":"5%"});
                    $('#app > div > div.row > div').after(kb_section_html);
                }
        }
        if ( typeof logoImageKb !== 'undefined') {
            if(logoImageKb){
                $('#app > nav > div > div.navbar-header > a').html(logoImageKb);
            }
        }
       
    }
}
if ( typeof html !== 'undefined') {
    if(html){
        $('#eup-submit-form-main-area > div:nth-child(5)').after(html);
    }
}
if ( typeof customHtml !== 'undefined') {
    if(customHtml){
        $('#eup-submit-form-main-area input[name="name"]').before(customHtml);
    }
}
if ( typeof logoImageEup !== 'undefined') {
    if(logoImageEup){
        $('#app > nav > div > div.navbar-header > a').html(logoImageEup);
    }
}
// if ( typeof logoImageKb !== 'undefined') {
//     if(logoImageKb){
//         $('.navbar-brand-with-text').html(logoImageKb);
//     }
// }
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