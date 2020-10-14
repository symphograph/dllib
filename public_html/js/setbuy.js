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
            //console.log('Сервер вернул:' + data);
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
});