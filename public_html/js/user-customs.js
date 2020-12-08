function ContentLoad(custway)
{
    var needelement = "#items";
    var url = "hendlers/" + custway + ".php";

    $.ajax
    ({
        url: url,
        type: "POST",
        datatype: "html",
        cache: false,
        data:
            {
                usercustoms: "1"
            },

        // Данные пришли
        success: function(data )
        {
            $(needelement).html(data );
        }
    })
}

function SetProf(prof_id)
{
    var lvl = $("#prof_"+ prof_id).val();
    var okid = "#PrOk_"+ prof_id;

    $.ajax({
        url: "hendlers/setprof.php", // путь к ajax файлу
        type: "POST",      // тип запроса
        dataType: "html",
        cache: false,
        data: {
            prof_id: prof_id,
            lvl: lvl
        },

        // Данные пришли
        success: function(data ) {

            $(okid).html(data );
            $(okid).show();
            setTimeout(function() { $(okid).hide('slow'); }, 500);
            console.log(data);
        }
    });
}

$('#all_info').on('input','.pr_inputs',function(){
    //Удаляет цену юзера
    var form_id = $(this).get(0).form.id;

    SetPrice(form_id);
});

function SetPrice(form_id)
{
    var form = $("#"+form_id);
    var item_id = form_id.slice(3);
    var okid = "#PrOk_"+item_id;

    $.ajax
    ({
        url: "hendlers/setprcl.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: form.serialize(),

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $(okid).html(data );
            $(okid).show();
            setTimeout(function() {$(okid).hide('slow');}, 0);
            $("#prdel_"+item_id).show();
            if(data == "ок")
                $('input[class=pr_inputs]',form).css('background-color', '#79f148');
        }
    });
}

$('#all_info').on('click','.small_del',function(){
    //Удаляет цену юзера
    var form_id = $(this).get(0).form.id;
    var item_id = form_id.slice(3);
    var okid = "#PrOk_"+item_id;

    $.ajax
    ({
        url: "hendlers/setprcl.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                del: 'del',
                item_id: item_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $(okid).html(data );
            $(okid).show();
            setTimeout(function() {$(okid).hide('slow');}, 0);

            $("#prdel_"+item_id).hide('slow');
            $("#"+form_id).find("input[type=number]").val("");
        }
    });

});

function AucraftDel(item_id)
{
    $.ajax
    ({
        url: "hendlers/aucraftdel.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: {
            item_id: item_id
        },

        // Данные пришли
        success: function(data)
        {
            $("#aucraft_"+item_id).hide();
        }
    });
}

$('#all_info').on('click','.itim',function(){
    var item_id = $(this).attr('id').slice(5);
    var url = 'catalog.php?item_id='+item_id;
    window.location.href = url;
});

$('#all_info').on('click','.item_name',function(){
    var txt = $(this).text();
    selectText(this.id);
    document.execCommand("copy");
    $(this).html(txt +=' ');
    $(this).html(txt);

});

function selectText(elementId) {
    var doc = document,
        text = doc.getElementById(elementId),
        range,
        selection;

    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}