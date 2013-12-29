<?php
/**
 * @package bibtex
 * @author Andreas Classen
 * @version 20111211
 */
/*
Plugin Name: BibTeX
Plugin URI: http://www.classen.be/bibtex2html/
Description: This plugin adds a new tag [bibtex]...[/bibtex] which you can use in posts to display a list of BibTeX entries in nice HTML.  
             The following shortcode parameters are available:
              * [bibtex groupyear]: entries are grouped by year (can be combined with grouptype);
              * [bibtex grouptype]: entries are grouped by type (can be combined with groupyear);
              * [bibtex desc]: numbers all entries in descending order;
              * [bibtex authorlimit=integer]: limit the number of authors shown to integer, some integer value (the script prints "et al." when the limit is exceeded);
              * [bibtex sort=list,of,sort,criteria]: sort the entries according to the specified criteria (have a look at the comments of the PHP script for further explanations);
              * [bibtex highlight=secondname]: highlight the author with the specified second name (case insensitive). The script puts a span with class "highlight" around it, so you have to define the class in your CSS to format it appropriately. Note that the Wordpress plugin does not support spaces in the second name.
             Multiple shortcode parameters are simply separated by spaces. There can be no spaces around the = sign.
             There will be a "BibTeX" link behind every entry; clicking on it will open a popup window with the raw BibTeX of the entry. 
             There will also be a link titled "Abstract" if the abstract of an entry is specified; clicking it will open once again a popup with the abstract. 
             The popup window includes a reference to the stylesheet of the current Wordpress theme and the body of the window has class bibtex-display, which can be used to format it appropriately.
             More info on the plugin website.
Author: Andreas Classen
Version: 20111211
Author URI: http://www.classen.be/
*/

/**
 * Replaces [bibtex] tags.
 */
function filterBibtex($content) {
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

		$groupyear = false;
		$grouptype = false;
		$sorting = null;
		$highlightName = null;
		$numbersDesc = false;
		$authorLimit = 0;
		
		$options = explode(" ", substr($content, $spos + 6, $i - $spos - 6 - 1));
		$bibstring = strip_tags(substr($content, $bibstringSpos, $bibstringEpos));
		
		for($i = 0; $i < count($options); $i++) {
			$options[$i] = trim($options[$i]);
			
			if(substr($options[$i], 0, 1) == ',') {
				$options[$i] = substr($options[$i], 1);
			} 
			if(substr($options[$i], strlen($options[$i]) - 1, 1) == ',') {
				$options[$i] = substr($options[$i], 0, strlen($options[$i]) - 1);
			}
			
			if($options[$i] == 'groupyear') $groupyear = true;
			else if($options[$i] == 'grouptype') $grouptype = true;
			else if($options[$i] == 'asc') $numbersDesc = false;
			else if($options[$i] == 'desc') $numbersDesc = true;
			else if(substr($options[$i], 0, 12) == 'authorlimit=') $authorLimit = (int) substr($options[$i], 12);
			else if(substr($options[$i], 0, 10) == 'highlight=') $highlightName = substr($options[$i], 10);
			else if(substr($options[$i], 0, 5) == 'sort=') {
				$sorting = substr($options[$i], 5);
				if(strpos($sorting, ',') !== false) $sorting = explode(",",$sorting);
			}
		}
		
		require_once(WP_PLUGIN_DIR.'/bibtex/bibtex2html.php');
		$biburl = WP_PLUGIN_URL.'/bibtex/display.php?p='.get_the_ID().'&s='.$bibstringSpos.'&e='.$bibstringEpos.'&k=%key';
		$absurl = $biburl.'&a=1';
		// &#039; is a single quote, this is necessary to make wordpress behave nicely with these links
		$bibtex = bibstring2html($bibstring, 
								 null, 
								 $grouptype, 
								 $groupyear, 
								 "javascript: var bibwindow = window.open(&#039;{$biburl}&#039;,&#039;%key&#039;,&#039;height=300,width=750&#039;);",
								 $highlightName,
								 $numbersDesc,
								 $sorting,
								 $authorLimit,
								 "javascript: var bibwindow = window.open(&#039;{$absurl}&#039;,&#039;%key-abstract&#039;,&#039;height=300,width=750&#039;);");
		$content = substr($content, 0, $spos).$bibtex.substr($content, $epos);
		
		$spos = strpos($content, '[bibtex');
		$epos = strpos($content, "[/bibtex]");
	}
	return $content;
}


add_filter('the_content', 'filterBibtex');

?>
