<?php defined('IN_IA') or exit('Access Denied');?>    <?php  if(is_array($nav_top_tiled_other)) { foreach($nav_top_tiled_other as $other) { ?>
    <?php  if($other['is_system']) { ?><?php  continue;?><?php  } ?>
    <li ><a href="<?php  echo $other['url'];?>" target="_blank"><i class="<?php  echo $other['icon'];?>"></i><span><?php  echo $other['title'];?></span></a></li>
    <?php  } } ?>
    <?php  if($_W['iscontroller'] && $_W['isadmin']) { ?>
        <li><a href="<?php  echo $_W['siteroot'] . 'web/home.php'?>" ><i class="wi wi-caozuotai"></i><span>客户端</span></a></li>
    <?php  } else { ?>
    <?php  if($_W['isadmin']) { ?>
        <li ><a href="<?php  echo url('account/manage', array('iscontroller' => 1))?>"><i class="wi wi-kongzhitai"></i><span>控制台</span></a></li>
    <?php  } ?>
    <?php  } ?>
    <?php  if(is_array($nav_top_tiled_other)) { foreach($nav_top_tiled_other as $other) { ?>
    <?php  if(!$other['is_system']) { ?><?php  continue;?><?php  } ?>
    <?php  if($other['name'] == 'workorder' && (!permission_check_account_user('see_workorder') || !$_W['iscontroller'])) { ?><?php  continue;?><?php  } ?>
    <?php  if($other['name'] == 'store' && $_W['iscontroller']) { ?><?php  continue;?><?php  } ?>
    <li class="js-header-user" data-toggle="tooltip" data-placement="bottom" title="<?php  echo $other['title'];?>">
        <a href="<?php  echo $other['url'];?>"><i class="<?php  echo $other['icon'];?>"></i></a>
    </li>
    <?php  } } ?>

    <?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-notice', TEMPLATE_INCLUDEPATH)) : (include template('common/header-notice', TEMPLATE_INCLUDEPATH));?>
    
    <!-- 用户信息 -->
    <li class="dropdown user" >
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" ><i class="wi wi-user"></i><span class="caret"></span></a>
        <ul class="dropdown-menu color-gray dropdown-menu-right" role="menu">
            <li>
                <a href="<?php  echo url('user/profile');?><?php  if(!$_W['iscontroller']) { ?>iscontroller=0<?php  } ?>" target="_blank">用户名： <?php  echo $_W['username'];?></a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="<?php  echo url('user/profile');?><?php  if(!$_W['iscontroller']) { ?>iscontroller=0<?php  } ?>" target="_blank"><i class="wi wi-money color-gray"></i> 我的账号</a>
            </li>
            <li class="divider"></li>

            <?php  if(permission_check_account_user('see_system_upgrade') && $_W['iscontroller']) { ?>
            <li><a href="<?php  echo url('cloud/upgrade');?>" target="_blank"><i class="wi wi-update color-gray"></i> 自动更新</a></li>
            <?php  } ?>
            <?php  if(user_is_vice_founder($_W['uid'])) { ?>
            <li><a href="<?php  echo url('account/manage', array('iscontroller' => 1))?>" ><i class="wi wi-user color-gray"></i> 副站长控制台</a></li>
            <?php  } ?>
            <li><a href="#" id="updateCache"><i class="wi wi-cache color-gray"></i> 更新缓存</a></li>
            <li class="divider"></li>

            <li>
                <a href="<?php  echo url('user/logout');?>"><i class="fa fa-sign-out color-gray"></i> 退出系统</a>
            </li>
        </ul>
    </li>
    <!-- end用户信息 -->

    <li class="dropdown-star <?php  if(!in_array(FRAME, array('account', 'wxapp')) || 'store' == $_GPC['m']) { ?>hidden<?php  } ?>">
        <a href="javascript:;" class="star-info">
            <img src="<?php  if(defined('IN_MODULE')) { ?><?php  echo $_W['current_module']['logo'];?><?php  } else { ?><?php  echo $_W['account']['logo'];?><?php  } ?>" class="account-img" alt="">
            <span class="name text-over"><?php  if(defined('IN_MODULE')) { ?><?php  echo $_W['current_module']['title'];?><?php  } else { ?><?php  echo $_W['account']['name'];?><?php  } ?></span>
            <span class="caret"></span>
        </a>
        <div class="star-box">
            <div class="star-box__header">
                切换平台/应用
                <ul class="nav nav-tabs pull-right" role="tablist">
                    <li role="presentation" class="" data-toggle="tooltip" data-placement="bottom" title="我的星标"><a href="#star-star" data-star="star" aria-controls="star-star" role="tab" data-toggle="tab"><i class="wi wi-star"></i></a></li>
                    <li role="presentation" data-toggle="tooltip" data-placement="bottom" title="历史查看"><a href="#star-history" data-star="history" aria-controls="star-history" role="tab" data-toggle="tab"><i class="wi wi-waiting"></i></a></li>
                    <?php  if(ACCOUNT_MANAGE_NAME_CLERK != $_W['highest_role']) { ?><li role="presentation" data-toggle="tooltip" data-placement="bottom" title="所有平台"><a href="#star-platform" data-star="platform" aria-controls="star-platform" role="tab" data-toggle="tab"><i class="wi wi-platform"></i></a></li><?php  } ?>
                    <li role="presentation" data-toggle="tooltip" data-placement="bottom" title="所有应用"><a href="#star-modules" data-star="modules" aria-controls="star-modules" role="tab" data-toggle="tab"><i class="wi wi-apply"></i></a></li>
                </ul>
            </div>
            <div class="tab-content star-box__content">
                <div role="tabpanel" class="tab-pane" id="star-star"></div>
                <div role="tabpanel" class="tab-pane" id="star-history">2</div>
                <div role="tabpanel" class="tab-pane" id="star-platform">3</div>
                <div role="tabpanel" class="tab-pane" id="star-modules">4</div>
            </div>
        </div>
    </li>

    <script>
        $(function() {
            // 加border
            if($('.js-header-user').length && $($('.js-header-user')[0]).prev().length) {
                $($('.js-header-user')[0]).prev().after('<li><a class="header-user-border"></a></li>')
            }
            $('.dropdown-star .star-info').click(function(e) {
                $('.dropdown-star').toggleClass('active')
            })
            $('.dropdown').on('show.bs.dropdown', function () {
                $('.dropdown-star').removeClass('active')
            })
            $(document).click(function(e){
                if($(e.target).parents(".dropdown-star").length == 0){
                    $('.dropdown-star').removeClass('active')
                }
            });
            $('#updateCache').on('click', function(){
                $('.loader').show();
                $.post('./index.php?c=system&a=updatecache&do=updatecache', {}, function(data) {
                    $('.loader').hide();
                        util.message('更新缓存成功！', '', 'success');
                })
            });
            test = function(e) {
                console.log(e)
                return true;
            }
            $('.dropdown-star [data-toggle="tab"]').on('show.bs.tab', function (e) {
                console.log(e)
                var star = $(e.target).data('star')
                window.localStorage.setItem('we7StarHeader', $(e.target).data('star'))
                var menuList = {
                    star: {
                        apiUrl: './index.php?c=account&a=display&do=list_star&',
                        name: '星标',
                        url: './home.php#/mystar'
                    },
                    history: {
                        apiUrl: './index.php?c=account&a=display&do=history&',
                        name: '历史',
                        url: './home.php#/history'
                    },
                    platform: {
                        apiUrl: './index.php?c=account&a=display&do=list&type=all',
                        name: '平台',
                        url: './home.php#/platform'
                    },
                    modules: {
                        apiUrl: './index.php?c=module&a=display&do=own&',
                        name: '应用',
                        url: './home.php#/modules'
                    }
                }
                $('#star-' + star).html('<div class="loading star-header-list"><i class="fa fa-spinner fa-spin"></i>加载中</div>')
                $.get(menuList[star]['apiUrl'], {
                    limit_num: 5,
                    page: 1
                } , function(res) {
                    if(res.message && res.message.errno == 0) {
                        let html = '<div class="star-header-list">'
                        for(var i in res.message.message) {
                            var item = res.message.message[i];
                            html = html + '<a href="' + item.switchurl + '" class="star-header-item star-item--account">' +
                                    '<img src="'+ item.logo +'"  alt="" class="star-header-item__logo ' + (item.list_type == 'account' ? 'account-img' : 'module-img') + '"/>' +
                                    '<div class="star-header-item__info">' +
                                        '<div class="star-header-item__name text-over">' + (item.list_type == 'account' ? item.name : item.title) + '</div>' +
                                        '<div class="star-header-item__desc text-over">' + (item.list_type == 'account' ? (item.type_name + (item.level ? ('/' + item.level) : '')) : ('所属平台：' + (item.default_account ? item.default_account.name : ''))) + '</div>' +
                                    '</div>' +
                                    '<div class="star-header-item__go">' +
                                        '<i class="wi wi-angle-right"></i>' +
                                    '</div>' +
                                '</a>'
                        }
                        html = html + '</div>' +
                            '<div class="star-go"><a href="'+ menuList[star]['url'] + '">查看全部' + menuList[star]['name'] + '</a></div>'
                        $('#star-' + star).html(html)
                    }
                }, 'json')
            })
            var headerStar = window.localStorage.getItem('we7StarHeader');
            if(!headerStar) {
                headerStar = 'star';
            }
            if($('.dropdown-star').length && !$('.dropdown-star').hasClass('.hide')) {
                $('[data-star="' + headerStar + '"]').tab('show')
            }
        })
    </script>
</ul>