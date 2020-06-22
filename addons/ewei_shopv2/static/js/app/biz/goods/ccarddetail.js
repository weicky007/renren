define(['core', 'tpl', 'biz/goods/picker', 'biz/plugin/ccarddiyform'], function (core, tpl, picker, diyform) {
    var modal = {};
    modal.init = function (params) {
        modal.goodsid = params.goodsid;
        modal.optionid = 0;
        modal.total = 1;
    };

    $('.buybtn').unbind('click').click(function () {
        if ($('.diyform-container').length > 0) {
            var diyformdata = diyform.getData('.diyform-container');
            if (!diyformdata) {
                return
            } else {
                core.json('order/create/diyform', {
                    id: modal.goodsid,
                    diyformdata: diyformdata
                }, function (ret) {
                    location.href = core.getUrl('order/create', {
                        id: modal.goodsid,
                        optionid: modal.optionid,
                        total: modal.total,
                        gdid: ret.result.goods_data_id
                    });
                }, true, true);
            }
        } else {
            location.href = core.getUrl('order/create', {
                id: modal.goodsid,
                optionid: modal.optionid,
                total: modal.total
            });
        }
    });

    return modal
});