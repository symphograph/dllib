$(window).load(function(){
$('.prices').on('change', 'input[type=checkbox]', function(){
	var current = $(this);
	var mat_id = $(this).attr('id').slice(5);
	var okid = $('#craft_price');
    var dataObj = {
        item_id: mat_id,
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
					//$(okid).show();
					current.removeAttr("checked");
					setTimeout(function() {PrimeCost();}, 200);


				} else
				{
					//$(okid).html("Ok");
					//$(okid).show();
					PrimeCost();
				}

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

function PrimeCost() {

	var okid = $("#craft_price");
	var item_id = $("#packselect").val();
	//console.log(item_id);
	$.ajax({
		data: {
			item_id: item_id
		},
		url: 'hendlers/packpost/packobj.php',
		type: "POST",
		success: function(data){

				var ddd = jQuery.parseJSON(data);

				$(okid).html(ddd.esyprice);



			//setTimeout(function() {$(okid).hide('slow');}, 2000);
		}
	});

}