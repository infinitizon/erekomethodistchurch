<style>

    @import url(http://fonts.googleapis.com/css?family=Nunito);
    /*CSS file for fontawesome - an iconfont we will be using. This CSS file imported contains the font-face declaration. More info: http://fortawesome.github.io/Font-Awesome/ */
    @import url(http://thecodeplayer.com/uploads/fonts/fontawesome/css/font-awesome.min.css);

    /*Basic reset*/
    * {margin: 0; padding: 0;}

    body {
        background: #4EB889;
        font-family: Nunito, arial, verdana;
    }
    #accordian {
        background: #004050;
        width: 250px;
        margin: 100px auto 0 auto;
        color: white;
        /*Some cool shadow and glow effect*/
        box-shadow: 
            0 5px 15px 1px rgba(0, 0, 0, 0.6), 
            0 0 200px 1px rgba(255, 255, 255, 0.5);
    }
    /*heading styles*/
    #accordian h3 {
        font-size: 12px;
        line-height: 34px;
        padding: 0 10px;
        cursor: pointer;
        /*fallback for browsers not supporting gradients*/
        background: #003040; 
        background: linear-gradient(#003040, #002535);
    }
    /*heading hover effect*/
    #accordian h3:hover {
        text-shadow: 0 0 1px rgba(255, 255, 255, 0.7);
    }
    /*iconfont styles*/
    #accordian h3 span {
        font-size: 16px;
        margin-right: 10px;
    }
    /*list items*/
    #accordian li {
        list-style-type: none;
    }
    /*links*/
    #accordian ul ul li a {
        color: white;
        text-decoration: none;
        font-size: 11px;
        line-height: 27px;
        display: block;
        padding: 0 15px;
        /*transition for smooth hover animation*/
        transition: all 0.15s;
    }
    /*hover effect on links*/
    #accordian ul ul li a:hover {
        background: #003545;
        border-left: 5px solid lightgreen;
    }
    /*Lets hide the non active LIs by default*/
    #accordian ul ul {
        display: none;
    }
    #accordian li.active ul {
        display: block;
    }
</style>

<div id="accordian">
    <ul>
        <li>
            <h3><span class="icon-dashboard"></span>Dashboard</h3>
            <ul>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Search</a></li>
                <li><a href="#">Graphs</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </li>
        <!-- we will keep this LI open by default -->
        <li class="active">
            <h3><span class="icon-tasks"></span>Tasks</h3>
            <ul>
                <li><a href="#">Today's tasks</a></li>
                <li><a href="#">Urgent</a></li>
                <li><a href="#">Overdues</a></li>
                <li><a href="#">Recurring</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </li>
        <li>
            <h3><span class="icon-calendar"></span>Calendar</h3>
            <ul>
                <li><a href="#">Current Month</a></li>
                <li><a href="#">Current Week</a></li>
                <li><a href="#">Previous Month</a></li>
                <li><a href="#">Previous Week</a></li>
                <li><a href="#">Next Month</a></li>
                <li><a href="#">Next Week</a></li>
                <li><a href="#">Team Calendar</a></li>
                <li><a href="#">Private Calendar</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </li>
        <li>
            <h3><span class="icon-heart"></span>Favourites</h3>
            <ul>
                <li><a href="#">Global favs</a></li>
                <li><a href="#">My favs</a></li>
                <li><a href="#">Team favs</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </li>
    </ul>
</div>

<!-- prefix free to deal with vendor prefixes -->
<script src="http://thecodeplayer.com/uploads/js/prefixfree-1.0.7.js" type="text/javascript" type="text/javascript"></script>

<!-- jQuery -->
<script src="http://thecodeplayer.com/uploads/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
    /*jQuery time*/
    $(document).ready(function() {
        $("#accordian h3").click(function() {
            //slide up all the link lists
            $("#accordian ul ul").slideUp();
            //slide down the link list below the h3 clicked - only if its closed
            if (!$(this).next().is(":visible")){
                $(this).next().slideDown();
            }
        })
    })
</script>