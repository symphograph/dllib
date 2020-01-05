$(document).ready(function () {
	$('#category_id').change(function () {
		var category_id = $(this).val();
		if (category_id == '0') {
			$('#item_id').html('<option>- сначала выберете категорию -</option>');
			$('#item_id').attr('disabled', true);
			return(false);
		}
		$('#item_id').attr('disabled', true);
		$('#item_id').html('<option>загрузка...</option>');
		
		var url = 'get_items.php';
		
		$.get(
			url,
			"category_id=" + category_id,
			function (result) {
				
				if (result.type == 'error') {
					alert('error');
					return(false);
				}
				else {
					var options = '';
					$(result.items).each(function() {
						options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
					});
					$('#item_id').html(options);
					$('#item_id').attr('disabled', false);
				}
			},
			"json"
		);
	});
});
