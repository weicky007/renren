
function YDBOBJ() {
	this.YundabaoUA = navigator.userAgent.toLowerCase();
	this.isIos = this.YundabaoUA.match(/(iphone|ipod|ipad);?/i);
	this.isAndroid = this.YundabaoUA.match(/android/i);
	this.isWindows = this.YundabaoUA.match(/windows/i);
	this.siteUrl=core.options.siteUrl;
	
};
YDBOBJ.prototype.ExitApp = function() {
	
	 var Share='ExitApp()';
   api.execScript({
					script:Share
				});
	
	/*api.closeWidget({
         silent : true
        });*/
};


YDBOBJ.prototype.isWXAppInstalled = function(installstate) {
	
};


YDBOBJ.prototype.SetStatusBarStyle = function(colorvalue) 
{
	


};





YDBOBJ.prototype.Share = function(title, content, img, linkUrl) {
   var  config={'apiKey':'','apiSecret':''};
   
   var shaer_confing={
        rect: {
            h: 180
        },
       texts:{
         cancel: '取消'
    },
      items: [{
            text: '微信好友',
            icon: 'widget://image/weifriend.png'
        }, {
            text: '微信朋友圈 ',
            icon: 'widget://image/share_to_icon_wxq.png'
        }
		],
        styles: {
             bg:'#FFF',
        column: 4,
        itemText: {
            color: '#000',
            size: 15,
            marginT:4
        },
        itemIcon:{
            size:54
        },
        cancel:{  
            bg: 'fs://icon.png',   
            color:'#000',          
            h: 68 ,                 
            size: 21      
          }
        },
        tapClose: true
    };
	
	
   var Share='Share(\''+title+'\',\''+content+'\',\''+img+'\',\''+linkUrl+'\','+JSON.stringify(config)+' ,'+JSON.stringify(shaer_confing)+')';
   api.execScript({
					script:Share
				});

};




YDBOBJ.prototype.ClearCache = function() {
	
	 var Share='ClearCache()';
   api.execScript({
					script:Share
				});

};



YDBOBJ.prototype.SetWxpayInfo = function(ProductName, Desicript, Price, OuttradeNo, attach) {

		var  config={'apiKey':'wxa2e1d545cb2aca53','mchId':'1519772201','partnerKey':'35e04e751c9be301f9a7612dc7099192','notifyUrl':this.siteUrl+'addons/ewei_shopv2/payment/wechat/notify.php'};

   var Share='SetWxpayInfo(\''+ProductName+'\',\''+Desicript+'\',\''+Price+'\',\''+OuttradeNo+'\',\''+attach+'\','+JSON.stringify(config)+')';
   api.execScript({
					script:Share
				});


	
};

YDBOBJ.prototype.SetRSA2AlipayInfo = function()
{
};
//YDBOBJ.prototype.SetAlipayInfo = function(ProductName, Desicript, Price, OuttradeNo) {
//	
//
//	
// core.json('appaccount/alipay', {ProductName:ProductName,Desicript:Desicript,OuttradeNo:OuttradeNo,Price:Price}, function(ret) {
//var Share='payOrder('+JSON.stringify(ret)+')';
// api.execScript({
//					script:Share
//				});
//	
// 
//
//	 }, true);
//};

YDBOBJ.prototype.SetAlipayInfo = function(ProductName, Desicript, Price, OuttradeNo) {
var  config={'partner':'2088002048040679','seller':'18052788111','rsaPriKey':'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCTeV2jZxJZY+2uQIleNW7RZCXkFGYcKE6ihdwrfs38SXS9VmbUfoREKQm7PQQjeP8U4sRDiZAeUuEJcfr5c50Mb19IwAnIphuTKz3IGebAcCbJjA6+TgOpTIMDYfW3nlrNOaI2Bk1ZYilJM6XQCtor1IgQucL7LdUl5/jnw2srehEW8pBy5n17WKbA83yxLaGmLaifwtUD/vhwS7vF1NUswzYB+a9TSehQrf319H07V6xnS4AWpDUVVjHo/jH0WYdYpBYDq9XD2pUhCzM1evcA+4EJsuKz2eBbxoRKj+zS6pIXg3gu0ynRkOoxr3flPAJaeAMDQZWPfdPXYHSnXDSZAgMBAAECggEATA5Qi4ARx4YwwyhHcA2wjE7Q99LJYoYrH8hXZxsrkowzp54SxE5HWnurqCPsqXqyWwwkWgxtBiaKJnvhCptkiFA73OIlaZS5LeokH7mz9tUgO9t+kwja/IoYGzt/JOaHP8YUcTZI1+s3DaiUQDoIkIg4cB/NfgsCHV2IUcbH05B+/IR5lwYRNClzBivkChFGrLlef8obLbV6h7hg+oRedHiYT+S99N5x4XJLCHLZQWXYQWrXSJYSbB8YB5ZxsxjR3J7vIC9IGV6gPTJWPqE6kt7aXkbNMgXa+sCefeb9tgyoyFiMzYD+gpIh+20I4Z66PpBOH8x96wAePwG688jbcQKBgQD47qK2cBviYRJH6IAOfGKzyT91eP/R5RRyeppjz4QFaREtHN6YA3Mf5+nxYFa095Y2tePYXJTEC3wUi2JBT/5GOo0thNEiarTHsWag9XszO9kVBif8gmDK6pP7UJSwZSqtKFvTJ/9FV5zW3VGb0kprdeObJPjUg+4BQHvlg8rktQKBgQCXqUgeF20V7IeHoqjfU7kpfK1mo7lFo4E/cUn+/x0/KMPZ00K4aQUnZ9FWlhJZ7fotig5nOMYX0hZAq96g6Xl9p5m1cdDnrlJNxkWl8XGOy6E/+f2AK5ZjZhD+pSeGd1IP7s3OWd11ZYk+tdTHmJP9FtZ+LlqV+2+jVUcdYsiC1QKBgQDvVmmlJQb6UkCEWLt+sgMoPs8/wBWeljVhmBWG74dMuGcmS8KMv4xZ2d2pEOps1jtb4OfvJ5x1HWGwUw8mIqYkmbkRUcjN4XBtK1i0WzGX4evm9eNOOYCcuIuNLz22l54/nDUlQSiDYChQwbvsKHUa+t7aVOLVWP9lvr0gv+U8nQKBgEzSfepoOWSKnJhTB7GgakGemwNL2bRxvy2QyEe3mGv+zT5QahKZd/fe+cYfXIpbJofcz7DvrEAytfzqUmo1+clxlUW4snY48g3dajhFlh0b/sE2c3dyHMqaz+79X4kYdeQGNg/Zq3klBqFSX8b+/a+M2vetqUshwwn8T3qOKD1FAoGAK60MGi+sQ0eeznV2wybWOKruRl8N+YUyY7RE5I/t9sFEycSF4Ah7vZkosxjiA/xSyIL1LqA7CXzfmjpITUAsK1HKXOcO5lmLQ8Qnnprl69MgAMT/4c6c94lKc2bf7D3n+0+0GP5qSirLgn+/JntcIv6wLZnWZUtWdbka+zjKKKY=',
       'rsaPubKey':'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk3ldo2cSWWPtrkCJXjVu0WQl5BRmHChOooXcK37N/El0vVZm1H6ERCkJuz0EI3j/FOLEQ4mQHlLhCXH6+XOdDG9fSMAJyKYbkys9yBnmwHAmyYwOvk4DqUyDA2H1t55azTmiNgZNWWIpSTOl0AraK9SIELnC+y3VJef458NrK3oRFvKQcuZ9e1imwPN8sS2hpi2on8LVA/74cEu7xdTVLMM2AfmvU0noUK399fR9O1esZ0uAFqQ1FVYx6P4x9FmHWKQWA6vVw9qVIQszNXr3APuBCbLis9ngW8aESo/s0uqSF4N4LtMp0ZDqMa935TwCWngDA0GVj33T12B0p1w0mQIDAQAB',
       'notifyURL':this.siteUrl+'addons/ewei_shopv2/payment/alipay/notify.php'};
   var Share='SetAlipayInfo(\''+ProductName+'\',\''+Desicript+'\',\''+Price+'\',\''+OuttradeNo+'\','+JSON.stringify(config)+')';
   api.execScript({
					script:Share
				});
	
};


YDBOBJ.prototype.QQLogin = function(accessUrl) {
	
var  config={};
   var Share='QQLogin(\''+accessUrl+'\','+JSON.stringify(config)+')';
   api.execScript({
					script:Share
				});
	
};


YDBOBJ.prototype.WXLogin = function(returnDataType, accessUrl) {
	
FoxUI.toast.show("正在呼起微信客户端");

var  config={'apiKey':'wxa2e1d545cb2aca53','apiSecret':'1e51ce9f50fe50168707aeb6ed325ce5'};



   var Share='WXLogin(\''+returnDataType+'\',\''+accessUrl+'\','+JSON.stringify(config)+')';
   api.execScript({
					script:Share
				});
			
};
