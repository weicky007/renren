define(['core', 'tpl', 'foxui.picker'], function (core, tpl) {
    var modal = {
        params: {
            needrealname: 0,
            needmobile: 0,
            needsmscode: 0,
            needsbirthday: 0,
            needsidnumber: 0,
            card_id: "",
            encrypt_code: ""
        }
    };
    modal.init = function (params) {
        modal.params = params;
        $('#birthday').datePicker();
        $('#btnSubmit').click(function () {

            if (modal.params.needrealname == 1 &&($('#realname').val()== undefined || $('#realname').val()== '')) {
                FoxUI.toast.show('请填写真实姓名');
                return
            }

            if (modal.params.needmobile == 1&& !$('#mobile').isMobile() ) {
                FoxUI.toast.show('请输入11位手机号码');
                return
            }

            if (modal.params.needsidnumber == 1&&!$('#idnumber').isIDCard()) {
                FoxUI.toast.show('请填写正确身份证号码');
                return
            }


            var birthyear =0;
            var birthmonth =0;
            var birthday =0;

            //备注
            if($('#birthday').val()!= undefined && $('#birthday').val()!= '' && modal.params.needsbirthday == 1)
            {
                var birthday = $('#birthday').val().split('-');
                birthyear = birthday[0];
                birthmonth =  birthday[1];
                birthday =  birthday[2];
            }

            var postdata = {
                card_id: modal.params.card_id,
                encrypt_code: modal.params.encrypt_code,
                realname: $('#realname').val(),
                birthyear: birthyear,
                birthmonth: birthmonth,
                birthday: birthday,
                idnumber: $('#idnumber').val(),
                mobile: $('#mobile').val(),
                sms_code: $('#sms_code').val()
            };

            core.json('member/activation/submit', postdata, function (ret) {
                if (ret.status != 1) {
                    FoxUI.toast.show(ret.result.message);
                    $('#btnSubmit').html('立即激活').removeAttr('stop');
                    return
                } else {
                    location.href = core.getUrl('member/activation/success')
                }
            }, false, true)
        });
        $('#btnCode').click(function () {

            if ($('#btnCode').hasClass('disabled')) {
                return
            }
            if (!$('#mobile').isMobile()) {
                FoxUI.toast.show('请输入11位手机号码');
                return
            }
            modal.seconds = 60;
            core.json('member/activation/verifycode', {mobile: $('#mobile').val(),}, function (ret) {
                FoxUI.toast.show(ret.result.message);
                if (ret.status != 1) {
                    $('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
                }
                if (ret.status == 1) {
                    modal.verifycode()
                }
            }, false, true)
        })
    };
    modal.verifycode = function () {
        modal.seconds--;
        if (modal.seconds > 0) {
            $('#btnCode').html(modal.seconds + '秒后重发').addClass('disabled').attr('disabled', 'disabled');
            setTimeout(function () {
                modal.verifycode()
            }, 1000)
        } else {
            $('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
        }
    };
    return modal
});