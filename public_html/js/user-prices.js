window.onload = function() {

    //$(".small_del").show();
    LoadPrices();

};


$('#all_info').on('input','.pr_inputs',function(){

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

$('.prmenu').on('change','#sort', function(){
    LoadPrices();
});

function LoadPrices()
{
    let sort = $('#sort').val();
    let puser_id = $('#puser_id').val();
    $.ajax
    ({
        url: "hendlers/user_prices.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: {
            sort: sort,
            puser_id: puser_id
        },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#all_info").html(data);
            $(".small_del").show();
        }
    });
}
