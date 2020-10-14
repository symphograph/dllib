// JavaScript Document

$(window).load(function(){
let tooltipElem;
$('body').on('mouseenter', '[data-tooltip]', function() 
{ 
	//console.log(this);
	var tdiv = $(this).attr('id');
	let tooltipHtml = $(this).attr('data-tooltip');
	
      if (!tooltipHtml) return;
	 // ...создадим элемент для подсказки
	
      tooltipElem = document.createElement('div');
      tooltipElem.className = 'tooltip';
      tooltipElem.innerHTML = tooltipHtml;
      document.body.append(tooltipElem);
	 
	 // спозиционируем его сверху от аннотируемого элемента (top-center)
      let coords = $(this)[0].getBoundingClientRect();
		 
	var elwidth = $(this)[0].offsetWidth; //Ширина элемента
	//console.log(elwidth);
      let left = coords.left - tooltipElem.offsetWidth/2 + elwidth/2 ;
      if (left < 0) left = 0; // не заезжать за левый край окна

      let top = coords.top - tooltipElem.offsetHeight - 5;
      if (top < 0) { // если подсказка не помещается сверху, то отображать её снизу
        top = coords.top + $(this).offsetHeight + 5;
      }

      tooltipElem.style.left = left + 'px';
      tooltipElem.style.top = top + 'px';
	 
});
$('body').on('mouseleave', '[data-tooltip]', function() 
{
	if(tooltipElem)
	{
		tooltipElem.remove();
    	tooltipElem = null;	
	}
	
	
});
$('body').on('click', function() 
{
	if(tooltipElem)
	{
		tooltipElem.remove();
    	tooltipElem = null;	
	}
});
$('body').on('keydown', function() 
{
	if(tooltipElem)
	{
		tooltipElem.remove();
    	tooltipElem = null;	
	}
});
});