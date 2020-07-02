$(window).load(function(){
    istractor();
    $(document).ready( function(){
        $('#timeform').on('change, input','input[type=radio], input[type=checkbox]',function()
        {
            istractor();
            LoadTimes();
        });
    });

    $('#timeform').on('input',function()
    {
        istractor();

    });

    $('#timeform').on('click','#sendtime',function()
    {
        SendTime();
    });




});

function InTime(){
    var intime = $("#to_input").val();
    if(intime)
        $("#time").val(intime);
    else
        $("#time").val('');
}

function LoadTimes(){
    $.ajax
    ({
        url: "hendlers/listusertime.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: $('#timeform').serialize() + '&' + $('#zfrom').serialize() + '&' + $('#zto').serialize(),
        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#table_div").html(data );
            InTime();
        }
    });
}

function istractor() {
    var buff3 = $("#buff_3");
    var buff2 = $("#buff_2");
    if($('#trans_1').prop("checked"))
    {
        // console.log("yes");
        buff3.show(0);
        buff2.show(0);
    }else
    {
        // console.log('no');
        buff3.find("input[type=checkbox]").prop("checked", false);
        buff3.hide(0);
        buff2.find("input[type=checkbox]").prop("checked", false);
        buff2.hide(0);

    }
}

function DurDel(dur_id)
{
    $.ajax({
        url: "hendlers/deldur.php", // путь к ajax файлу
        type: "POST",      // тип запроса
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        },
        data: {
            dur_id: dur_id
        },

        // Данные пришли
        success: function(data) {
            if(data === "ok")
                $("#row_"+dur_id).hide();
        }
    });
}

function SendTime() {
    var form = $('#timeform');

    if(!$("#time").val() > 0)
    {
        // console.log("is 0");
        return false;
    };
    $.ajax
    ({
        url: "hendlers/tradetimeadd.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: form.serialize() + '&' + $('#zfrom').serialize() + '&' + $('#zto').serialize(),
        dataType: "html",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        },
        cache: false,
        // Данные пришли
        success: function(data)
        {
            $('#responses').html(data);
            LoadTimes();
        }
    });
}