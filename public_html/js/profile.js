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

function NickValid(nick)
{
    var re = new RegExp("^([a-zA-Zа-яА-ЯёЁ0-9]{3,20})$");
    var re1 = new RegExp("^([a-zA-Z0-9]{3,20})$");
    var re2 = new RegExp("^([а-яА-ЯёЁ0-9]{3,20})$");
    var okid = "#Nick_Ok";

    if (re1.test(nick) || re2.test(nick)) {

        $('#sendnick').show();
        //console.log("Valid");
        return true;
    } else {
        if(re.test(nick))
        {
            console.log("mixed");
            $(okid).prop('style','color: red');
            $(okid).html('Не смешивайте языки');
            $(okid).show();
            setTimeout(function() {$(okid).hide('slow');}, 1000);
        }
        $('#sendnick').hide();
        console.log("Invalid");
        return false;
    }
}

function SetNick(nick)
{
    var okid = "#Nick_Ok";

    $.ajax
    ({
        url: "hendlers/setnick.php", // путь к ajax файлу
        type: "POST",      // тип запроса
        dataType: "html",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        },
        cache: false,
        data: {
            nick: nick
        },
        // Данные пришли
        success: function(data)
        {
            if(data != 'ok')
                $(okid).prop('style','color: red');
            else
                $(okid).prop('style','color: green');

            if(data == 'reload')
            {
                $(okid).html("Ой, не понял! Еще разик, плз.");
                $(okid).show();
                setTimeout(function() {$(okid).hide('slow');}, 1000);
                return document.location.reload(true);

            }

            $('#sendnick').hide();
            $(okid).html(data);
            $(okid).show();
            setTimeout(function() {$(okid).hide('slow');}, 1000);
        }
    });
}