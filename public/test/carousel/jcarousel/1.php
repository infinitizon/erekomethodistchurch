<html>
    <head>
        <title>jCarousel</title>
        <script type="text/javascript" src="/assets/js/jquery-1-11.min.js"></script>
        <script type="text/javascript" src="jquery.jcarousel.min.js"></script>
        <style type="text/css">
            .jcarousel-wrapper {
                width: 310px; margin: 20px auto; position: relative; border: 10px solid #fff;
                -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;
                -webkit-box-shadow: 0 0 2px #999; -moz-box-shadow: 0 0 2px #999; box-shadow: 0 0 2px #999;
            }
            /*
            This is the visible area of you carousel. Set a width here to define how much items are visible. 
            The width can be either fixed in px or flexible in %.  Position must be relative!
            */
            .jcarousel {
                position: relative;
                overflow: hidden;
                width: 310px;
            }

            /*
            This is the container of the carousel items.
            You must ensure that the position is relative or absolute and that the width is big enough to contain all items.
            */
            .jcarousel ul {
                width: 930px;
                position: relative;
                /* Optional, required in this case since it's a <ul> element */
                list-style: none; margin: 0;  padding: 0;
            }

            /*
            These are the item elements. jCarousel works best, if the items have a fixed width and height (but it's not required).
            */
            .jcarousel li {
                /* Required only for block elements like <li>'s */
                float: left;
            }
            /** Carousel Controls **/
            .jcarousel-prev, .jcarousel-next {
                position: absolute; top: 70px;
                width: 30px; height: 30px;
                background: #4E443C;
                font: 24px/27px Arial, sans-serif; color: #fff; text-align: center; text-decoration: none; text-shadow: 0 0 1px #000;
                -webkit-border-radius: 30px; -moz-border-radius: 30px; border-radius: 30px;
                -webkit-box-shadow: 0 0 2px #999; -moz-box-shadow: 0 0 2px #999; box-shadow: 0 0 2px #999;
            }
            .jcarousel-prev {
                /*left: -50px; -- This pulls the previous button left*/
            }
            .jcarousel-next {
               /* right: -50px;-- This pulls next button right*/right: 0;
            }
            /*Didn't seem neccessary
                '.jcarousel-prev:hover span, .jcarousel-next:hover span {
                display: block;
                }
            */
            .jcarousel-prev.inactive, .jcarousel-next.inactive {
                opacity: .5;
                cursor:default;
            }
            /** Carousel Pagination **/
            .jcarousel-pagination {
                position:absolute; bottom:0; right:10px;
            }
            .jcarousel-pagination a {
                text-decoration:none; display:inline-block;
                font-size:11px; line-height:14px; min-width:14px;
                background:#fff; color:#4E443C; border-radius:14px; padding:3px; text-align:center;
                margin-right: 2px;
                opacity: .75;
            }
            .jcarousel-pagination a.active {
                background: #4E443C; color: #fff; opacity: 1;
                text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.75);
            }
</style>
    </head>
    <body>
        <div class="jcarousel-wrapper">
            <div class="jcarousel">
                <ul>
                    <li><a href="google.com"><img src="../../../assets/images/entrance.jpg" alt="" style="width:310px;height:150px;" /></a></li>
                    <li><img src="../../../assets/images/vision_mission.gif" alt="" style="width:310px;height:150px;" /></li>
                    <li><img src="../../../assets/images/entrance.jpg" alt="" style="width:310px; height:150px;" /></li>
                </ul>
            </div>
            <!-- Controls -->
            <a class="jcarousel-prev" href="#">&lsaquo;</a>
            <a class="jcarousel-next" href="#">&rsaquo;</a>
            <!-- Pagination -->
            <p class="jcarousel-pagination">
                <!-- Pagination items will be generated in here when you use the .jcarouselPagination plugin -->
            </p>
        </div>
    </body>
    <script type="text/javascript">
        $(function() {
            $('.jcarousel').jcarousel({
                wrap: 'both'
            }).jcarouselAutoscroll({
                interval: 500,
                target: '+=1',
                autostart: true
            });
            $('.jcarousel-prev').jcarouselControl({
                target: '-=1'
            });
            $('.jcarousel-next').jcarouselControl({
                target: '+=1'
            });
            $('.jcarousel-pagination').jcarouselPagination({
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
        });
    </script>
</html>