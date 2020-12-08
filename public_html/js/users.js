$('#fol_form').on('change','input[type="checkbox"]',function(){

    var form = $('#fol_form');
    $.ajax
    ({
        url: "hendlers/setfolow.php", // путь к ajax файлу
        type: "POST",      // тип запроса
        dataType: "html",
        cache: false,
        data: form.serialize(),
        // Данные пришли
        success: function(data)
        {
            /*
            $(okid).html(data);
            $(okid).show();
            setTimeout(function() {$(okid).hide('slow');}, 0);
            */
        }
    });
});
$('#all_info').on('click','#sendnick',function(){
    var nick = $('#public_nick').val();
    var valid = NickValid(nick);
    if(valid)
        SetNick(nick);
});

$('#all_info').on('input','#public_nick',function(){
    var nick = $('#public_nick').val();
    NickValid(nick);
});