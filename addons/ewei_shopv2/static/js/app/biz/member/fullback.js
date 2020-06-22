define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, type: 0};
    modal.init = function (params) {
        modal.type = params.type;
        modal.orderid = params.orderid;

        FoxUI.tab({
            container: $('#tab'), handlers: {
                tab1: function () {
                    modal.changeTab(0)
                }, tab2: function () {
                    modal.changeTab(1)
                }
            }
        });
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.changeTab = function (type) {
        $('.container').html(''), $('.infinite-loading').show(), $('.content-empty').hide(), modal.page = 1,modal.type = type, modal.getList()
    };
    modal.getList = function () {
        core.json('member/fullback/get_list', {page: modal.page, type: modal.type ,orderid: modal.orderid}, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $('.container').hide();
                $('.content-empty').show();
                $('.fui-content').infinite('stop')
            } else {
                $('.container').show();
                $('.content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('.container', 'tpl_member_log_list', result, modal.page > 1);
            $(".fullback-item").off("click").on("click", function () {
                if ($(this).next().height() == 0) {
                    $(this).next().height("auto");
                    $(this).find(".remark").addClass("active")
                } else {
                    $(this).next().height("0");
                    $(this).find(".remark").removeClass("active")
                }
            })

        })
    };
    return modal
});