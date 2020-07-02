$(window).load(function(){



    $('#form').on('change','#zfrom',function()
    {

        ZtoLoad();
        $(document).ready( function() {
            var item_id = $("#packselect").val();
            PackLoad(0,item_id);
        });



    });

    $(document).ready( function(){

        $('#form').on('change','#packselect',function()
        {

            var zone = $("#packselect option:selected").data("id");
            $("#zfrom").val(zone);
            $("#packselect").children('[data-id!="'+zone+'"]').remove();

            ZtoLoad(zone)
            FreshTimeLoad();


        });
        $('#form').on('change','#freshtime, #per, #siol, #zto',function()
        {
            InfoLoad();
        });

        $('#form').on('check', '#siol',function()
        {
            InfoLoad();
        });
    });




});

function ZtoLoad(from_id,to_id = 0) {
    if(!from_id)
        from_id = $("#zfrom").val();
    if(!to_id)
        var to_id = $("#zto").val();
    if(from_id === 100)
    {
        $("#zto").html('');
        $("#bill").html('');
        $("#freshtime").html('');
        return;
    }


    $.ajax
    ({
        url: "/hendlers/packpost/selectzones.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                from_id: from_id,
                to_id: to_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#zto").html(data );
        },async: false
    });
}

function InfoLoad(){
    var from_id = $("#zfrom").val();
    if(from_id === "100")
    {
        $("#bill").html('');
        return;
    }

    $.ajax
    ({
        url: "hendlers/packpost/packpostinfo.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: $('#form').serialize(),
        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#bill").html(data);
            MatsLoad();

        }
    });
}

function MatsLoad() {

    var item_id = $("#packselect").val();

    $.ajax
    ({
        url: "hendlers/packpost/packpostmats.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                item_id: item_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#maters").html(data );
            $(".small_del").show();

        }
    });

}



function FreshTimeLoad(item_id,from_id) {
    if(!item_id)
        item_id = $("#packselect").val();
    if(!from_id)
        from_id = $("#zfrom").val();

    $.ajax
    ({
        url: "hendlers/packpost/selfreshtime.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                item_id: item_id,
                from_id: from_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#freshtime").html(data );
            InfoLoad();
        },
        async: false
    });

}



function PackLoad(from_id,item_id = 0) {
    if(!from_id)
        from_id = $("#zfrom").val();

    $.ajax
    ({
        url: "hendlers/packpost/selectpack.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                from_id: from_id,
                pitem_id: item_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#packselect").html(data );
            FreshTimeLoad();

        },
        async:false
    });

}