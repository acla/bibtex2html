<?php

require('../../../wp-blog-header.php');
include("bibtex2html.php");

?>
<html>
<head>
<title><?php echo isset($_GET['a']) && $_GET['a'] == '1' ? 'Abstract' : 'BibTex'; ?> of <?php echo isset($_GET['k']) ? $_GET['k'] : ''; ?></title>
<link href="<?php echo get_bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" rel="stylesheet" />
</head>
<body class="bibtex-display">
<?php

if(!isset($_GET['k']) || !isset($_GET['p'])) echo "Bad parameters.";
else {
	$post = wp_get_single_post($_GET['p']);
	if($post) {
		$content = $post->post_content;
		
		$bibstring = '';
		$spos = strpos($content, '[bibtex');
		$epos = strpos($content, "[/bibtex]");
		
		while($spos !== false && $epos !== false)  {
		
			$epos += 9;
			$stop = false;
			for($i = $spos + 7; !$stop && $i < strlen($content); $i++) {
				if(substr($content, $i, 1) == ']') $stop = true;
			}
		
			$bibstringSpos = $i;
			$bibstringEpos = $epos - $i - 9;
			$bibstring .= strip_tags(substr($content, $bibstringSpos, $bibstringEpos));

			$content = substr($content, 0, $spos).substr($content, $epos);
			
			$spos = strpos($content, '[bibtex');
			$epos = strpos($content, "[/bibtex]");

		}

		$e = extractBibEntryFromString($bibstring, $_GET['k']);
		if($e === false) echo "Entry not found.";
		else {
			if(isset($_GET['a']) && $_GET['a'] == '1') echo '<p>'.extractBib("abstract", $e, make_accent_table())."\n</p>";
			else echo '<pre>'.htmlentities($e)."\n</pre>";
		}
	} else echo "Bad parameters.";
}

?>
</body>