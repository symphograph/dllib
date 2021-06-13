window.onload = function() {
    //setTimeout(function() {$("#xlgames").hide('slow');}, 3000);
    TipTop();
};
//$('#packsettings').on('change','input, select',function(){QueryPacks()});

/*
$('#input_data').ready(function()
{
    $('#input_data').on('mouseover','div[class="pack_icon"]',function()
    {
        var item_id = $(this).attr('itid');
        var divid = $(this).attr('id');
        if(divid == 0) return false;
        QueryMats(item_id,divid);
        //$("#"+divid).attr('id',0);

    });
})

function QueryMats(id,divid)
{
    var mats = $("#"+divid).next(".pkmats_area");
    $.ajax
    ({
        url: "hendlers/pack_mats.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: {
            item_id: id
        },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $(mats).html(data );
        }
    });

}
*/
$('main').on('click','div',function(){



    //return false;
});

function TipTop()
{

    $.ajax
    ({
        url: "hendlers/tiptop.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: {
            tiptop: 1
        },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#tiptop").html(data );
        }
    });

}
function QueryPacks()
{
    var form = $("#packsettings");

    var side = $('input[name=side]:checked',form).val();
    //$("#input_data").html(jdun);
    $jdun = $("#jdun");

    if(!side) return($("#input_data").html('<h2>"Восток" или "Запад"?</h2>'));
    //console.log(side);
    $jdun.removeClass("jdun"); $jdun.addClass("loading");
    $.ajax
    ({

        url: "hendlers/packs_list.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: form.serialize(),

        dataType: "json",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $jdun.removeClass("loading"); $jdun.addClass("jdun");
            //$("#input_data").html(data );
            packList(data)
            TipTop();
        }
    });
}

function packList(arr)
{
    //validator.showErrors(JSON.parse(arr))
    console.log(arr)

    $.each(arr,function(k,v){


        let row = '';
        row = '<div className="pack_row0">' + v.Pack.pack_name + '</div><hr>';
        console.log(row);
    });


}

$('#all_info').on('input','.pr_inputs',function(){
    //Удаляет цену юзера
    var form_id = $(this).get(0).form.id;
    //console.log(form_id);
    //var name = $(this).attr("name");

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

$('#all_info').on('click','.itim',function(){
    var item_id = $(this).attr('id').slice(5);
    var url = 'catalog.php?item_id='+item_id;
    window.location.href = url;
});