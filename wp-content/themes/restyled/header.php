<!DOCTYPE html >
<html lang="en">
<head>
	<title><?php wp_title(); ?></title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<?php wp_head(); ?>
</head>

<body>

	<header>

		<a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a>

		<nav>
			<ul>
				<?php wp_list_pages('title_li=&depth=1');?>
			</ul>
		</nav>

	</header>

