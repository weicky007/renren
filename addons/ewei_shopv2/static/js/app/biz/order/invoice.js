define([],function(){var n={type:0,tpl:{entity:!1,company:!1,title:!1,number:!1},open:function(e,i,t){n.type=t||0,n.render(e,i)},default:function(e){return void 0===e&&(e=n.tpl),void 0===e.entity&&(e.entity=n.tpl.entity),void 0===e.company&&(e.company=n.tpl.company),void 0===e.title&&(e.title=n.tpl.title),void 0===e.number&&(e.number=n.tpl.number),e},render:function(e,i){e=n.default(e);n.container=new FoxUIModal({content:'<div class="invoice-picker"style="bottom: 0;position: absolute;padding-top: 20px;color: #333;width: 100%;background: #fff;font-family: \'微软雅黑\', \'Microsoft Yahei\';height: 410px"><style>.invoice-picker div{margin-left: 4%;line-height: 2.4rem;width: 92%;}.invoice-picker div:not(.title){border-bottom: 1px solid #efefef;} .invoice-picker .title{line-height: 1rem;margin-top: 10px}.invoice-picker label.active{background: #ff5555;color: #fff;border: 1px solid #ff5555;}</style><div class="title">发票材质</div><div style="padding-bottom: 10px"><label style="display:inline-block;line-height: 30px;border-radius: 20px;border: #e3e3e3 1px solid;width: 100px;text-align: center;margin-right: 10px" id="entity0"><input type="radio" name="invoice_entity" value="0" style="display: none">电子发票</label><label style="display:inline-block;line-height: 30px;border-radius: 20px;border: #e3e3e3 1px solid;width: 100px;text-align: center;margin-right: 10px" id="entity1"><input type="radio" name="invoice_entity" value="1" style="display: none">纸质发票</label></div><div class="title">发票类型</div><div style="padding-bottom: 10px"><label style="display:inline-block;line-height: 30px;border-radius: 20px;border: #e3e3e3 1px solid;width: 100px;text-align: center;margin-right: 10px"><input type="radio" name="invoice_company" value="0" style="display: none">个人</label><label style="display:inline-block;line-height: 30px;border-radius: 20px;border: #e3e3e3 1px solid;width: 100px;text-align: center;margin-right: 10px"><input type="radio" name="invoice_company" value="1" style="display: none">单位</label></div><div><span style="width: 30%">发票抬头</span><input type="text" style="height: 1.8rem;border: 0;width: 65%;margin-left: 5%;font-size: 0.8rem; font-family: \'微软雅黑\';" name="invoice_title"></div><div id="invoice_number" style="display: none"><span style="width: 30%">纳税人识别号</span><input type="text" style="height: 1.8rem;border: 0;width: 65%;margin-left: 5%;font-size: 0.8rem; font-family: \'微软雅黑\';" name="invoice_number"></div><a href="javascript:;" class="btn btn-danger" style="position: absolute;bottom: 20px;width: 94%;border-radius: 100px;" id="confirm-invoice">确定</a></div>',extraClass:"picker-modal",maskClick:n.close}),2==n.type||(1==n.type?$("#entity1").hide():$("#entity0").hide()),n.active(e,1),n.listen(i)},close:function(){$(".invoice-picker").fadeOut(100).remove(),n.container.close(),$("#invoice_number").hide(),$(".fui-modal").remove()},active:function(e,i){1===i?(e.entity||0===n.type?$('input[name=invoice_entity][value="1"]').attr("checked","true").parent().addClass("active"):$('input[name=invoice_entity][value="0"]').attr("checked","true").parent().addClass("active"),e.company?$('input[name=invoice_company][value="1"]').attr("checked","true").parent().addClass("active"):$('input[name=invoice_company][value="0"]').attr("checked","true").parent().addClass("active"),e.title&&$("input[name=invoice_title]").val(e.title),e.number&&($("input[name=invoice_number]").val(e.number),$("#invoice_number").show())):($("input[name="+i+"]").parent().removeClass("active"),e.parent().addClass("active"),"invoice_company"===i&&"1"===e.val()?$("#invoice_number").show():"invoice_company"===i&&$("#invoice_number").hide())},listen:function(i){$(document).on("change","input[name=invoice_entity]",function(){var e=$(this);n.active(e,"invoice_entity")}),$(document).on("change","input[name=invoice_company]",function(){var e=$(this);n.active(e,"invoice_company")}),$(document).on("click","#confirm-invoice",function(){var e={};e.entity="1"==$("input[name=invoice_entity]:checked").val(),e.company="1"==$("input[name=invoice_company]:checked").val(),e.title=$("input[name=invoice_title]").val().replace(/[' ']/g,"")||!1,e.number=$("input[name=invoice_number]").val().replace(/[' ']/g,"")||!1,e.company||(e.number=!1),e.title?0<e.title.indexOf(" ")||e.number&&0<e.number.indexOf(" ")?FoxUI.toast.show("发票信息不能包含空格"):!e.company||e.number?(n.close(),i(e)):FoxUI.toast.show("请填写纳税人识别号"):FoxUI.toast.show("请填写发票抬头")}),n.listen=function(){return!1}}};return n});