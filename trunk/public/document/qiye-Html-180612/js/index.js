	
	$(function(){
		var li=$('.tab_t ul li'); 
		var li_c=$('.tab_c ul li'); 
		li.click(function(){ 
		$(this).addClass('selected').siblings().removeClass('selected'); 
		var j=$(this).index(); 
		li_c.eq(j).show().siblings().hide(); 
		}); 
		});  
	$(function(){ 
		var li_q=$('.banner_left_img ul li'); 
		var li_s=$('.content_right ul li'); 
		li_s.mouseenter(function(){ 
		$(this).addClass('select').siblings().removeClass('select'); 
		var d=$(this).index(); 
		li_q.eq(d).show().siblings().hide(); 
		}); 
	}); 
