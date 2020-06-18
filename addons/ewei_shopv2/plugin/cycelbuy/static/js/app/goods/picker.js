define(["core","tpl","biz/member/cart","biz/plugin/diyform"],function(p,o,t,n){var m={goodsid:0,goods:[],option:!1,specs:[],options:[],params:{titles:"",optionthumb:"",split:";",option:!1,total:1,optionid:0,onSelected:!1,onConfirm:!1,autoClose:!0},ttime:"",open:function(t){if(m.params=$.extend(m.params,t||{}),m.goodsid!=t.goodsid||t.refresh){m.specs=[],m.options=[],m.option=!1,m.params.optionid=0,m.goodsid=t.goodsid;var e={id:t.goodsid};t.liveid&&(e.liveid=t.liveid),p.json("cycelbuy/goods/picker",e,function(t){if(0!=t.status){if(m.followtip="",m.followurl="",2==t.status)return m.followtip=t.result.followtip,m.followurl=t.result.followurl,void m.show();if(4==t.status)return m.followtip=0,m.needlogin=1,m.endtime=t.result.endtime||0,m.imgcode=t.result.imgcode||0,void m.show();if(3==t.status)return m.followtip=0,m.needlogin=0,m.mustbind=1,m.endtime=t.result.endtime||0,m.imgcode=t.result.imgcode||0,void m.show();if(5==t.status)return FoxUI.toast.show(t.result.message),void(m.goodsid="");var e=window.screen.width*window.devicePixelRatio,i=window.screen.height*window.devicePixelRatio;t.result.width=e,t.result.height=i,m.containerHTML=o("option-picker",t.result),m.goods=t.result.goods,m.specs=t.result.specs,m.options=t.result.options,m.seckillinfo=t.result.seckillinfo,""==m.goods.unit&&(m.goods.unit="件"),m.needlogin=0,m.followtip=0,m.mustbind=0,m.show()}else FoxUI.toast.show("未找到商品!")},!0,!1)}else m.show()},close:function(){m.container.close()},init:function(){m.tmonth=0,m.getDateList(),m.nowDate(),$(".other-time").click(function(){$(".cyclenotime").css("display","none"),$(".cyceltime").css("display","block"),$(".confirmbtn-date").css("display","table-cell"),$(".confirmbtn").css("display","none"),$(".cancelbtn").css("display"," table-cell")}),$(".confirmbtn-date").click(function(){$(".cyceltime").css("display","none"),$(".cyclenotime").css("display","block"),$(".cancelbtn").css("display"," none"),$(".confirmbtn-date").css("display","none"),$(".confirmbtn").css("display","table-cell");var t=$(".day.active").attr("data-id"),e=$(".day.active").attr("data-date");$(".spec-item-time").html(e).addClass("btn-danger").attr("data-date",t)}),$(".closebtn",m.container.container).unbind("click").click(function(){m.close()}),$(".cancelbtn",m.container.container).unbind("click").click(function(){$(".cyceltime").css("display","none"),$(".cyclenotime").css("display","block"),$(".cancelbtn").css("display"," none");var t=$(".spec-item-time").attr("data-date");$(".day").each(function(){$(this).data("id")==t&&($(this).addClass("active"),$(this).prevAll().removeClass("active"),$(this).nextAll().removeClass("active"))})}),$(".fui-mask").unbind("click").click(function(){m.close()}),0==m.seckillinfo?$(".fui-number",m.container.container).numbers({value:m.params.total,max:m.goods.maxbuy,min:m.goods.minbuy,minToast:"{min}"+m.goods.unit+"起售",maxToast:"最多购买{max}"+m.goods.unit,callback:function(t){m.params.total=t}}):m.params.total=1,$(".spec-item",m.container.container).unbind("click").click(function(){m.chooseSpec(this)}),$(".cartbtn",m.container.container).unbind("click").click(function(){m.addToCart()}),$(".buybtn",m.container.container).unbind("click").click(function(){if(!$(this).hasClass("disabled")&&m.check()){if(0<$(".diyform-container").length){var t=n.getData(".diyform-container");if(!t)return;p.json("cycelbuy/order/create/diyform",{id:m.goods.id,diyformdata:t},function(t){location.href=p.getUrl("cycelbuy/order/create",{id:m.goods.id,optionid:m.params.optionid,total:m.params.total,gdid:t.result.goods_data_id})},!0,!0)}else location.href=p.getUrl("cycelbuy/order/create",{id:m.goods.id,optionid:m.params.optionid,total:m.params.total});m.params.autoClose&&m.close()}}),$(".confirmbtn",m.container.container).unbind("click").click(function(){$(this).hasClass("disabled")||m.check()&&(location.href=p.getUrl("cycelbuy/order/create",{id:m.goods.id,optionid:m.params.optionid,total:m.params.total,predicttime:$(".spec-item-time").attr("data-date")}),m.params.autoClose&&m.close())});var t=.6*$(document.body).height(),e=t-$(".option-picker-cell").outerHeight()-$(".option-picker .fui-navbar").outerHeight();m.container.container.find(".option-picker").css("height",t),$(".date-picker").css("height","18rem"),m.container.container.find(".option-picker .option-picker-options").css("height",e);var i=document.documentElement.clientHeight||document.body.clientHeight;$(window).on("resize",function(){if((document.documentElement.clientHeight||document.body.clientHeight)<i){$(".fui-navbar").css({display:"none"}),$(".option-picker").css({height:"auto"});var t=(e=.6*$(document.body).height())-$(".option-picker-cell").outerHeight();m.container.container.find(".option-picker").css("height",e),m.container.container.find(".option-picker .option-picker-options").css("height",t),$(".option-picker").addClass("android")}else{$(".fui-navbar").css({display:"block"});var e;t=(e=.6*$(document.body).height())-$(".option-picker-cell").outerHeight()-$(".option-picker .fui-navbar").outerHeight();m.container.container.find(".option-picker").css("height",e),m.container.container.find(".option-picker .option-picker-options").css("height",t),$(".option-picker").addClass("android")}})},nowDate:function(){var t=new Date;t.getFullYear(),t.getMonth(),t.getDate(),t.getDay();var i=0,e=$(".ahead_goods").html();$.ajax({url:p.getUrl("cycelbuy/trade/picker/getDayNum"),type:"get",data:{year:t.getFullYear(),month:t.getMonth()},async:!1,success:function(t){var e=JSON.parse(t);i=e.num}});var o=parseInt(t.getDate())+parseInt(e);if(i<o){if(11<(c=Math.round(parseInt(t.getMonth())+o/i)))var n=parseInt(t.getFullYear())+1;else n=t.getFullYear();var a=o%i,s=new Date(n,c,a).getDay()}else{n=t.getFullYear();var c=t.getMonth();a=o,s=new Date(n,c,o).getDay()}switch(s){case 0:var r="周日";break;case 1:r="周一";break;case 2:r="周二";break;case 3:r="周三";break;case 4:r="周四";break;case 5:r="周五";break;case 6:r="周六"}c=parseInt(c+1)<10?"0"+parseInt(c+1):parseInt(c+1);var l=parseInt(a)<10?"0"+parseInt(a):parseInt(a),d=n+"年"+c+"月"+l+"日 "+r;$(".spec-item-time").html(d),$(".spec-item-time").attr("data-date",String(n)+String(c)+String(l)),m.ttime=t},show:function(){if(m.followtip)FoxUI.confirm(m.followtip,function(){""!=m.followurl&&null!=m.followurl&&(location.href=m.followurl)});else{if(m.needlogin){var e=p.getUrl("cycelbuy/goods/detail",{id:m.goodsid});return e=e.replace("./index.php?",""),void require(["biz/member/account"],function(t){t.initQuick({action:"login",backurl:btoa(e),endtime:m.endtime,imgcode:m.imgcode,success:function(){var t=m.params;t.refresh=!0,m.open(t)}})})}if(m.mustbind)require(["biz/member/account"],function(t){t.initQuick({action:"bind",backurl:btoa(location.href),endtime:m.endtime,imgcode:m.imgcode,success:function(){var t=m.params;t.refresh=!0,m.open(t)}})});else{if(m.container=new FoxUIModal({content:m.containerHTML,extraClass:"picker-modal"}),m.init(),m.seckillinfo&&0==m.seckillinfo.status&&($(".fui-mask").hide(),$(".picker-modal").hide(),(void 0===m.options.length||m.options.length<=0)&&$(".diyform-container").length<=0))return"buy"==m.params.action?void(location.href=p.getUrl("cycelbuy/order/create",{id:m.goods.id,total:1,optionid:0})):void m.addToCart();$(".fui-mask").show(),$(".picker-modal").show(),m.params.showConfirm?$(".confirmbtn",m.container.container).show():($(".buybtn",m.container.container).show(),m.goods.canAddCart&&$(".cartbtn",m.container.container).show()),"0"!=m.params.optionid&&m.initOption(),m.container.show(),1==m.specs.length&&$.each(m.options,function(){var t=this.specs;0==this.stock&&$(".spec-item"+t).removeClass("spec-item").removeClass("btn-danger").addClass("disabled").off("click")})}}},initOption:function(){$(".spec-item").removeClass("btn-danger");var t=m.params.optionid,i=!1;if($.each(m.options,function(){if(this.id==t)return i=this.specs.split("_"),!1}),i){var o=[];if($(".spec-item").each(function(){var t=$(this),e=t.data("id");$.each(i,function(){this==e&&(o.push(t),t.addClass("btn-danger"))})}),0<o.length){var e=o[o.length-1];m.chooseSpec(e,!1)}}},chooseSpec:function(t,e){var n=$(t);n.closest(".spec").find(".spec-item").removeClass("btn-danger"),n.addClass("btn-danger");var i=n.data("thumb")||"";i&&$(".thumb",m.container.container).attr("src",i),m.params.optionthumb=i;var a=$(".spec-item.btn-danger",m.container.container),o=[];a.length<=m.specs.length&&$.each(m.options,function(){if(m.specs.length-a.length==1){var t=[],e=this.specs;if($.each(a,function(){0<=e.indexOf(this.getAttribute("data-id"))&&t.push(this.getAttribute("data-id"))}),t.length==a.length){for(var i=0;i<t.length;i++)e=e.replace(t[i],"");e=e.split("_");var o=[];$.each(e,function(t,e){var i=$.trim(e);""!=i&&o.push(i)}),this.stock<=0&&-1!=this.stock?$(".spec-item"+o[0]).removeClass("spec-item").removeClass("btn-danger").addClass("disabled").off("click"):$(".spec-item"+o[0]).removeClass("disabled").addClass("spec-item").off("click").on("click",function(){m.chooseSpec(this)})}}else if(m.specs.length==a.length){t=[],e=this.specs;$.each(a,function(){0<=e.indexOf(this.getAttribute("data-id"))&&0<=e.indexOf(n.data("id"))&&t.push(this.getAttribute("data-id"))});o=[];if(t.length==m.specs.length-1){for(i=0;i<t.length;i++)e=e.replace(t[i],"");e=e.split("_"),$.each(e,function(t,e){var i=$.trim(e);""!=i&&o.push(i)}),this.stock<=0&&-1!=this.stock?$(".spec-item"+o[0]).removeClass("spec-item").removeClass("btn-danger").addClass("disabled").off("click"):$(".spec-item"+o[0]).removeClass("disabled").addClass("spec-item").off("click").on("click",function(){m.chooseSpec(this)})}}}),a.length==m.specs.length&&(a.each(function(){o.push($(this).data("id"))}),$.each(m.options,function(){if(this.specs.split("_").sort().join("_")==o.sort().join("_")){var t="-1"==this.stock?"无限":this.stock;$(".total",m.container.container).html(t),"-1"!=this.stock&&this.stock<=0?($(".confirmbtn",m.container).show().addClass("disabled").html("库存不足"),$(".cartbtn,.buybtn",m.container).hide()):m.params.showConfirm?($(".confirmbtn",m.container).removeClass("disabled").html("确定"),$(".cartbtn,.buybtn",m.container).hide()):($(".cartbtn,.buybtn",m.container).show(),$(".confirmbtn").hide());var e=Date.parse(new Date)/1e3;0<m.goods.ispresell&&(0==m.goods.preselltimeend||m.goods.preselltimeend>e)?($(".price",m.container.container).html(this.presellprice),0<this.seecommission&&($(".option-Commission").addClass("show"),$(".option-Commission span",m.container.container).html(this.seecommission))):($(".price",m.container.container).html(this.marketprice),0<this.seecommission&&($(".price",m.container.container).html(this.marketprice),$(".option-Commission").addClass("show"),$(".option-Commission span",m.container.container).html(this.seecommission))),m.option=this,m.params.optionid=this.id}}));var s=[];a.each(function(){$(this)[0]!=$(".spec-item-time")[0]&&s.push($.trim($(this).html()))}),m.params.titles=s.join(m.params.split),$(".info-titles",m.container.container).html("已选 "+m.params.titles),e&&m.params.onSelected&&m.params.onSelected(m.params.total,m.params.optionid,m.params.titles)},check:function(){var t=$(".spec",m.container.container),e=!0;if(t.each(function(){if($(this).find(".spec-item.btn-danger").length<=0)return FoxUI.toast.show("请选择"+$(this).find(".title").html()),e=!1}),e){if(-1!=m.option.stock&&m.option.stock<=0)return FoxUI.toast.show("库存不足"),!1;var i=parseInt($(".num",m.container.container).val());return i<=0&&(i=1),i>m.option.stock&&(i=m.option.stock),$(".num",m.container.container).val(i),0<m.goods.maxbuy&&i>m.goods.maxbuy?(FoxUI.toast.show("最多购买 "+m.goods.maxbuy+" "+m.goods.unit),!1):!(0<m.goods.minbuy&&i<m.goods.minbuy)||(FoxUI.toast.show(m.goods.minbuy+m.goods.unit+"起售"),!1)}return!1},changeCartcount:function(t){if(0<$("#menucart").length){var e=$("#menucart").find(".badge");e.length<1?$("#menucart").append('<span class="badge">'+t+"</div>"):e.text(t)}},getDateList:function(){$(".ahead_goods").html();$.ajax({url:p.getUrl("cycelbuy/trade/picker/date_list"),type:"get",data:{ttime:m.ttime,tmonth:m.tmonth,tdate:m.tdate,from:"create"},success:function(t){$("#datepicker").html(t),$("#date"+m.tdate).removeClass("active").addClass("active"),$(".date-alert").addClass("show"),$("#month-left").click(function(){m.tmonth--,m.getDateList()}),$("#month-right").click(function(){m.tmonth++,m.getDateList()}),$(".day").unbind("click").click(function(){if("1"!=$(this).attr("data-status")){$(".spec-item .btn-danger").attr("data-day");m.tdate=$(this).attr("data-id");var t=$(this).attr("data-date");$("#date").html(t),$("#datepicker").html(),$(".day_item").removeClass("active"),$(this).addClass("active"),$(this).prevAll().removeClass("active"),$(this).nextAll().removeClass("active")}})}})}};return m});