/**
 * Created by daniq on 16-3-2017.
 */
var id = $('#id').val();
$(window).load(function() {
    $('#chatbox').load('/chat-api.php?chat='+id + '');
    setTimeout(
        function()
        {
            $('#chatbox').scrollTop(10000)
        }, 500);
});
function updateChat() {
    $('#chatbox').load('/chat-api.php?chat='+id + '');
}
function loadChats() {
    $('#chats').load('/chat-api.php?loadchats');
}
setInterval(updateChat, 1000);
setInterval(loadChats, 1000);
$( "#send" ).click(function() {
    var bericht = $('#bericht').val();
    $.get( "chat-api.php", { sendchat: id, message: bericht } );
    $('#bericht').val('');
    setTimeout(
        function()
        {
            $('#chatbox').scrollTop(10000)
        }, 500);
});
function isEmpty( el ){
    return !$.trim(el.html())
}
$('#bericht').keypress(function(e) {
    if(e.which == 13) {
        var bericht = $('#bericht').val();
        $.get( "chat-api.php", { sendchat: id, message: bericht } );
        $('#bericht').val('');
        setTimeout(
            function()
            {
                $('#chatbox').scrollTop(10000)
            }, 500);
    }
});