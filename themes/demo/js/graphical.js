// This file was copied from a real application just to test the assets pipeline on javascript files.
// Can be safely removed anytime




//This variable will hold the instance of the calendar object prototype we are going to be using in this page
var calendar;
//We keep the datetime of the pageload in this object so we keep a common point of refference between calendars
var current_time = new Date().set({day: 1, hour: 0 ,minute:0});
//We make sure the Calendar namespace is set so we can add objects to it without overwriting it
var Calendar = Calendar||{};

Calendar.MINUTE = 1000 * 60;
Calendar.HOUR = Calendar.MINUTE * 60;
Calendar.DAY = Calendar.HOUR * 24;


Calendar.zoom_levels = [
                        {calendar: 'halfDay', format:time_format_js},
                        {calendar: 'day', format:time_format_js},
                        {calendar: 'twoDays', format:time_format_js},
                        {calendar: 'threeAndHalfDays',format:time_format_js},
                        {calendar: 'week',format:time_format_js},
                        {calendar: 'twoWeeks',format:time_format_js},
                        {calendar: 'month',format:time_format_js},                        
                        ];

Calendar.zoom_levels.index = 1;

console.log(Calendar.zoom_levels.index);


//We keep the drag options in this object so we can easily use them throughout the page
Calendar.drag_options = {					
	snap: 'tbody td:not(:first-child)>div',
	snapMode:'outer',
	stop: function(event, ui){
		if (calendar.orientation === 'horizontal')
		{
			var segment = calendar.getSegment({
				top : $(this).offset().top + ($(this).outerHeight() / 2), 
				left : $(this).offset().left
			});
		}
		else
		{
			var segment = calendar.getSegment({
				top : $(this).offset().top, 
				left : $(this).offset().left + ($(this).outerWidth() / 2)
			});
		}
		
		
		if(!!segment)
		{
			$(segment).append($(this).css({top:0, left:0}));
		}
		else
		{
			$(this).animate(ui.originalPosition);
		}
	}
};

Calendar.event_click = function (e){
	var	t = this;
	//If the event is being resized don't fire the click event
	if($(e.target).is('.ui-resizable-handle') || $(e.target).is('.ui-draggable-dragging')){
		return;
	}
	
	var bg = $('<div/>').css({
		position: 'absolute',
		top: 0,
		left: 0,
		height: $(document).height(),
		width: $(document).width(),
		background: '#000000',
		opacity: 0.1,
		'z-index': $.topZIndex()+1
	});
		
	var bubble = $('#bubble').show().css({'z-index':$.topZIndex()+1});
	$(bg).on('click', function(){$(this).off('click'); $(this).remove(); $(bubble).hide();});
	$('body').append(bg);
	$('body').append(bubble);
	
	$(bubble).css({
		position:'absolute',
		top: $(t).offset().top - ($(bubble).outerHeight() + 5),
		left: $(t).offset().left
	});		
};

//We keep the select options in this object so we can easily use them throughout the page
Calendar.select_options = {
	//All the td's of the tables body are selectable except the first of each th
	filter: 'tbody td:not(:first-child)>div',
	stop: function(e, ui){
		var start_segment = $('.ui-selected', this).get(0);
		var end_segment = $('.ui-selected', this).get(-1);
		//If there is already an event inside this segment, don't append a new event
		if(!$(start_segment).is(':empty')){return false;}
		
		//Clone the proper event DOM element from the 'elements bucket' we have on our HTML
		var event = (calendar.orientation === 'horizontal')?$($('#bucket .h_event').get(0)).clone():$($('#bucket .v_event').get(0)).clone();					
		event.click(Calendar.event_click);
		
		//Make the background opaque, assign it a random color and make it a background
		//The background class style is defined in the pages stylesheet
		$('.background',event).css({
			opacity: 0.5,
			'background-color': random_color('rgb'),
			'z-index': -1
		});
		
		//Make the event draggable
		$(event).draggable(Calendar.drag_options);
		
		//Make the event resizable according to it's orientation and give it's proper width and height as well
		if($(event).hasClass('h_event'))
		{
			var h_distance = ($(end_segment).offset().left + $(end_segment).outerWidth()) - $(start_segment).offset().left;
			$(event).resizable({handles:'e'});
			$(event).css({width: h_distance+'px', height:'inherit'});
		}
		else
		{
			var v_distance = ($(end_segment).offset().top + $(end_segment).outerHeight()) - $(start_segment).offset().top;
			$(event).resizable({handles:'s'});
			$(event).css({width:'inherit',height: v_distance+'px'});
		}
		
		$(start_segment).append(event).css({top:0, left:0});
	}
};

//The following three methods (day, week and month) are here and not in the calendar.js
//because they have page specific information and should be easily editable by the developer

//We extend the Calendar object to add the redraw function to it
Calendar.prototype.redraw = function(){
	$('#calendar_container').empty();
	var table = calendar.draw();
	$('#calendar_container').append(table);
	$( table ).selectable(Calendar.select_options);
	calendar.makeSegmented();			
};

//Generates a day calendar
Calendar.prototype.halfDay = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:12,
		step:Calendar.HOUR,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

//Generates a day calendar
Calendar.prototype.day = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:24,
		step:Calendar.HOUR,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};
Calendar.prototype.twoDays = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:24,
		step:Calendar.HOUR * 2,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

Calendar.prototype.threeAndHalfDays = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:7,
		step:Calendar.HOUR * 12,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

//Generates a week calendar
Calendar.prototype.week = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:7,
		step:Calendar.DAY,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

//Generates a week calendar
Calendar.prototype.twoWeeks = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:15,
		step:Calendar.DAY,
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

//Generates a month calendar
Calendar.prototype.month = function(){
	var today = new Date(current_time.toString());	
	this.segments = 4;
	this.timeframes = calendar.generateTimeframes({
		initial_date:today,
		columns:30, 
		step:Calendar.DAY, 
		format:date_format_js+' '+time_format_js
	});
	
	calendar.redraw();
};

$(function(){
	var loader = $('<div/>').addClass('loader big').css({width: $(document).width(), height: $(document).height()});
	$('body').append(loader);
	//Load the rooms from the server and draw the initial calendar
	$.post(base_url+'ajax/rooms', function(response){
		
		calendar = new Calendar();
		calendar.rooms = response;
		
		switch (get_anchor()) {
		case 'day':
			calendar.day();
			break;
		case 'week':
			calendar.week();
			break;
		case 'month':
			calendar.month();
			break;		
		default:
			calendar.day();
			break;
		}		
		$(loader).remove();
	},'json');
			
	
	$('#portrait').click(function(){
		calendar.orientation = 'vertical';				
		calendar.redraw();
	});
	$('#landscape').click(function(){
		calendar.orientation = 'horizontal';				
		calendar.redraw();
	});
	
	$('#zoom_in').click(function(){
		if(Calendar.zoom_levels.index == 0){
			return;
		}
		Calendar.zoom_levels.index--;
		$('#debug').text('Calendar being displayed '+Calendar.zoom_levels[Calendar.zoom_levels.index].calendar);
		eval('calendar.'+Calendar.zoom_levels[Calendar.zoom_levels.index].calendar+'()');				
	});
	
	$('#zoom_out').click(function(){
		if(Calendar.zoom_levels.index == (Calendar.zoom_levels.length-1)){
			return;
		}
		Calendar.zoom_levels.index++;
		$('#debug').text('Calendar being displayed '+Calendar.zoom_levels[Calendar.zoom_levels.index].calendar);
		eval('calendar.'+Calendar.zoom_levels[Calendar.zoom_levels.index].calendar+'()');
	});
	
	$('#previous_column').click(function(){
		calendar.timeframes = calendar.generateTimeframes({initial_date:current_time.previous()[calendar.step]()});
		calendar.redraw();
	});
	
	$('#next_column').click(function(){
		calendar.timeframes = calendar.generateTimeframes({initial_date:current_time.next()[calendar.step]()});
		calendar.redraw();
	});
	
	$('#day').click(function(){		
		calendar.day();
		for(var i in Calendar.zoom_levels)
		{
			if(Calendar.zoom_levels[i] === 'day'){
				Calendar.zoom_levels.index = i;
				break;
			}
		}
	});
	
	$('#week').click(function(){
		calendar.week();
		for(var i in Calendar.zoom_levels)
		{
			if(Calendar.zoom_levels[i] === 'week'){
				Calendar.zoom_levels.index = i;
				break;
			}
		}
	});
	
	$('#month').click(function(){
		calendar.month();
		for(var i in Calendar.zoom_levels)
		{
			if(Calendar.zoom_levels[i] === 'month'){
				Calendar.zoom_levels.index = i;
				break;
			}
		}
	});
});

