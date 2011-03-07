/* godmode enabler */
$(function(){
    var phrase = 'iddqd';
    var input = '';
    $(window).keypress(function(event){
        if (event.target.tagName.toLowerCase() == 'input')
            return;
        if (event.target.tagName.toLowerCase() == 'textarea')
            return;
        if (event.target.tagName.toLowerCase() == 'select')
            return;
        if (event.altKey || event.shiftKey || event.ctrlKey)
            return;
        var key = String.fromCharCode(event.which);
        if (!key)
            return;
        input += key;
        if (input.substr(-phrase.length) == phrase)
            window.location.href = '/admin/?return';
    });
});
