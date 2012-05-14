// This file was copied from a real application just to test the assets pipeline on javascript files.
// Can be safely removed anytime

alert('test');
$.fn.makeSegmented = function(opt){
	var def = {
			segments:4,
			orientation:'horizontal'
	};
	
	$.extend(def, opt);
	var segment;
	for(var i=0; i < def.segments; i++)
	{
		segment = $('<div/>');
		if(def.orientation === 'horizontal')
		{
			$(segment).css({
				position:'relative',
				float: 'left',
				width: Math.floor($(this).width()/def.segments),
				height: '80px'
			});
		}
		else
		{
			$(segment).css({
				position:'relative',
				width: $(this).width(),
				height: Math.floor($(this).height()/def.segments)
			});
		}
		
		$(this).append(segment);
	}
};
function Calendar(){
	var t = this;
	//Each room is an object that has the following properties:
	//title The room name
	//id   The room id
	t.rooms = Array();
	//The array that containes the strings of each timeframe for the time axis of the calendar
	t.timeframes = Array();
	//How many segments each timeframe is made of
	t.segments = 4;
		
	t.table = {};
	
	t.orientation = 'horizontal';
	
	/**
	 * Finds and returns the td segment that is at the provided offset (required)
	 * @param offset {top: number, left: number} Required
	 * @param callback A function that takes the segment as a parameter and is executed once the segment is found
	 */
	t.getSegment = function(offset){
		var segment = null;
		$('tbody tr',t.table).each(function(){
			var _offset = $(this).offset();			
			//Works for horizontal segments			
			if((_offset.top < offset.top) && ((_offset.top + $(this).outerHeight()) > offset.top))
			{							
				$('td',this).each(function(){
					var _offset = $(this).offset();
					if((_offset.left <= offset.left) && (($(this).outerWidth()+_offset.left) >= offset.left))
					{						
						$('div',this).each(function(){
							var _offset = $(this).offset();							
							if(t.orientation==='horizontal')
							{
								if(_offset.left >= offset.left)
								{
									segment = this;
									return false;
								}
							}
							else
							{
								if(_offset.top >= offset.top)
								{
									segment = this;
									return false;
								}
							}
						});
						return false;
					}
				});				
				return false;
			}						
		});	
		
		return segment;
	},
	
	t.makeSegmented = function(){
		$('tbody td:not(:first-child)', t.table ).makeSegmented({'segments': t.segments, orientation: t.orientation});
	},
	
	//Creates the DOM element of the calendar
	t.draw = function(){
		switch (t.orientation)
		{
			case 'vertical':
				var top = t.rooms;
				var left = t.timeframes;
				break;
			case 'horizontal':
				var top = t.timeframes;
				var left = t.rooms;
				break;
			default:
				t.orientation = 'horizontal';
				var left = t.rooms;
				var top = t.timeframes;
				break;
		}
		var table = $('<table/>');
		var thead = $('<thead/>');
		var tr = $('<tr/>');
		
		var cell = $('<td/>');
		var segment;
		//Enter an empty cell that will create the first column
		$(tr).append(cell);
		//Populate the thead part of the calendar
		for (var i=0; i<top.length; i++)
		{			
			cell = $('<td/>');
			$(cell).text(top[i].title);
			$(tr).append(cell);
		}
		$(thead).append(tr);
		
		//Populate the tbody part of the calendar
		var tbody = $('<tbody/>');
		for (var i=0; i<left.length; i++)
		{
			tr = $('<tr/>');
			cell = $('<td/>').text(left[i].title);
			$(tr).append(cell);			
			for(var j=0; j<top.length; j++)
			{
				cell = $('<td/>');
				$(tr).append(cell);
			}
			$(tbody).append(tr);			
		}
		$(table).append(thead);
		$(table).append(tbody);
		$(table).css({width:'100%'}).addClass('styled');
		t.table = table;
		return table;
	};
	
	//Generates an object that contains the data needed to populate the 'timeframes' property of the 
	//calendar object
	t.generateTimeframes = function(obj)
	{

		//Keep the params in the calendar object so we can redraw the calendar based on previously set parameters
		t.initial_date = obj.initial_date||t.initial_date;
		t.columns = obj.columns||t.columns;
		t.step = obj.step||t.step;
		t.format = obj.format||t.format;
		
		//The date the timeframes bar starts from
		var start = new Date(t.initial_date.toString());
		var timeframes = Array();
		for(var i = 0; i<t.columns ; i++)
		{
			var o = {title: start.addMilliseconds(t.step).toString(t.format)};
			timeframes.push(o);
		}
		
		return timeframes;	
	};

}