alert('test');$.fn.makeSegmented=function(opt){var def={segments:4,orientation:'horizontal'};$.extend(def,opt);var segment;for(var i=0;i<def.segments;i++)
{segment=$('<div/>');if(def.orientation==='horizontal')
{$(segment).css({position:'relative',float:'left',width:Math.floor($(this).width()/def.segments),height:'80px'});}
else
{$(segment).css({position:'relative',width:$(this).width(),height:Math.floor($(this).height()/def.segments)});}
$(this).append(segment);}};function Calendar(){var t=this;t.rooms=Array();t.timeframes=Array();t.segments=4;t.table={};t.orientation='horizontal';t.getSegment=function(offset){var segment=null;$('tbody tr',t.table).each(function(){var _offset=$(this).offset();if((_offset.top<offset.top)&&((_offset.top+$(this).outerHeight())>offset.top))
{$('td',this).each(function(){var _offset=$(this).offset();if((_offset.left<=offset.left)&&(($(this).outerWidth()+_offset.left)>=offset.left))
{$('div',this).each(function(){var _offset=$(this).offset();if(t.orientation==='horizontal')
{if(_offset.left>=offset.left)
{segment=this;return false;}}
else
{if(_offset.top>=offset.top)
{segment=this;return false;}}});return false;}});return false;}});return segment;},t.makeSegmented=function(){$('tbody td:not(:first-child)',t.table).makeSegmented({'segments':t.segments,orientation:t.orientation});},t.draw=function(){switch(t.orientation)
{case'vertical':var top=t.rooms;var left=t.timeframes;break;case'horizontal':var top=t.timeframes;var left=t.rooms;break;default:t.orientation='horizontal';var left=t.rooms;var top=t.timeframes;break;}
var table=$('<table/>');var thead=$('<thead/>');var tr=$('<tr/>');var cell=$('<td/>');var segment;$(tr).append(cell);for(var i=0;i<top.length;i++)
{cell=$('<td/>');$(cell).text(top[i].title);$(tr).append(cell);}
$(thead).append(tr);var tbody=$('<tbody/>');for(var i=0;i<left.length;i++)
{tr=$('<tr/>');cell=$('<td/>').text(left[i].title);$(tr).append(cell);for(var j=0;j<top.length;j++)
{cell=$('<td/>');$(tr).append(cell);}
$(tbody).append(tr);}
$(table).append(thead);$(table).append(tbody);$(table).css({width:'100%'}).addClass('styled');t.table=table;return table;};t.generateTimeframes=function(obj)
{t.initial_date=obj.initial_date||t.initial_date;t.columns=obj.columns||t.columns;t.step=obj.step||t.step;t.format=obj.format||t.format;var start=new Date(t.initial_date.toString());var timeframes=Array();for(var i=0;i<t.columns;i++)
{var o={title:start.addMilliseconds(t.step).toString(t.format)};timeframes.push(o);}
return timeframes;};}