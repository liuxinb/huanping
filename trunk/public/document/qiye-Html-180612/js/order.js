/**
 * Created by Administrator on 2018/3/28.
 */

$(function() {
    $(".left .btn").click(function(){
        /*console.log(!$(this).hasClass("active"))
        console.log( $(this).parent().siblings().children("ul"))*/
        // alert(1)
        if(!$(this).hasClass("active")){
            $(".left .btn").removeClass("active");
            $(".left li").removeClass("active");
            $(this).siblings("ul").slideDown();
            $(this).addClass("active");
            $(this).parent().siblings().children("ul").slideUp()
        }else{
            /*console.log($(this).parent().siblings().children("ul"))*/

            $(this).siblings("ul").slideUp();
        }

    });

    $(".left ul>li").click(function(){
        $(".left ul>li").removeClass("active");
        $(this).addClass("active");
    });

  /* $.fn.menu = function(b) {
         var c,
             item,
             httpAdress;
         b = jQuery.extend({
                 Speed: 220,
                 autostart: 1,
                 autohide: 1
             },
             b);
         c = $(this);
         item = c.children("ul").parent("li").children("a");
         httpAdress = window.location;
         item.addClass("inactive");
         function _item() {
             var a = $(this);
             if (b.autohide) {
                 a.parent().parent().find(".active").parent("li").children("ul").slideUp(b.Speed / 1.2,
                     function() {
                         $(this).parent("li").children("a").removeAttr("class");
                         $(this).parent("li").children("a").attr("class", "inactive")
                     })
             }
             if (a.attr("class") == "inactive") {
                 a.parent("li").children("ul").slideDown(b.Speed,
                     function() {
                         a.removeAttr("class");
                         a.addClass("active")
                     })
             }
             if (a.attr("class") == "active") {
                 a.removeAttr("class");
                 a.addClass("inactive");
                 a.parent("li").children("ul").slideUp(b.Speed)
             }
         }
         item.unbind('click').click(_item);
         if (b.autostart) {
             c.children("a").each(function() {
                 if (this.href == httpAdress) {
                     $(this).parent("li").parent("ul").slideDown(b.Speed,
                         function() {
                             $(this).parent("li").children(".inactive").removeAttr("class");
                             $(this).parent("li").children("a").addClass("active")
                         })
                 }
             })
         }
    }*/
  $("#all").click(function(){
		if (this.checked) {
		$("#list :checkbox").prop("checked",true);
		} else{
		$("#list :checkbox").prop("checked",false);
		};
	});
});
