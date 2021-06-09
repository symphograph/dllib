$('#rent_in').on('click','#ok',function (){

    $.ajax
    ({
        url: "hendlers/recedit.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: $('#recdata').serialize(),

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $('.responses').html(data );
            $('.responses').show();
            //setTimeout(function() {$('.responses').hide('slow');}, 0);

        }
    });
});