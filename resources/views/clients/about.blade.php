<!DOCTYPE html>
<html lang="en">
<head>
    <title>QUERO - Sobre</title>
    <style type="text/css">
        body {
            padding:0;
            margin:0;
        }
        .container {
            width: 100vw;
        }
        .about-image {
            width: 100%;
        }
        .content {
            padding: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <img class="about-image" src="{{$p_Photo['value']}}"/>
    <p></p>

    <div class="content">
        {!! $p_Item != null ? $p_Item['value'] : '' !!}
    </div>
</div>
</body>
</html>