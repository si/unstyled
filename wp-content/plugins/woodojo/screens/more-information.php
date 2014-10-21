<body class="iframe">

<div id="woodojo" class="wrap more-information">

	<h2 class="main-heading"><?php echo esc_html( $this->component->title ); ?></h2>
	
	<div class="component-stats">
				
		<h3>FYI</h3>
		<ul>
			<li><strong>Version:</strong> 0.1</li>
			<li><strong>Author:</strong> <a href="http://www.woothemes.com" target="_blank">WooThemes</a></li>
			<li><strong>Requires WordPress Version:</strong> 2.9 or higher</li>
			<li><strong>Compatible up to:</strong> 3.1.4</li>
			<li><a target="_blank" href="http://wordpress.org/extend/plugins/random-cat-facts/">WordPress.org Plugin Page »</a></li>
			<li><a target="_blank" href="http://www.topcatlitterboxes.com/cat-facts-plugin">Plugin Homepage  »</a></li>
		</ul>
			</div>
			
	<h3><?php _e( 'Description', 'woodojo' ); ?></h3>
	
	<p><?php echo $this->component->long_description; ?></p>

<?php if ( count( $this->screenshots ) > 0 ) { ?>
	<div id="images">
		<h3><?php _e( 'Screenshots', 'woodojo' ); ?></h3>
		<div class="flexslider">
		    <ul class="slides">
		    	<?php foreach ( $this->screenshots as $k => $v ) { ?>
		    	<li><img src="<?php echo $v; ?>" /></li>
		    	<?php } ?>
		    </ul>
		</div>	
	</div>
<?php } ?>	
	<br class="clear"/>
	
</div><!--/#woodojo .wrap-->

</body>
</html>