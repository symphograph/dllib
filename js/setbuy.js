$(window).load(function(){
$('.prices').on('change', 'input[type=checkbox]', function(){
	var current = $(this);
	var item_id = $(this).attr('id').slice(5);
	var okid = $('#responses');
    var dataObj = {
        item_id: item_id,
        value: $(this).is(':checked')
    }
    $.ajax({
        data: dataObj,
        url: 'hendlers/isbuysets.php',
		type: "POST",
        success: function(data){
            console.log('Сервер вернул:' + data);
				if(data != "ok")
				{
					$(okid).html(data);
					$(okid).show(); 
					current.removeAttr("checked");
				} else
				{
					$(okid).html("Ok");
					$(okid).show(); 

				}
			//setTimeout(function() {$(okid).hide('slow');}, 2000);
        }
    });
});
});