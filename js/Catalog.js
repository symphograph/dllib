// JavaScript Document

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
function ContentLoad(sgroup,categ_id = 0)
{
	$('#items_head').removeClass('hidden');
	var needelement = "#categories";
	var url = "hendlers/item_groups.php";
	//console.log(url);
	$.ajax
	({
			url: url,
			type: "POST",
			datatype: "html",
			cache: false,
			data: 
			{
				sgroup: sgroup,
				categ_id: categ_id
			},
		
			 // Данные пришли
			success: function(data ) 
			{
				$(needelement).html(data );
				$('#snav').html('<div id="searchbtn"></div>');
				$('#search_box').val('');
			}
	})
	$('#nav-toggle').prop('checked',true);
	$('#items').html('');
	$('#categ_name').html('');
	TipTop();
}
$('#all_info').on('click','.mcateg',function()
{
	var categ_id = $(this).attr("id").slice(6);
	var sgroup = $(this).attr("sgroup");
	//console.log(sgroup);
	ContentLoad(sgroup,categ_id);
	QueryItems(categ_id);
});
$('#snav').on('click','#searchbtn',function()
{
	if($("#search_box").val().length<3)
		return;
	
	$('#nav-toggle').prop('checked',false);
	$('#items_head').removeClass('hidden');
	//$('#catalog_head').addClass('hidden');
	SearchItems();
	
});
	
	
$('#all_info').on('click','.main_itim',function()
{
	var craft_id = this.id.slice(3);
	if(craft_id == 0) return false;
	var item_id = $(this).attr("name");
	//console.log(item_id);
	SetCraftAsBest(craft_id,item_id);
	
});
function SetCraftAsBest(craft_id,item_id)
{
	$.ajax
	({
		
		url: "hendlers/set_best.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: 
			{
				craft_id: craft_id,
			},
		
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			LoadItem(item_id);

		}
	});
}

$('#all_info').on('change','input[name="isbuy"]',function()
{
	var isbuy = $('input[name=isbuy]:checked',"#all_info").val();
	var item_id = $('input[name=isbuy]:checked',"#all_info").attr("id").slice(3);
	
	IsBuySet(item_id,isbuy);
	
});	
	
$('#all_info').on('click','div[class="itim"], label[class="nicon"]',function()
{
	var item_id = $(this).attr('id').slice(5);
	 if (event.shiftKey) {
        UpdateItem(item_id);
    }else
	LoadItem(item_id);
});

$('#all_info').on('change','#u_amount',function()
{
	var u_amount = $("#u_amount").val();
	var item_id = $(".nicon div[class=itim]","#all_info").attr("id").slice(5);
	//console.log(u_amount);
	LoadItem(item_id, u_amount);
});
	
function SearchItems()
{ 

	var squery = $('#search_box').val();
	squery = $.trim(squery);
	var view = $('input[name=view]:checked',"#all_info").val();
	
	$("#categ_name").html('Результат поиска');
	
	$.ajax
	({
		
		url: "hendlers/items_list.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: 
			{
				squery: squery,
				view: view
			},
		
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$("#items").html(data);
			$('#search_advice_wrapper').html('');
			$('#search_advice_wrapper').hide();
			TipTop();
		}
	});
}
function QueryItems(cat_id = 0)
{ 
	var form = $("#all_info");
	if(cat_id ==0)
	{
		var cat_id = $('body input[name=cat_id]:checked').val();
	}
	var view = $('input[name=view]:checked',form).val();
	var categ_name = $('input[name=cat_id]:checked').prev().html();
	$('label[class="catname"]').removeClass("catname");
	var makegray = $('input[name=cat_id]:checked').prev();
	makegray.addClass("catname");
	
	$("#categ_name").html(categ_name);
	
	//console.log(cat_id);
	$.ajax
	({
		
		url: "hendlers/items_list.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: 
			{
				cat_id: cat_id,
				view: view
			},
		
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$("body #items").html(data );
			TipTop();
		}
	});
}	


function LoadItem(item_id, u_amount = '')
{
	
	
	var needelement = "#items";
	var url = "hendlers/item.php";
	$('#search_advice_wrapper').html('');
	
	
	$.ajax
	({
			url: url,
			type: "POST",
			datatype: "html",
			cache: false,
			data: 
			{
				item_id: item_id,
				u_amount: u_amount
			},
		
			 // Данные пришли
			success: function(data ) 
			{
				$(needelement).html(data );
				$('#snav').html('<div id="backbtn"></div>');
				var item_name = $('#mitemname').text();
				$('#search_box').val(item_name);
				$('#nav-toggle').prop('checked',false);
				$('#items_head').addClass('hidden');
				$('#catalog_head').removeClass('hidden');
				$('#current').val(item_id);
				TipTop();
			}
	})
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

$('#all_info').on('input','.pr_inputs',function(){
	
	var form_id = $(this).get(0).form.id;
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

function IsBuySet(item_id,isbuy)
{ 	
	
	$.ajax
	({
		url: "hendlers/isbuyset.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			item_id: item_id,
			isbuy: isbuy
		},

		// Данные пришли
		success: function(data) 
		{
			LoadItem(item_id);
		}
	});
}