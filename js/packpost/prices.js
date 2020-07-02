$(window).load(function(){


        $(".small_del").show();


    $('#all_info').on('change', '.pr_inputs', function () {

        var form_id = $(this).get(0).form.id;
        SetPrice(form_id);
        /*
        var bronz = 0;
        var silver = 0;
        var gold = 0;
        $("[name=setbronze]").each(function(){
            var a_id = $(this).attr("id");
            bronz += parseInt($(this).val(), 10);

            var a_id = a_id.slice(4);
            console.log(a_id);
        });
        $("[name=setsilver]").each(function(){
            silver += parseInt($(this).val(), 10);
            console.log($(this).attr("id"));
        });
        $("[name=setgold]").each(function(){
            gold += parseInt($(this).val(), 10);
            console.log($(this).attr("id"));
        });
        if(!gold) gold = 0;
        if(!silver) silver = 0;
        if(!bronz) bronz = 0;

        sum = gold*10000 + silver*100 + bronz;
        console.log(form_id);

         */

    });

    function SetPrice(form_id) {
        var form = $("#" + form_id);

        var mat_id = form_id.slice(3);
        var okid = "#craft_price";


        $.ajax
        ({
            url: "hendlers/setprcl.php", // путь к ajax файлу
            type: "POST",      // тип запроса

            data: form.serialize(),

            dataType: "html",
            cache: false,
            // Данные пришли
            success: function (data) {

                $("#prdel_" + mat_id).show();

                var iss = $("input").is('#isby_'+mat_id);
                if(!iss) PrimeCost();

                if($('#isby_'+mat_id).prop("checked"))
                    PrimeCost();
            }
        });
    }

    $('#maters').on('click', '.small_del', function () {
        //Удаляет цену юзера
        var form_id = $(this).get(0).form.id;
        var item_id = form_id.slice(3);
        var okid = "#PrOk_" + item_id;
        var iss = $("input").is('#isby_'+item_id);
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
            success: function (data) {
                $(okid).html(data);
                $(okid).show();
                setTimeout(function () {
                    $(okid).hide('slow');
                }, 0);

                $("#prdel_" + item_id).hide('slow');
                $("#" + form_id).find("input[type=number]").val("");


                if(!iss) PrimeCost();
            }
        });

    });

});