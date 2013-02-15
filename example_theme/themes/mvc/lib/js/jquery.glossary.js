;(function($){var _settings={descriptionClass:"glossary-description"}
$.fn.glossify=function(settings)
{settings=_settings;for(var i=0;i<this.length;i++)
{var g=this[i];var $g=$(g);g.glossTitle=g.title;g.title="";var $subElement=$g.find('acronym, abbr');if($subElement.length){caption=$subElement[0].title;$subElement[0].title="";}
else
caption=$g.text();g.$glossElement=makeGloss(settings,caption,g.glossTitle);}
this.mouseover(function glossover(ev){this.$glossElement.show();followTheMouse(ev,this.$glossElement);});this.mouseout(function(){this.$glossElement.hide();});this.mousemove(followTheMouse);}
function makeGloss(settings,caption,text)
{var $div=$('<div><strong/><span/></div>').css('position','absolute').addClass(settings.descriptionClass).hide();$div.find('strong').text(caption);$div.find('span').text(text);$('body').append($div);return $div;}
function followTheMouse(ev,$el)
{var $el=$el?$el:this.$glossElement
var rx=ev.clientX;var ry=ev.clientY;var x=ev.pageX;var y=ev.pageY;var xOffset=25;var yOffset=25;if((rx+$el.width()+xOffset)>$(window).width())
{x-=$el.width();xOffset*=-1;}
if((ry+$el.height()+yOffset)>$(window).height())
{y-=$el.height();yOffset*=-1;}
$el.css("left",x+xOffset);$el.css("top",y+yOffset);}})(jQuery);