    <div id="footer">
	<div id="copyright">Copyright<sup>&copy;</sup> Ereko Methodist Church <?php	echo date("Y") == 2014 ? 2014 : 2014 .' - '. date("Y"); ?></div>
    </div><!-- End div - footer -->
</div><!-- End div - gen_container -->

<?php if(@$_GET['message'] && @$_GET['type'] || (isset($type) && isset($message) )): ?>
<div id="notification">
	<div class="close">x</div>
	<div class="<?php echo isset($type) ? $type : @$_GET['type']; ?>" ><?php echo isset($message) ? $message : @$_GET['message']; ?></div>
</div>
<?php endif; ?>


<!--[if IE]>
	<?php foreach ( $ie_js_files as $ie_js ): ?>
		<script language="javascript" type="text/javascript" src="<?php echo WEB_ROOT; ?>/home/assets/js/<?php echo $ie_js; ?>" /></script>
	<?php endforeach; ?>
<![endif]-->
<?php foreach ( $general_js_files as $gen_files ): ?>
	<script language="javascript" type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/<?php echo $gen_files; ?>" /></script>
<?php endforeach; ?>
<?php foreach ( $page_js_files as $page_js ): ?>
	<script language="javascript" type="text/javascript" src="<?php echo WEB_ROOT; ?>/home/assets/js/<?php echo $page_js; ?>" /></script>
<?php endforeach; ?>
</body>
</html>