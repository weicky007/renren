define(['core', 'tpl'], function (core, tpl) {
    var modal = {
        wsClient: false,
        wsConfig: {},
        wsConnected: false,
        wsBanned: {all: false, self: false},
        status: 0,
        realOnline: 0,
        showOnline: 0,
        wsCanAt: false,
        wsCanRepeal: false,
        msgAt: {},
        lastliketime: 0,
        inputTip: ['跟大家说点什么吧...', '点击键盘回车也可发送信息哦~']
    };
    modal.init = function (params) {
        modal.wsConfig = params.wsConfig || {};
        modal.roomid = modal.wsConfig.roomcount;
        modal.initWs();
        modal.initClick();
        modal.initPlayer();
        setInterval(function () {
            var tipIndex = Math.floor(Math.random() * 3);
            if (tipIndex == 2 && !modal.wsCanAt) {
                tipIndex == 1
            }
            $('#input').attr('placeholder', modal.inputTip[tipIndex]);
            if (modal.wsConnected) {
                modal.wsSend('communication', {toUser: 'system'});
                modal.clickLike()
            }
        }, 8000);
        $(".roomcoupon").off("click").on("click", function () {
            var _this = $(this);
            var roomid = parseInt(_this.attr("data-roomid"));
            var couponid = parseInt(_this.attr("data-couponid"));
            var livetime = parseInt(_this.attr("data-livetime"));
            var disabled = _this.hasClass("disabled") ? false : true;
            if (disabled) {
                core.json('live/room/roomcoupon', {
                    roomid: roomid,
                    livetime: livetime,
                    couponid: couponid
                }, function (ret) {
                    var result = ret.result;
                    if (ret.status < -1) {
                        _this.addClass("disabled").find(".live-mask").text(result.message);
                        return
                    } else if (ret.status >= -1 && ret.status <= 0) {
                        FoxUI.toast.show(result.message)
                    } else {
                        FoxUI.toast.show('优惠券已领取，请到我的优惠券中查看')
                    }
                })
            }
        })
    };
    modal.initPlayer = function () {
        if (modal.wsConfig.isMobile && modal.wsConfig.isIos) {
            var playerHeight = $('body').width() * 0.56;
            if (!$('.fui-content').hasClass('fullscreen')) {
                $('.block-video').css('height', playerHeight + 'px');
                $('.block-content').css('top', playerHeight + $('.block-tab').height() + 'px')
            }
        }
        var player = $('#player')[0];
        if (!modal.wsConfig.isMobile || modal.wsConfig.isIos) {
            return
        }
        player.addEventListener("x5videoenterfullscreen", function () {
            $('.fui-content').addClass('player-fullscreen');
            var playerHeight = $('body').width() * 0.56;
            if (!$('.fui-content').hasClass('fullscreen')) {
                $('.block-tab').css('top', $('.block-title').height() + playerHeight + 'px');
                $('.block-content').css('top', $('.block-title').height() + playerHeight + $('.block-tab').height() + 'px')
            }
        });
        player.addEventListener("x5videoexitfullscreen", function () {
            var playerHeight = $('body').width() * 0.56;
            $('.block-tab').css('top', playerHeight + 'px');
            $('.block-content').css('top', playerHeight + $('.block-tab').height() + 'px');
            $('.fui-content').removeClass('player-fullscreen')
        })
    };
    modal.initWs = function () {
        if (!modal.wsConfig) {
            modal.liveMsg('notice', '通讯服务器配置错误');
            return
        } else {
            $('.block-input .input-place').html('初始化通讯服务...').show().siblings().hide();
        }
        var wsConfig = modal.wsConfig;
        var wsClient = new WebSocket(wsConfig.address);
        wsClient.onopen = function () {
            modal.wsSend('login', {toUser: 'system'})
        };
        wsClient.onmessage = function (evt) {
            var data = JSON.parse(evt.data);
            if (data.type == 'connected') {
                FoxUI.toast.show('连接成功');
                $('.block-input .input-place').html('').hide().siblings().show();
                modal.wsConnected = true;
                modal.wsBanned = data.banned;
                if (data.banned.all == 1) {
                    modal.liveMsg('notice', '管理员禁止任何人发言')
                } else if (data.banned.self != '') {
                    modal.liveMsg('notice', '你被管理员禁止发言')
                }
                modal.status = data.settings.status;
                modal.wsCanAt = data.settings.canat == 1 ? true : false;
                modal.wsCanRepeal = data.settings.canrepeal == 1 ? true : false;
                modal.realOnline = data.online || 0;
                modal.showOnline = data.settings.virtual || 0;
                modal.initStatus();
                modal.initOnline();
                if (modal.wsCanAt) {
                    modal.inputTip.push('点击蓝色昵称可@Ta')
                } else {
                    modal.inputTip.splice(2, 1)
                }
                if (modal.wsCanRepeal) {
                    $('.btn-repeal').addClass('show')
                } else {
                    $('.btn-repeal').removeClass('show')
                }
            } else if (data.type == 'notice') {
                modal.liveMsg('notice', data.text)
            } else if (data.type == 'setting') {
                var settings = data.settings;
                if (!settings) {
                    return
                }
                if (modal.status != settings.status) {
                    modal.status = settings.status;
                    modal.initStatus()
                }
                if (modal.showOnline != settings.virtual) {
                    modal.showOnline = settings.virtual;
                    modal.initOnline()
                }
                modal.wsCanAt = settings.canat == 1 ? true : false;
                modal.wsCanRepeal = settings.canrepeal == 1 ? true : false;
                if (modal.wsCanAt) {
                    modal.inputTip.push('点击蓝色昵称可@Ta')
                } else {
                    modal.inputTip.splice(2, 1)
                }
                if (modal.wsCanRepeal) {
                    $('.btn-repeal').addClass('show')
                } else {
                    $('.btn-repeal').removeClass('show')
                }
                if (settings.nickname_old) {
                    modal.liveMsg('notice', '管理员"' + settings.nickname_old + '"更名为"' + settings.nickname + '"')
                }
            } else if (data.type == 'userEnter') {
                if (data.role != 'manage') {
                    modal.liveMsg('notice', data.nickname + ' 进入直播间！掌声欢迎~')
                }
                modal.realOnline++;
                modal.initOnline()
            } else if (data.type == 'userLeave') {
                modal.realOnline--;
                modal.initOnline()
            } else if (data.type == 'text' || data.type == 'sent') {
                modal.liveMsg('text', data)
            } else if (data.type == 'image') {
                modal.liveMsg('image', data)
            } else if (data.type == 'repeal') {
                if (data.msgid) {
                    var text = '"' + data.nickname + '"';
                    if (data.fromUser == modal.wsConfig.uid) {
                        text = '你'
                    }
                    var fullscreen = '';
                    if (modal.wsConfig.fullscreen) {
                        fullscreen += 'nopadding'
                    }
                    $('.tab-content .msg[data-msgid="' + data.msgid + '"]').addClass(fullscreen).html('<div class="tip"><div class="text">' + text + '撤回了一条消息</div></div></div>')
                }
            } else if (data.type == 'delete') {
                if (data.msgid) {
                    var text = '"' + data.deleteNick + '"撤回了一条消息';
                    if (data.deleteUid == modal.wsConfig.uid) {
                        text = '管理员"' + data.nickname + '"删除了你的一条消息'
                    }
                    var fullscreen = '';
                    if (modal.wsConfig.fullscreen) {
                        fullscreen += 'nopadding'
                    }
                    $('.tab-content .msg[data-msgid="' + data.msgid + '"]').addClass(fullscreen).html('<div class="tip"><div class="text">' + text + '</div></div></div>')
                }
            } else if (data.type == 'banned') {
                if (data.banned == 1) {
                    modal.wsBanned.self = true;
                    modal.liveMsg('notice', '你被管理员禁止发言');
                    $('.btn-send').removeClass('active')
                } else {
                    if (modal.wsBanned.all == '') {
                        var value = $.trim($('#input').val());
                        if (value) {
                            $('.btn-send').addClass('active')
                        }
                    }
                    modal.wsBanned.self = false;
                    modal.liveMsg('notice', '你被管理员允许发言')
                }
            } else if (data.type == 'bannedAll') {
                if (data.banned == 1) {
                    $('.btn-send').removeClass('active');
                    modal.wsBanned.all = true;
                    modal.liveMsg('notice', '管理员禁止任何人发言');
                    $('.btn-send').removeClass('active')
                } else {
                    if (modal.wsBanned.self == '') {
                        var value = $.trim($('#input').val());
                        if (value) {
                            $('.btn-send').addClass('active')
                        }
                    }
                    modal.wsBanned.all = false;
                    modal.liveMsg('notice', '管理员解除全体禁言')
                }
            } else if (data.type == 'clicklike') {
                modal.clickLike()
            } else if (data.type == 'redpack') {
                modal.liveMsg('redpack', data)
            } else if (data.type == 'redpackget') {
                if (data.prestatus == 0) {
                    FoxUI.toast.show('获取失败')
                } else if (data.prestatus == 1) {
                    $('.layer-mask').fadeIn(200);
                    $('.layer-redpack').addClass('in open')
                } else if (data.prestatus == 2) {
                    $('.layer-redpack .redpack-info .price').addClass('small').html('手速慢太慢了，没抢到..');
                    $('.layer-mask').fadeIn(200);
                    $('.layer-redpack').addClass('in open')
                } else if (data.prestatus == 3) {
                    $('.layer-mask').fadeIn(200);
                    $('.layer-redpack').removeClass('open').addClass('in')
                }
                FoxUI.loader.hide();
                $(document).find('.redpack[data-pushid="' + data.redpackid + '"]').removeClass('stop')
            } else if (data.type == 'redpackdraw') {
                if (data.status == 0) {
                    setTimeout(function () {
                        $('.layer-redpack').removeClass('in');
                        $('.layer-mask').fadeOut(200);
                        FoxUI.loader.show('领取失败', 'icon icon-cry');
                        $('.layer-redpack .redpack-draw').removeClass('rotate');
                        setTimeout(function () {
                            FoxUI.loader.hide()
                        }, 1500)
                    }, 1500)
                } else if (data.status == 1 || data.status == 3) {
                    setTimeout(function () {
                        $('.layer-mask').fadeIn(200);
                        $('.layer-redpack').addClass('in open');
                        $('.layer-redpack .redpack-draw').removeClass('rotate')
                    }, 1500)
                } else if (data.status == 2) {
                    setTimeout(function () {
                        $('.layer-mask').fadeIn(200);
                        $('.layer-redpack').addClass('in open');
                        $('.layer-redpack .redpack-draw').removeClass('rotate')
                    }, 1500)
                }
                $('.layer-redpack').removeClass('stop')
            }

            setTimeout(function () {
                modal.scrollBottom();
            }, 10);
        };
        wsClient.onclose = function (evt) {
            if (!modal.wsConnected) {
                return
            }
            $('.block-input .input-place').html('与通讯服务器断开 <a class="btn-reconnect">点击重连</a>').show().siblings().hide();
            modal.wsConnected = false
        };
        wsClient.onerror = function (evt) {
            $('.block-input .input-place').html('与通讯服务器连接失败 <a class="btn-reconnect"> 点击重连</a>').show().siblings().hide();
            modal.wsConnected = false
        };
        modal.wsClient = wsClient
    };
    modal.wsSend = function (type, obj) {
        if (!type || $.isEmptyObject(obj)) {
            return false
        }
        if (type != 'login') {
            if (!modal.wsConnected) {
                FoxUI.toast.show('通讯服务器连接失败');
                return false
            }
            if (type != 'redpackget' && type != 'redpackdraw' && type != 'communication') {
                if (modal.wsBanned.all == 1) {
                    FoxUI.toast.show('管理员禁止任何人发言');
                    return false
                }
                if (modal.wsBanned.self != '') {
                    FoxUI.toast.show('你被管理员禁止发言');
                    return false
                }
            }
        }
        var wsConfig = modal.wsConfig;
        obj.type = type;
        obj.scene = 'live';
        obj.roomid = wsConfig.roomid;
        obj.uniacid = wsConfig.uniacid;
        obj.uid = wsConfig.uid;
        obj.nickname = wsConfig.nickname;
        if (!$.isEmptyObject(modal.msgAt)) {
            obj.at = modal.msgAt
        }
        modal.wsClient.send(JSON.stringify(obj));
        return obj
    };
    modal.liveMsg = function (type, obj) {
        var atText = '', fullscreen = '';
        if (obj.atUsers && !$.isEmptyObject(obj.atUsers)) {
            $.each(obj.atUsers, function (uid, nickname) {
                atText += '<span class="nickname';
                if (uid == modal.wsConfig.uid) {
                    atText += ' self', nickname = '你';
                    modal.liveAt(obj.nickname, obj.msgid)
                }
                atText += '" data-uid="' + uid + '" data-nickname="' + nickname + '">@' + nickname + '</span> '
            })
        }
        if (type == 'image') {
            obj.text = modal.tomedia(obj.text);
            obj.text = '<img src="' + obj.text + '" />'
        } else if (type == 'text' && obj.text) {
            if (obj.text.indexOf("[") > -1 && obj.text.indexOf("]") > -1) {
                var res = obj.text.match(/\[([^\]]+)\]/g);
                if (res) {
                    $.each(res, function (index, val) {
                        var text = val.replace('[', '');
                        text = text.replace(']', '');
                        var elm = $('.block-emoji .item[title="' + text + '"]');
                        if (elm.length > 0) {
                            obj.text = obj.text.replace(val, elm.html())
                        }
                    })
                }
            }
        } else if (type == 'redpack') {
            obj.text = '<div class="redpack" data-pushid="' + obj.redpack.id + '">红包来袭，手慢无！快抢</div>';
            if (modal.wsConfig.fullscreen) {
                fullscreen += 'nopadding'
            }
        }
        var html = '';
        html += '<div data-msgid="' + obj.msgid + '" class="msg ' + fullscreen;
        if (type == 'notice') {
            html += ' notice'
        }
        if (obj.self) {
            html += ' self'
        }
        html += '">';
        if (type == 'notice') {
            html += '系统提醒：' + obj
        } else {
            if (obj.self) {
                obj.nickname += '(你)';
                if (modal.wsCanRepeal) {
                    obj.text += '<span class="btn-repeal"> 撤回</span>'
                }
            }
            if (type != 'redpack' || !modal.wsConfig.fullscreen) {
                html += '<div class="nickname ';
                if (obj.self) {
                    html += ' self'
                }
                html += '" data-uid="' + obj.fromUser + '" data-nickname="' + obj.nickname + '">' + obj.nickname + '：</div>'
            }
            html += '<div class="content">' + atText + obj.text + '</div>'
        }
        html += '</div>';
        $('.tab-content[data-tab="chat"]').append(html);
        if (type == 'redpack') {
            $('.block-content .msg[data-msgid="' + obj.msgid + '"]').find('.redpack').click()
        }
        modal.scrollBottom()
    };
    modal.liveAt = function (nickname, msgid) {
        if (!nickname || !msgid) {
            return
        }
        var elm = $('.layer-at');
        if (elm.hasClass('in')) {
            clearTimeout(modal.liveAtEnd)
        }
        $('.layer-at .at-text').text(nickname + '@了你');
        $('.layer-at').addClass('in').data('msgid', msgid);
        modal.liveAtEnd = setTimeout(function () {
            elm.removeClass('in').data('msgid', 0).find('.at-text').text('')
        }, 10000)
    };
    modal.initStatus = function () {
        if (modal.status == 1) {
            $('.live-tips.play').show().siblings('.live-tips').hide()
        } else if (modal.status == 2) {
            $('.live-tips.pause').show().siblings('.live-tips').hide();
            $('#player')[0].pause()
        } else {
            $('.live-tips.stop').show().siblings('.live-tips').hide();
            $('#player')[0].pause()
        }
    };
    modal.initOnline = function () {
        var online = parseInt(modal.realOnline) + parseInt(modal.showOnline);
        if (online > 10000) {
            online = (online / 10000).toFixed(2);
            online += '万'
        }
        $('#online').text(online)
    };
    modal.initVideo = function (status) {
        if (status == 'pause') {
            alert('暂停直播/显示暂停提示')
        } else if (status == 'stop') {
            alert('停止直播/显示直播未开始提示')
        } else {
            alert('开始直播')
        }
    };
    modal.initClick = function () {
        $(document).on('click', '.btn-reconnect', function () {
            if (modal.wsConnected) {
                FoxUI.toast.show('当前已连接，如还提示请刷新');
                return
            }
            modal.initWs()
        });
        $('.block-tab a').click(function () {
            var tab = $(this).data('tab');
            $(this).addClass('active').siblings().removeClass('active');
            $('.block-content .tab-content[data-tab="' + tab + '"]').show().siblings('.tab-content').hide()
        });
        $('.btn-play').click(function () {
            var url = $('#player').attr('src');
            if (url == '') {
                FoxUI.toast.show('视频获取失败或未设置');
                return
            }
            $('#player')[0].play();
            $('.live-tips').hide()
        });
        $('#player')[0].addEventListener("ended", function () {
            $('.live-tips.play').show().siblings('.live-tips').hide()
        });
        $('#input').focus(function () {
            $('body').animate({scrollTop: "10000px"}, 500);
            $('.block-input').addClass('focus')
        });
        $('#input').blur(function () {
            if ($('.fui-content').hasClass('show-emoji')) {
                return
            }
            $('.block-input').removeClass('focus')
        });
        $('#input').keydown(function (event) {
            if (event.keyCode == 8) {
                var textValue = '';
                var textObj = $(this).get(0);
                if (textObj.setSelectionRange) {
                    var rangeStart = textObj.selectionStart;
                    var rangeEnd = textObj.selectionEnd;
                    var delValue = textObj.value.substring(rangeStart - 1, rangeStart);
                    var tempStr1 = textObj.value.substring(0, rangeStart - 1);
                    var tempStr2 = textObj.value.substring(rangeEnd);
                    textValue = tempStr1 + tempStr2;
                    if (delValue == "]" && tempStr1.indexOf("[") > -1) {
                        var res = tempStr1.match(/(\[[\u4E00-\u9FA5]*)$/g);
                        textValue = tempStr1.substring(0, tempStr1.lastIndexOf("[")) + tempStr2
                    } else if (delValue == " " && tempStr1.indexOf("@") > -1) {
                        textValue = tempStr1.substring(0, tempStr1.lastIndexOf("@")) + tempStr2;
                        modal.msgAt = {}
                    }
                    textObj.value = textValue;
                    textObj.focus();
                    textObj.setSelectionRange(rangeStart - 1, rangeStart - 1);
                    return false
                } else {
                    return true
                }
            } else if (event.keyCode == 13) {
                var value = $.trim($("#input").val());
                if (!value) {
                    FoxUI.toast.show('不能发送空消息');
                    return
                }
                var msg = modal.wsSend('text', {toUser: 'all', text: value});
                if (msg) {
                    $(this).removeClass('active');
                    $("#input").val('');
                    modal.msgAt = {}
                }
                $('.fui-content').removeClass('show-emoji')
            }
        });
        $('#input').on('input propertychange, change', function () {
            var value = $.trim($(this).val());
            if (value != '' && modal.wsConnected && modal.wsBanned.all != 1 && modal.wsBanned.self != 1) {
                $(".btn-send").addClass('active')
            } else {
                $(".btn-send").removeClass('active')
            }
        });
        $(document).on('click', '.block-content .msg .btn-repeal', function () {
            if (!modal.wsCanRepeal) {
                FoxUI.toast.show('管理员禁止撤回消息')
            }
            var msgid = $(this).closest('.msg').data('msgid');
            FoxUI.confirm('确定要撤回此条消息吗？', function () {
                modal.wsSend('repeal', {toUser: 'system', msgid: msgid})
            })
        });
        $(document).on('click', '.block-content .msg .nickname', function () {
            if (!modal.wsCanAt) {
                FoxUI.toast.show('管理员禁止@用户');
                return
            }
            if (!$.isEmptyObject(modal.msgAt)) {
                FoxUI.toast.show('每次只能@一位用户');
                return
            }
            var nickname = $.trim($(this).data('nickname'));
            var uid = $(this).data('uid');
            if ($(this).hasClass('self')) {
                FoxUI.toast.show('你不能@自己');
                return
            }
            modal.msgAt[uid] = nickname;
            modal.insertAtCaret('#input', "@" + nickname + ": ")
        });
        $('.btn-emoji').click(function () {
            if ($('.fui-content').hasClass('show-emoji')) {
                $('.block-input').removeClass('focus')
            } else {
                $('.block-input').addClass('focus')
            }
            $('.fui-content').toggleClass('show-emoji');
            $(this).toggleClass('active');
            modal.scrollBottom()
        });
        $(".block-emoji .item").click(function () {
            var id = $(this).attr('title');
            modal.insertAtCaret('#input', '[' + id + ']')
        });
        $('.btn-like').click(function () {
            if (!modal.wsConnected) {
                return
            }
            modal.clickLike();
            var time = new Date().getTime();
            if (modal.lastliketime + 10000 >= time) {
                return
            }
            modal.lastliketime = time;
            modal.wsSend('clicklike', {toUser: 'system'})
        });
        $('.btn-send').on('touchstart', function () {
            var value = $.trim($("#input").val());
            if (!value) {
                FoxUI.toast.show('不能发送空消息');
                return
            }
            var msg = modal.wsSend('text', {toUser: 'all', text: value});
            if (msg) {
                $(this).removeClass('active');
                $("#input").val('');
                modal.msgAt = {}
            }
            $('.fui-content').removeClass('show-emoji');
            $('.block-input .input .btn-emoji').removeClass('active')
        });
        $('.layer-roominfo .room-btn').click(function () {
            var _this = $(this);
            var roomid = _this.data("roomid");
            core.json('live/room/favorite', {'roomid': roomid}, function (ret) {
                if (ret.status == 0) {
                    FoxUI.loader.show(ret.result.message, 'icon icon-cry');
                    setTimeout(function () {
                        FoxUI.loader.hide()
                    }, 1000);
                    return
                }
                if (ret.result.favorite == 0) {
                    $('.btn-favorite').removeClass('disabled').text('订阅');
                    _this.removeClass('disabled').text('点击订阅');
                    FoxUI.loader.show('取消订阅成功', 'icon icon-check')
                } else {
                    $('.btn-favorite').addClass('disabled').text('取消');
                    _this.addClass('disabled').text('取消订阅');
                    FoxUI.loader.show('订阅成功', 'icon icon-check')
                }
                setTimeout(function () {
                    FoxUI.loader.hide()
                }, 1000)
            }, true, true)
        });
        $('.layer .layer-close').click(function () {
            var layer = $(this).closest('.layer');
            $('.layer-mask').fadeOut(200);
            if (layer.hasClass('in')) {
                layer.removeClass('in');
                if (layer.hasClass('layer-at')) {
                    clearTimeout(modal.liveAtEnd)
                }
            } else {
                $(this).closest('.layer').fadeOut(200)
            }
            $('.layer-mask').fadeOut(200)
        });
        $('.live-info').click(function () {
            $('.layer-mask').fadeIn(200);
            $('.layer-roominfo').addClass('in')
        });
        $(document).on('click', '.block-content .msg .redpack', function () {
            var title = $.trim($(this).text());
            var pushid = $(this).data('pushid');
            if (pushid == '') {
                FoxUI.toast.show('参数错误');
                return
            }
            if ($(this).hasClass('stop')) {
                return
            }
            $('.layer-redpack').attr('data-pushid', pushid);
            $(this).addClass('stop');
            FoxUI.loader.show('loading');
            modal.wsSend('redpackget', {toUser: 'system', pushid: pushid, openid: modal.wsConfig.openid});
            $('.layer-redpack .redpack-title').text(title)
        });
        $('.btn-link').click(function () {
            var url = $(this).data('url');
            if ($('#liveframe').length < 1) {
                $('.fui-content').append('<iframe id="liveframe"></iframe>')
            }
            $('#liveframe').attr('src', url).show()
        });
        $('.layer-redpack .redpack-draw').click(function () {
            if (!modal.wsConnected) {
                FoxUI.toast.show('通讯服务器连接失败');
                return false
            }
            var _this = $(this), redpack = $(this).closest('.layer-redpack');
            if (_this.hasClass('rotate')) {
                return
            }
            var pushid = redpack.data('pushid');
            if (pushid == '') {
                FoxUI.toast.show('参数错误');
                return
            }
            redpack.attr('data-pushid', pushid);
            _this.addClass('rotate');
            redpack.addClass('stop');
            modal.wsSend('redpackdraw', {toUser: 'system', pushid: pushid, openid: modal.wsConfig.openid})
        });
        $('.btn-goods').click(function () {
            $('.layer-mask').fadeIn(200);
            $('.layer-goods .inner').css('height', $('.layer-goods').height() + 'px');
            $('.layer-goods').show().addClass('in')
        });
        $('.btn-gifts').click(function () {
            $('.layer-mask').fadeIn(200);
            $('.layer-gifts').addClass('in')
        });
        $(document).click(function (e) {
            var input = $(e.target).closest('.block-input').length;
            var emoji = $(e.target).closest('.block-emoji').length;
            if (emoji < 1 && input < 1) {
                $('.fui-content').removeClass('show-emoji');
                $('.block-input .input .btn-emoji').removeClass('active')
            }
        })
    };
    modal.clickLike = function () {
        var colors = ['#ffc510', '#ff4a4a', '#ff9141', '#fb7c63', '#05e0e8', '#24ec79', '#50b7ff', '#b9f110', '#59e4b5', '#fe76e9', '#b976fe', '#fea2d0', '#918eff'],
            cindex = Math.floor(Math.random() * 12);
        var icons = ['icon-aixin1', 'icon-gouwudai', 'icon-yifuicon122438', 'icon-juzi', 'icon-liwu', 'icon-aixin', 'icon-flower1', 'icon-shuiguo', 'icon-kafei'],
            iindex = Math.floor(Math.random() * 8);
        var x = 100, y = 400;
        var rand = parseInt(Math.random() * (x - y + 1) + y);
        $('.fui-content').append('<div class="ico-like" style="color: ' + colors[cindex] + ';"><i class="icon ' + icons[iindex] + '"></i></div>');
        $(".ico-like").animate({bottom: "800px", opacity: "0", right: rand,}, 3000, null, function () {
            $(this).remove()
        })
    };
    modal.scrollBottom = function () {
        var elm = $('.tab-content[data-tab="chat"]');
        var scrollHeight = elm[0].scrollHeight;
        elm.stop(true).animate({scrollTop: scrollHeight + "px"}, 100)
    };
    modal.tomedia = function (src) {
        if (typeof src != 'string') {
            return ''
        }
        if (src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/ewei_shopv2/') == 0) {
            return src
        } else if (src.indexOf('images/') == 0 || src.indexOf('audios/') == 0) {
            return modal.wsConfig.attachurl + src
        }
    };
    modal.insertAtCaret = function (elm, textFeildValue) {
        var textObj = $(elm).get(0);
        if (document.all && textObj.createTextRange && textObj.caretPos) {
            var caretPos = textObj.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? textFeildValue + '' : textFeildValue
        } else if (textObj.setSelectionRange) {
            var rangeStart = textObj.selectionStart;
            var rangeEnd = textObj.selectionEnd;
            var tempStr1 = textObj.value.substring(0, rangeStart);
            var tempStr2 = textObj.value.substring(rangeEnd);
            textObj.value = tempStr1 + textFeildValue + tempStr2;
            textObj.focus();
            var len = textFeildValue.length;
            textObj.setSelectionRange(rangeStart + len, rangeStart + len);
            textObj.blur()
        } else {
            textObj.value += textFeildValue
        }
        $(elm).trigger('change')
    };
    return modal
});