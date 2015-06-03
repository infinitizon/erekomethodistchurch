<html>
    <head>
        <title>bxslider</title>
        <script type="text/javascript" src="/assets/js/jquery-1-11.min.js"></script>
        <script type="text/javascript" src="jquery.bxslider.min.js"></script>
        <link href="jquery.bxslider.css" rel="stylesheet" />
        <style type="text/css">
            .bxslider-wrapper {
                width: 310px; margin: 20px auto; position: relative;
                -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;
                -webkit-box-shadow: 0 0 2px #999; -moz-box-shadow: 0 0 2px #999; box-shadow: 0 0 2px #999;
            }
        </style>
    </head>
    <body>
        <div class="bxslider-wrapper">
                <ul class="bxslider">
                    <li><a href="google.com"><img src="../../../assets/images/entrance.jpg" alt="" style="width:310px;height:150px;" /></a></li>
                    <li><img src="../../../assets/images/vision_mission.gif" alt="" style="width:310px;height:150px;" /></li>
                    <li><img src="../../../assets/images/entrance.jpg" alt="" style="width:310px; height:150px;" /></li>
                </ul>
        </div>
    </body>
    <script type="text/javascript">
        $(function() {
            $('.bxslider').bxSlider();
        });
    </script>
</html>