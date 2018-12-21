/*点击添加学员*/
jQuery(document).ready(function($){
        
		
		//打开窗口
        $('.cd-popup-trigger3').on('click', function(event){
            event.preventDefault();
            $('.cd-popup3').addClass('is-visible3');
            //$(".dialog-addquxiao").hide()
        });
        //关闭窗口
        $('.cd-popup3').on('click', function(event){
            if( $(event.target).is('.close') || $(event.target).is('.cd-popup3') ) {
                event.preventDefault();
                $(this).removeClass('is-visible3');
            }
        });
        //ESC关闭
        $(document).keyup(function(event){
            if(event.which=='27'){
                $('.cd-popup3').removeClass('is-visible3');
            }
        });
    });
    
/*$("#datetimepicker").on("click",function(e){
				e.stopPropagation();
				$(this).lqdatetimepicker({
					css : 'datetime-day',
					dateType : 'D',
					selectback : function(){

					}
		});时间选择器*/