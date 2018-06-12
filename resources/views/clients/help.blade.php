<!DOCTYPE html>
<html lang="en">
<head>
    <title>QUERO - Ajuda</title>
    <link rel="stylesheet" type="text/css" href="{{url("slick/slick.css")}}"/>
    <link rel="stylesheet" type="text/css" href="{{url("slick/slick-theme.css")}}"/>
    <style type="text/css">
        body {
            background-color: #818181;
        }
        .carousel {
            width: calc(100vw - 60px);
            margin-right: auto;
            margin-left: auto;
        }
        .tutorial-image-div {
            height: calc(100vh - 53px);
        }
        .tutorial-image-div:focus {
            outline: none;
        }
        .tutorial-image {
            height: 100%;
            margin-right: auto;
            margin-left: auto;
        }
        .slick-dots li button:before {
            font-size: 7px;
        }
        .slick-dots li button:hover:before, .slick-dots li button:focus:before {
            color:white;
            opacity: 0.8;
        }
        .slick-dots li.slick-active button:before {
            color:white;
        }

    </style>
</head>
<body>

<div class="carousel">
    @foreach($p_Items as $c_Item)
    <div class="tutorial-image-div">
        <img class="tutorial-image" src="{{$c_Item->value}}"/>
    </div>
    @endforeach
</div>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="{{url("slick/slick.min.js")}}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('.carousel').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            infinite: true,
            arrows:false
        });
    });
</script>

</body>
</html>