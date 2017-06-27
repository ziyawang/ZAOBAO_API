<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>分享</title>
    <script type="text/javascript">
        (function (doc, win) {
            var docEl = doc.documentElement,
                    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                    recalc = function () {

                        var clientWidth = docEl.clientWidth;
                        if (!clientWidth) return;
                        if (clientWidth >= 750) {
                            docEl.style.fontSize = '100px';
                        } else {
                            docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
                        }
                    };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);

            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
    </script>
    <style type="text/css">
        html{font-size: 100px;}
        body,div,a{padding:0;margin:0;}
        .header{padding:0.12rem 0.2rem;background:rgba(0,0,0,0.8);overflow: hidden;width:100%;box-sizing: border-box;}
        .pic{float: left;width:2.7rem;}
        .download{float: right;text-decoration: none;font-size: 0.32rem;color: #333;outline: none;display: block;line-height: 0.5rem;background:#ffda3f;border-radius: 0.1rem;width: 1.2rem;text-align: center;margin-top: 0.12rem;}
        .text{margin-top:0.1rem;font-size: 0.34rem;line-height: 1.8;padding: 0 0.1rem;text-align: justify;text-indent: 2em;}
        .img img{width:100%;max-width:100%;margin:0 auto;display: block;}
    </style>
</head>
<body>
<div class="header">
    <img src="{{asset('img/logo.png')}}" alt="logo" class="pic" />
    <a href="https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=2031538466,2239313161&fm=85&s=EAC29F0AD4E4F8AE00D440590300C0F2" class="download" download="https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=2031538466,2239313161&fm=85&s=EAC29F0AD4E4F8AE00D440590300C0F2">下载</a>
</div>
@foreach($datas as $data)
<div class="text">{{$data->content}}</div>
@if(!empty($data->describe))
    <div class="img"><img  src="http://images.ziyawang.com{{$data->describe}}" /></div>
    @else
    <div class="img"><img  src=""/></div>
    @endif
@endforeach
</body>
</html>