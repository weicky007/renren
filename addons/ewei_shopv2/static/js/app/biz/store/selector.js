define(['core', 'tpl', 'https://api.map.baidu.com/getscript?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&services=&t=20170324173232'], function (core, tpl) {
    var modal = {page: 1, loaded: false};
    modal.init = function (type, ids, merchid) {

        modal.type = type;
        modal.ids = ids;
        modal.merchid = merchid;
        modal.isneer = 0;
        $('.container').empty();
        $('.fui-content').infinite({

            onLoading: function () {
                if (modal.isneer) {
                    modal.getList(modal.lng, modal.lat)
                } else {
                    modal.getList()
                }

            }
        });

        modal.page = 1;
        if ($(".container .store-item").length <= 0) {
            $(".container").html('');
            modal.getList();
        }


        window.HOST_TYPE = "2";
        window.BMap_loadScriptTime = (new Date).getTime();
        modal.bindEvents();
        if (typeof(window.selectedStoreData) !== 'undefined') {
            $(".store-item .fui-list-media i").removeClass('selected');
            $(".store-item[data-storeid='" + window.selectedStoreData.id + "'] .fui-list-media i").addClass('selected')
        }
        $('.fui-searchbar input').bind('keyup', function (e) {
            if (e.keyCode===13){
                var val = $.trim($(this).val());
                if (val == '') {
                    $('.store-item').show()
                } else {
                    modal.page = 1;
                    modal.getList(modal.lng, modal.lat,val);
                }
            }
        });
        $('.fui-searchbar .searchbar-cancel').click(function () {
            $('.fui-searchbar input').val(''), $('.store-item').show(), $('.content-empty').hide()
        });


        $("#btn-near").click(function () {
            modal.page = 1;
            FoxUI.loader.show('正在定位...', 'icon icon-location');
            $('.fui-searchbar input').val(''), $('.store-item').show(), $('.content-empty').hide();
            var arr = [];
            var lat = "";
            var lng = "";
            /*高德地图定位*/
            var map = new AMap.Map('amap-container');
            map.plugin('AMap.Geolocation', function() {
                var geolocation = new AMap.Geolocation({
                    enableHighAccuracy: true,//是否使用高精度定位，默认:true
                    timeout: 5000,          //超过10秒后停止定位，默认：5s
                    maximumAge: 0,        //定位结果缓存0毫秒，默认：0(10min)
                });
                geolocation.getCurrentPosition(function(status,result) {
                    if (status == 'complete') {
                        lat = result.position.lat;
                        lng = result.position.lng;
                        var lang = modal.TxMapToBdMap(lat,lng);

                        modal.lat = lang.lat;
                        modal.lng = lang.lng;
                        modal.isneer = 1;
                        $('.container').empty();
                        modal.getList(lng, lat);

                    } else {
                        /*FoxUI.toast.show("位置获取失败!"+result.message);
                        return*/
                        /*百度地图定位*/
                        var geoLocation = new BMap.Geolocation();
                        geoLocation.getCurrentPosition(function (result) {
                            if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                                lat = result.point.lat;
                                lng = result.point.lng;
                                modal.lat = lat;
                                modal.lng = lng;
                                modal.isneer = 1;
                                $('.container').empty();
                                modal.getList(lng, lat);



                            } else {
                                FoxUI.loader.hide();
                                FoxUI.toast.show("位置获取失败!");
                                return
                            }
                        }, {enableHighAccuracy: true});
                    }
                });
            });

        });
        $(".icon-xiangqing-copy").click(function () {
            var address = $(this).closest(".fui-list").data("address");
            var realname = $(this).closest(".fui-list").data("realname");
            var mobile = $(this).closest(".fui-list").data("mobile");
            var map = $(this).closest(".fui-list").data("map");
            var storename = $(this).closest(".fui-list").data("storename");
            $("#shopmask").find(".shopmask-title").html(storename);
            $("#shopmask").find(".address").find("div").html(address);
            $("#shopmask").find(".address").find("a").attr("href", map);
            $("#shopmask").find(".mobile").find("div").html(mobile);
            $("#shopmask").find(".mobile").closest("a").attr("href", "tel:" + mobile);
            $("#shopmask").find(".realname").find("div").html(realname);
            $("#shopmask").css("display", "block")
        });
        $(".shopmask-bottom").click(function () {
            $("#shopmask").css("display", "none")
        })
    };

    modal.getList = function (lng, lat,keyword) {
        FoxUI.loader.hide();
        keyword = keyword || '';
        core.json('store/selector/get_list', {page: modal.page, type: modal.type, ids: modal.ids, isneer: modal.isneer, lng: lng, lat: lat,keyword:keyword, merchid: modal.merchid}, function (ret) {

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

            result.isneer = modal.isneer;

            core.tpl('.container', 'tpl_store_list', result, modal.page > 1);
            if(result.list.length >= result.pagesize) {
                modal.page++;
            }
            modal.bindEvents();
        })
    };
    // modal.sortneer = function (lng, lat) {
    //     var arr = [];
    //     $('.store-item').each(function () {
    //         var location = $(this).find('.location');
    //         var store_lng = $(this).data('lng'), store_lat = $(this).data('lat');
    //         if (store_lng > 0 && store_lat > 0) {
    //             var distance = core.getDistanceByLnglat(lng, lat, store_lng, store_lat);
    //             $(this).data('distance', distance);
    //             location.html('距离您: ' + distance.toFixed(2) + "km").show();
    //             location.parent("div").find("i").css("display", "block")
    //         } else {
    //             $(this).data('distance', 999999999999999999);
    //             location.html('无法获得距离').show()
    //         }
    //         arr.push($(this))
    //     });
    //     arr.sort(function (a, b) {
    //         return a.data('distance') - b.data('distance')
    //     });
    //     $.each(arr, function () {
    //         $('.fui-list-group').append(this)
    //     });
    //     FoxUI.loader.hide()
    // }
    modal.bindEvents = function () {
        $('.store-item .fui-list-media,.store-item .fui-list-inner').unbind('click').click(function () {
            var $this = $(this).parent();
            window.selectedStoreData = {
                'id': $this.data('storeid'),
                'storename': $this.find('.storename').html(),
                'realname': $this.find('.realname').html(),
                'mobile': $this.find('.mobile').html(),
                'address': $this.find('.address').html()
            };
            history.back()
        })
    };
    modal.click = function () {
    };

    modal.TxMapToBdMap = function (gg_lat, gg_lon) {
        var point = new Object();
        var x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        var x = new Number(gg_lon);
        var y = new Number(gg_lat);
        var z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
        var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
        var bd_lon = z * Math.cos(theta) + 0.0065;
        var bd_lat = z * Math.sin(theta) + 0.006;
        point.lng = bd_lon;
        point.lat = bd_lat;
        return point
    };

    return modal
});