<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *        *       *     *   *    BibTeX 2 HTML    *   *     *       *          *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                             *
 * Author: Andreas Classen <www.classen.be>                                    *
 *                                                                             *
 * This script is a modified version of a script written by Johannes Knabe:    *
 * <jknabe@uni-osnabrueck.de>, found at http://bibscript.panmental.de/.  (Page *
 * consulted in November 2006, version used marked as June/July 2005 version.) *
 *                                                                             *
 * The objective of the original script by Knabe was to enable the management  *
 * of references, including XML and BibTeX export.  The objective of this      *
 * script, however, is only to display the BibTeX entries contained in a given *
 * file.                                                                       *
 *                                                                             *
 * I took out the BibTeX formatting part of the original script and wrote      *
 * a parser to extract individual BibTeX entries of a file.                    *
 *                                                                             *
 *                                                                             *
 * Contributors                                                                *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                             *
 * Eric Sommerlade <www.sommerla.de> added suppot for accents.                 *
 * Johnnie Chan <www.johnniechan.com> fixed some bugs.                         *
 *                                                                             *
 *                                                                             *
 * How to use?                                                                 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * All you need to do in order to include the script in your page is to make   *
 * the following function calls to produce an HTML listing of the bibtex file. *
 * <?php                                                                       *
 *     require_once('./bibtex2html.php');                                      *
 *     echo bibfile2html('path/to/your/bibtex/file');                          *
 * ?>                                                                          *
 *                                                                             *
 *                                                                             *
 * Advanced Parameters                                                         *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * The bibfile2html function has a number of optional parameters, which        *
 * control the output.  If you want to omit a parameter, pass NULL; this will  *
 * make sure that the default value is used.                                   *
 *                                                                             *
 * The signature of the function is:                                           *
 *                                                                             *
 *     bibfile2html($filename,      // string                                  *
 *                  $displayTypes,  // array(string => string)                 *
 *                  $groupType,     // bool                                    *
 *                  $groupYear,     // bool                                    *
 *                  $bibLink,       // string                                  *
 *                  $highlightName, // string                                  *
 *                  $numbersDesc,   // bool                                    *
 *                  $sorting,       // string or array(string)                 *
 *                  $authorLimit,   // int                                     *
 *                  $abstractLink,  // string                                  *
 *                  )                                                          *
 *                                                                             *
 * The parameters (and their defaults) are explained below:                    *
 *                                                                             *
 * - $filename      is the path to the bib file that is to be displayed.       *
 * - $displayTypes  is an associative array specifying which bibtex entrtypes  *
 *                  you want to list and in which order.  The key is the       *
 *                  bibtex entry type, and the value is what is displayed as   *
 *                  a group title.  Only entries whose type is among those in  *
 *                  this array will be shown,  except if you use the special   *
 *                  key "_unknown", which acts  as a sink for those where the  *
 *                  key could not be found.  If you leave the parameter empty, *
 *                  there is a default with all common bibtex types and their  *
 *                  names in english.                                          *
 * - $groupType     A boolean indicating whether entries shoud be grouped by   *
 *                  type.  Default is true.                                    *
 * - $groupYear     Same for the year.  Default is true.                       *
 * - $bibLink       The url to a script which takes as parameter the key of a  *
 *                  bib entry and outputs the contents of this entry.  The     *
 *                  placeholder %key inside the string will be replaced by the *
 *                  key of the entry, and the whole url placed inside the href *
 *                  attribute of an <a> tag.  If the string is empty (the      *
 *                  default, the <a> will not be shown).                       *
 *                  Hint: use javascript to open a small popup, i.e.           *
 *                    "javascript: var bibwindow = window.open(                *
 *                                        '/displayBibTeX.php?key=%key',       *
 *                                        '%key',                              *
 *                                        'height=300,width=750'               *
 *                                      );"                                    *
 * - $highlightName If the second name of an author matches this parameter, it *
 *                  will be put into <span class="highlight"></span>.          *
 * - $numbersDesc   By default, the references will be numbered in ascending   *
 *                  order.  This can be changed by setting this parameter to   *
 *                  true.                                                      *
 * - $sorting       The field used to sort the entries (within a grouping).    *
 *                  Any field can be used for this.  Special values are:       *
 *                  'citation' (the rendered citation), 'key' (the BibTeX      *
 *                  key), 'author' (the rendered author list, not the list     *
 *                  as it appears in the file) and 'timestamp' (a non-standard *
 *                  field).  All fields except for 'year' and 'timestamp' are  *
 *                  sorted in ascending order.  It is also possible to specify *
 *                  multiple sort criteria by passing an array of field names. *
 *                  If left empty, the entries are listed in the order in      *
 *                  which they appear in the file.                             *
 * - $authorLimit   Limits the number of authors shown for each entry. Instead *
 *                  of the remaining authors, "et al." is shown.               *
 * - $abstractLink  The url to a script which takes as parameter the key of a  *
 *                  bib entry and outputs the abstract of this entry.  See the *
 *                  description of the $bibLink parameter for more info.       *
 *                                                                             *
 *                                                                             *
 * Creating a "display BibTeX" script                                          *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you use the $bibLink parameter of the script, you need to link to a      *
 * second script file which displays the BibTeX of a specific entry.  This can *
 * be accomplished by a php file with the following two lines:                 *
 * <?php                                                                       *
 *     require_once("./bibtex2html.php");                                      *
 *     echo extractBibEntry("path/to/your/bibtex/file", $_GET['key']);         *
 * ?>                                                                          *
 *                                                                             *
 *                                                                             *
 * Creating a "display abstract" script                                        *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you use the $abstractLink parameter of the script, you need to link to a *
 * second script file which displays the BibTeX of a specific entry.  This can *
 * be accomplished by a php file with the following code:                      *
 * <?php                                                                       *
 *     require_once("./bibtex2html.php");                                      *
 *     echo extractBib(                                                        *
 *               "abstract",                                                   *
 *               extractBibEntry("path/to/your/bibtex/file", $_GET['key']),    *
 *               make_accent_table()                                           *
 *          );                                                                 *
 * ?>                                                                          *
 *                                                                             *
 *                                                                             *
 * Formatting                                                                  *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * The script produces an ordered list (<ol>), and if grouping is activated,   *
 * it will use <h2> for first-level titles and <h3> for second-level titles.   *
 *                                                                             *
 * Furthermore, several fields are enclosed in <span>s so that they can be     *
 * formatted based on their class.  Most classes correspond to bibtex fiedls:  *
 *  - authors: The list of authors;.                                           *
 *  - highlight: An author that matched the $highlightedName parameter.        *
 *  - title: The title field of the entry.                                     *
 *  - in: The book, booktitle, or journal field of the entry.                  *
 *  - publisher: The publisher filed of the entry.                             *
 *  - school: The school filed of the entry (Thesis).                          *
 *  - institute: The institue filed of the entry (Techreport).                 *
 *                                                                             *
 * At the end of each entry is a list with links (see also below), each of     *
 * them is enclosed by a <span>.  The classes are:                             *
 *  - webpdf: The "pdf" link.                                                  *
 *  - url: The "www" link.                                                     *
 *  - publisherurl: The "publisher" link.                                      *
 *  - citeseerurl: The "citeseer" link.                                        *
 *  - doi: The "doi" link.                                                     *
 *  - links: Encloses all of these links.                                      *
 * If the $abstractLink parameter is set and the abstract of the entry is      *
 * specified, a link titled "Abstract" is placed at the end of the entry; it   *
 * is also enclosed by a span.                                                 *
 *  - abstractlink: The "Abstract" link.                                       *
 * If the $bibLink parameter is set, a BibTeX link is placed at the end of the *
 * entry (also enclosed by a span).                                            *
 *  - bibtexlink: The BibTeX link.                                             *
 *                                                                             *
 * You can use CSS to format (or hide) these things.  Note that, if you        *
 * already use <h2> somewhere else on your page, and you want the <h2>'s       *
 * produced by this script to look different, you can simply wrap the output   *
 * in a <div> and use conditional CSS.                                         *
 *                                                                             *
 *                                                                             *
 * BibTeX format and special fields                                            *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * This script interprets several non-standard BibTeX fields for web links.  A *
 * link has to start with "http://", except for DOIs where only the name is    *
 * required.  The fields are:                                                  *
 *  - url: Link to the venue, is used on the "in" part of the citation.  If    *
 *    there is no "in" part, a "www" link is displayed at the end.             *
 *  - webpdf: This is assumed to be a link to the pdf file of the given        *
 *    publication (title becomes clickable and also displayed as "pdf").       *
 *  - publisherurl: The url of the publisher, is used on the "publisher" part  *
 *    of the citation.  If there is none, a "publisher" link is displayed at   *
 *    the end.                                                                 *
 *  - citeseerurl: The citeseer url (displayed as "citeseer").                 *
 *  - doi: Interpreted as a DOI name from dx.doi.org (alternative for the "in" *
 *    part, otherwise displayed as "doi").                                     *
 *                                                                             *
 * If you use the JabRef BibTeX manager (jabref.sourceforge.net), most of the  *
 * above fields are available as default.  You can add custom fields by        *
 * choosing "Customize entry types" from the "Options" menu.                   *
 *                                                                             *
 * Note that the script does not interpret BibTeX strings (abbreviations),     *
 * crossrefs or similar things, maybe I'll add this one time. In the meantime, *
 * a more comprehensive BibTeX parser exists at bibliophile.sourceforge.net.   *
 *                                                                             *
 * Also note that the script assumes each BibTeX entry to start on a new line. *
 * (This is how most tools save BibTeX, but there might be exceptions).        *
 *                                                                             *
 *                                                                             *
 * License                                                                     *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Feel free to use this script wherever you want to, there are no speicific   *
 * licence restrictions.                                                       *
 *                                                                             *
 * I do not guarantee the correctness of the script, use it at your own risk!  *
 * If you detect errors in the presentation of a given entry, you can send me  *
 * a desciption of the error (this is: the BibTeX entry, the output of this    *
 * script and the output how it should look like), using the e-mail address    *
 * specified on www.classen.be (presumably <acs@info.fundp.ac.be>).            *
 *                                                                             *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function make_accent_table(){
	$a = array(
		"\\'{x}" => "&xacute;",
		"\\`{x}" => "&xgrave;",
		"\\^{x}" => "&xcirc;",
		"\\~{x}" => "&xtilde;",
		"\\\"{x}" => "&xuml;",
	);
	$vowel = array("a", "e", "i", "o", "u", "n", "A", "E", "I", "O", "U", "N");
	$b = array(
		"\\vr" => "&#X0159;",
		"\\v{r}" => "&#X0159;",
		"\\vR" => "&#X0158;",
		"\\v{R}" => "&#X0158;",
		"\\vs" => "&#X0161;",
		"\\v{s}" => "&#X0161;",
		"\\vS" => "&#X0160;",
		"\\v{S}" => "&#X0160;",
		"\\vi" => "&#X012D;",
		"\\v{i}" => "&#X012D;",
		"\\vI" => "&#X012C;",
		"\\v{I}" => "&#X012C;",
		"\\c{c}" => "&ccedil;",
		"\\c{C}" => "&Ccedil;",
	);
	foreach ($a as $k => $v ) {
		foreach ( $vowel as $vv ) {
			$kvv = str_replace('x',$vv,$k);
			$svv = str_replace('x',$vv,$v);
			$b[$kvv] = $svv;
		}
	}
	return $b;
}

// Replaces accents in a string 
function replace_accents($s, $table){
	return strtr($s, $table);
}

function extractBib($what, $haystack, $acc){
	$delim1 = "{";
	$delim2 = "}";
	if(substr_count($haystack,$delim1) < 3){
		$delim1 = "\"";
		$delim2 = "\"";
	}
	$ret="";
	while(($ret=="")&&($test=stristr($haystack,$what))){
		$haystack=trim(substr($haystack,strpos(strtoupper($haystack),strtoupper($what))-1));
		if($haystack!=$test){					//character before $what must be white-space
			$haystack=substr($test,strlen($what));
			continue;
		}
		$haystack=trim(substr($test,strlen($what)));
		if(strpos($haystack,"=")==0){ //first non-white-space character must be =
			$cnt=0;
			$startpos=strpos($haystack,$delim1);
			if(strpos($haystack,",")<$startpos){
				$ret=substr($haystack,1,strpos($haystack,",")-1);
				return trim(str_replace("\\","",str_replace($delim2,"",str_replace($delim1,"",replace_accents($ret,$acc)))));
			}
			$endpos=$startpos;
			while($cnt<strlen($haystack)){
				if($haystack[$endpos]==$delim1) $cnt++;
				if($haystack[$endpos]==$delim2) $cnt--;
				if($cnt==0){
					$ret=substr($haystack,$startpos+1,$endpos-$startpos-1);
					return trim(str_replace("\\","",str_replace($delim2,"",str_replace($delim1,"",replace_accents($ret,$acc)))));
				}
				$endpos++;
			}
		}
		$haystack=$test;
	}
	return trim($ret); //might be "" !
}

function extractBibName($haystack){
	$posi=strpos($haystack,"{")+1;
	$res=substr($haystack,$posi,strpos($haystack,",")-$posi);
	return $res;
}


/**
 * Takes one BibTeX name string and formats its properly: only one "and" remains, and
 * the all names are rewritten "SecondName, FN.".
 * 
 * If the hightlightName parameter is set, then a matching second name will be 
 * surrounded by <span class="highlighted"></span>.
 * 
 * @author Johannes Knabe, Andreas Classen
 */
function formatAuthors($author, $hightlightName, $authorLimit = 0){
	$editorStr = ", ed.";
	$suffix = "";
	if(strpos($author,", ed.")){
		$suffix=$editorStr;
		$author=str_replace(", ed.","",$author);
	}
	if(strpos($author,", eds.")){
		$suffix=$editorStr;
		$author=str_replace(", eds.","",$author);
	}
	if(strpos($author,", edt.")){
		$suffix=$editorStr;
		$author=str_replace(", edt.","",$author);
	}
	if(strpos($author," ed.")){
		$suffix=$editorStr;
		$author=str_replace(" ed.","",$author);
	}
	if(strpos($author," eds.")){
		$suffix=$editorStr;
		$author=str_replace(" eds.","",$author);
	}
	if(strpos($author," edt.")){
		$suffix=$editorStr;
		$author=str_replace(" edt.","",$author);
	}
	if(strpos($author,", editors")){
		$suffix=$editorStr;
		$author=str_replace(", editors","",$author);
	}
	if(strpos($author,", editor")){
		$suffix=$editorStr;
		$author=str_replace(", editor","",$author);
	}
	if(strpos($author," editors")){
		$suffix=$editorStr;
		$author=str_replace(" editors","",$author);
	}
	if(strpos($author," editor")){
		$suffix=$editorStr;
		$author=str_replace(" editor","",$author);
	}
	
	// Formatting
	$sepName = ", "; // the separator between the second name and the abbreviated first name
	$authArr = explode(" and ", str_replace(array("\n","\r","\t")," ", $author));
	$i = 0;
	
	while(is_array($authArr) && $i < count($authArr)) {
		$authArr[$i] = trim($authArr[$i]);
		$firstNames = '';
		$secondNames = '';
				
		// When there is a comma in the name, then it is written "SecondName, FirstName"; 
		if(strpos($authArr[$i], ",") !== false) {
			$firstNames  = trim(substr($authArr[$i], strpos($authArr[$i], ",")+1));
			$secondNames = trim(substr($authArr[$i], 0, strpos($authArr[$i], ",")));
		// Otherwise "FirstName SecondName"
		} else {
			$firstNames  = substr($authArr[$i], 0, strrpos($authArr[$i], " "));
			$secondNames = substr($authArr[$i], strrpos($authArr[$i], " ")+1);
		}
		
		// Abbreviate first names
		$firstNamesArr = explode(" ", trim($firstNames));
		foreach($firstNamesArr as $j => $name) {
			if(strlen($name) > 2 && strpos($name, ".") === false) {
				if(strpos($name, "-") === false) {
					$firstNamesArr[$j] = substr($name,0,1).".";
				} else {
					$firstNamesArr[$j] = substr($name,0,1).substr($name, strpos($name, '-'), 2).".";
				}
			}
		}
		$firstNames = implode(' ', $firstNamesArr);
		
		$authArr[$i] = trim($secondNames.$sepName.$firstNames);
		
		if($hightlightName != '' && strtolower($secondNames) == strtolower($hightlightName)) $authArr[$i] = '<span class="highlight">'.$authArr[$i].'</span>';
		
		$i++;
	}
	
	$authors = '';
	if(count($authArr) == 1) $authors = $authArr[0];
	else {
		$limit = $authorLimit == 0 ? count($authArr) : min(count($authArr), $authorLimit);
		$addEtAl = $authorLimit != 0 && count($authArr) > $authorLimit;
		
		$authors = '';
		for($i = 0; $i < $limit - ($addEtAl ? 0 : 1); $i++) {
			$authors .= '; '.$authArr[$i];
		}
		if($addEtAl) $authors = substr($authors, 2).' et al.';
		else $authors = substr($authors, 2).' and '.$authArr[$i];
	}
	
	return $authors.$suffix;
}

/**
 * Takes one BibTeX entry, its type, and an accent table and formats it as a citation.  
 * If the entry is not valid, an empty string will be returned.
 * 
 * The hightlightName parameter can be used to print a matching author/editor name in bold.
 * 
 * @author Johannes Knabe, Andreas Classen
 */
function bibtex2html($entry, $type, $accents, $hightlightName = '', $authorLimit = 0){
	$ret = extractBib("text", $entry, $accents);
	
	if(trim($ret)==""){
		// There is no predefined text to show, we create some
		$title = extractBib("title", $entry, $accents);
		$webpdf = extractBib("webpdf", $entry, $accents);
		$publisherurl = extractBib("publisherurl", $entry, $accents);
		$doi = extractBib("doi", $entry, $accents);
		if($doi != '') $doi = 'http://dx.doi.org/'.$doi;
		$url = extractBib("url", $entry, $accents);
		$year = extractBib("year", $entry, $accents);
		$author = extractBib("author", $entry, $accents);
		
		$urllinked = false;			// true if the url has been set in a link; it will be placed on the "in" part
		$publisherlinked = false;	// true if the publisherurl has been set in a link; it will be placed on the "in" part except if the url is there, in which case it will be placed on the publisher part
		$doilinked = false;			// true if the doi has been set in a link; it will be placed on the "in", except if one of the previous guys is
		
		// Title should not end with a full stop
		if($title[strlen($title)-1] == ".") $title = substr($title, 0, strlen($title)-1);

		if($author == "") {
			$author = extractBib("editor", $entry, $accents);
			$author = $author.", ed.";
		}
		// Authors:
		$ret = $ret . '<span class="authors">'.formatAuthors($author, $hightlightName, $authorLimit).'</span> ';
	
		// Check validity:
		if((trim($author)=="") || (trim($title)=="")) return "";
		
		if(trim($webpdf)!="") {
			$title = '<a href="'.$webpdf.'" >'.$title.'</a>';
		}
		// Ttitle:
		$ret .= '<span class="title">'.$title.'</span>. ';
		
		
		// Main content:
		switch($type) {
			case "article":
				$journ = extractBib("journal", $entry, $accents);
				if($journ == "") $journ = extractBib("book", $entry, $accents); //might be a book chapter...
				if($journ == "") $journ = extractBib("booktitle", $entry, $accents); //might be a book chapter...
				if(trim($journ) != ""){
					$vol	= extractBib("volume", $entry, $accents);
					$numb	= extractBib("number", $entry, $accents);
					$pages	= extractBib("pages",  $entry, $accents);
					
					// Decide on link
					if(trim($url) != "") {
						$journ = '<a href="'.$url.'" target="_blank">'.$journ.'</a>';
						$urllinked = true;
					} elseif(trim($publisherurl) != "") {
						$journ = '<a href="'.$publisherurl.'" target="_blank">'.$journ.'</a>';
						$publisherlinked = true;
					} elseif(trim($doi) != "") {
						$journ = '<a href="'.$doi.'" target="_blank">'.$journ.'</a>';
						$doilinked = true;
					}
					
					// Build output
					$ret	= $ret.' <span class="in">In '.$journ.'</span>';
					if(trim($vol)	!= "") $ret = $ret.', '.$vol;
					if(trim($numb)	!= "") $ret = $ret.' ('.str_replace(array("--", " -", "- "), "-", $numb).")";
					if(trim($pages)	!= "") $ret = $ret.': '.str_replace(array("--", " -", "- "), "-", $pages);
					if(trim($year)	!= "") $ret = $ret.', '.$year;
					$ret = $ret.".";
				}	
				break;
				
			case "inbook":
			case "inproceedings":	
			case "incollection":	
				$booktitle = extractBib("booktitle", $entry, $accents);
				if($booktitle == "") $booktitle = extractBib("book", $entry, $accents);
				if($booktitle == "") $booktitle = extractBib("journal", $entry, $accents);
				if(trim($booktitle)!=""){
					$publisher = extractBib("publisher", $entry, $accents);
					$pubaddress = extractBib("address", $entry, $accents);
					$pages = extractBib("pages", $entry, $accents);
					$series	= extractBib("series", $entry, $accents);
					$volume	= extractBib("volume", $entry, $accents);
					
					// Decide on link for booktitle
					if(trim($url) != "") {
						$booktitle = '<a href="'.$url.'" target="_blank">'.$booktitle.'</a>';
						$urllinked = true;
					} elseif(trim($doi) != "") {
						$booktitle = '<a href="'.$doi.'" target="_blank">'.$booktitle.'</a>';
						$doilinked = true;
					} elseif(trim($publisherurl) != "") {
						$booktitle = '<a href="'.$publisherurl.'" target="_blank">'.$booktitle.'</a>';
						$publisherlinked = true;
					}
					
					// Decide on link for publisher
					if(trim($publisher)	!= "") {
						if(trim($publisherurl) != "") {
							$publisher = '<a href="'.$publisherurl.'" target="_blank">'.$publisher.'</a>';
							$publisherlinked = true;
						} elseif(trim($doi) != "") {
							$publisher = '<a href="'.$doi.'" target="_blank">'.$publisher.'</a>';
							$doilinked = true;
						}
					}
					
					// Build output
					$ret = $ret.' <span class="in">In '.$booktitle.'</span>';
					if(trim($pages)		!= "") $ret = $ret.", pages ".str_replace(array("--"," -","- "),"-",$pages);
					if(trim($publisher)	!= "") $ret = $ret.', <span class="publisher">'.$publisher.'</span>';
					if(trim($pubaddress)!= "") $ret = $ret.', '.$pubaddress.'';
					if(trim($series)		!= "") $ret = $ret.', '.$series.' '.$volume.'';
					if(trim($year)		!= "") $ret = $ret.", ".$year;
					$ret = $ret.".";
				}
				break;
				
			case "book":	
			case "booklet":
			case "proceedings":
			case "conference":
				$publisher  = extractBib("publisher", $entry, $accents);
				$pubaddress = extractBib("address", $entry, $accents);
				$series	= extractBib("series", $entry, $accents);
				$volume	= extractBib("volume", $entry, $accents);
				
				// Decide on link for publisher
				if(trim($publisher)	!= "") {
					if(trim($publisherurl) != "") {
						$publisher = '<a href="'.$publisherurl.'" target="_blank">'.$publisher.'</a>';
						$publisherlinked = true;
					} elseif(trim($doi) != "") {
						$publisher = '<a href="'.$doi.'" target="_blank">'.$publisher.'</a>';
						$doilinked = true;
					}
				}
				
				// Build output
				if(trim($publisher)		!= "") $ret = $ret.' <span class="publisher">'.$publisher.'</span>';
				if(trim($pubaddress)	!= "") $ret = $ret.', '.$pubaddress.'';
				if(trim($series)		!= "") $ret = $ret.', '.$series.' '.$volume.'';
				if(trim($year)			!= "") $ret = $ret.', '.$year;
				$ret = $ret.".";				
				break;
				
			case "mastersthesis":
			case "phdthesis":
				$school = extractBib("school", $entry, $accents);
				$addrs = extractBib("address", $entry, $accents);
				
				// Decide on link for school
				if(trim($url) != "") {
					$school = '<a href="'.$url.'" target="_blank">'.$school.'</a>';
					$urllinked = true;
				} elseif(trim($doi) != "") {
					$school = '<a href="'.$doi.'" target="_blank">'.$school.'</a>';
					$doilinked = true;
				} elseif(trim($publisherurl) != "") {
					$school = '<a href="'.$publisherurl.'" target="_blank">'.$school.'</a>';
					$publisherlinked = true;
				}
				
				// Build output
				if($type == "mastersthesis") $ret = $ret." Master's Thesis";
				else $ret = $ret." Ph.D. Thesis";
				if(trim($school)!= "") $ret = $ret.', <span class="school">'.$school.'</span>';
				if(trim($addrs)	!= "") $ret = $ret.', '.$addrs;
				if(trim($year)	!= "") $ret = $ret.', '.$year;
				$ret = $ret.".";
				break;
			
			case "techreport":
				$institution = extractBib("institution", $entry, $accents);
				$number = extractBib("number", $entry, $accents);
				$addrs = extractBib("address", $entry, $accents);

				// Decide on link for institution
				if(trim($url) != "") {
					$institution = '<a href="'.$url.'" target="_blank">'.$institution.'</a>';
					$urllinked = true;
				} elseif(trim($doi) != "") {
					$institution = '<a href="'.$doi.'" target="_blank">'.$institution.'</a>';
					$doilinked = true;
				} elseif(trim($publisherurl) != "") {
					$institution = '<a href="'.$publisherurl.'" target="_blank">'.$institution.'</a>';
					$publisherlinked = true;
				}

				// Build output
				$ret = $ret." Technical Report";
				if(trim($number)		!= "") $ret = $ret.' '.$number;
				if(trim($institution)	!= "") $ret = $ret.', <span class="institution">'.$institution.'</span>';
				if(trim($addrs)			!= "") $ret = $ret.', '.$addrs;
				if(trim($year)			!= "") $ret = $ret.', '.$year;
				$ret = $ret.".";
				break;
			
			case "misc":
			case "unpublished":
			case "manual":
				$note = extractBib("note", $entry,$accents);
				if(trim($note) != "") $ret .= ', '.$note;
				break;
		}
		
		// Put a full stop before the links
		$ret = trim($ret);
		if($ret[strlen($ret)-1] != '.') $ret .= '.';
		
		// Links:
		$ret .= '<span class="links">';
		
		if(trim($webpdf) != "") {
			$ret .= ' <span class="webpdf"><a href="'.$webpdf.'" >pdf</a></span>&nbsp;';
		}
		
		$webcs = extractBib("citeseerurl", $entry, $accents);
		if(trim($webcs) != "") {
			$ret .= ' <span class="citeseerurl"><a href="'.$webcs.'" target="_blank">citeseer</a></span>&nbsp;';
		}
		
		if(!$doilinked && trim($doi) != "") {
			$ret .= ' <span class="doi"><a href="'.$doi.'" target="_blank">doi</a></span>&nbsp;';
		}
		
		if(!$urllinked && trim($url) != "") {
			$ret .= ' <span class="url"><a href="'.$url.'" target="_blank">www</a></span>&nbsp;';
		}

		if(!$publisherlinked && trim($publisherurl) != "") {
			$ret .= ' <span class="publisherurl"><a href="'.$publisherurl.'" target="_blank">publisher</a></span>&nbsp;';
		}

		$ret .= '</span>';
		
	}
	
	return $ret;
}

/**
 * Extracts the bibtex entry with the specified key from the specified file.
 * If no such bibtex entry could be found, the function returns false.
 */
function extractBibEntry($filename, $key) {
	return extractBibEntryFromString(file($filename), $key);
}

function extractBibEntryFromString($fileContent, $key) {
	if(is_array($fileContent)) $fileContent = implode("", $fileContent);
	$fileContent = str_replace("\r", "\n", $fileContent);
	$fileContent = str_replace("\n\n", "\n", $fileContent);
	$pos = strpos($fileContent, '{'.$key.',');
	if($pos === false) return false;	
	else {
		while(substr($fileContent, $pos, 1) != '@' && $pos >= -1) $pos--;
		if($pos == -1) return false;
		else {
			$braceLevel = 0;
			for($i = strpos($fileContent, '{', $pos); $i < strlen($fileContent); $i++) {
				if(substr($fileContent, $i, 1) == '{') $braceLevel++;
				if(substr($fileContent, $i, 1) == '}') $braceLevel--;
				if($braceLevel == 0) {
					return substr($fileContent, $pos, $i - $pos + 1);
				}
			}
			return false;
		}
	}
}

/**
 * bibfile2html($filename):
 * Returns an enumerated list of all entries contained in a BibTeX file.  
 * Comments and encoding notes are ignored.
 * 
 * For documentation see top of the script.
 * 
 * @author Andreas Classen
 */
function bibfile2html($filename, $displayTypes = NULL, $groupType = NULL, $groupYear = NULL, $bibLink = NULL, $highlightName = NULL, $numbersDesc = NULL, $sorting = NULL, $authorLimit = NULL, $abstractLink = NULL) {
	return bibstring2html(file($filename), $displayTypes, $groupType, $groupYear, $bibLink, $highlightName, $numbersDesc, $sorting, $authorLimit, $abstractLink);
}

/**
 * Returns an enumerated list of all entries contained in the passed string.
 * Comments and encoding notes are ignored.
 * 
 * For documentation see top of the script.
 * 
 * @author Andreas Classen
 */
function bibstring2html($fileContent, $displayTypes = NULL, $groupType = NULL, $groupYear = NULL, $bibLink = NULL, $highlightName = NULL, $numbersDesc = NULL, $sorting = NULL, $authorLimit = NULL, $abstractLink = NULL) {
	// Default parameter values
	
	if($displayTypes === null) {
		$displayTypes = array(	'article' => 'Articles',
							 	'book' => 'Books',
								'booklet' => 'Booklets',
								'conference' => 'Conferences',
								'inbook' => 'Book chapters',
								'incollection' => 'Collections',
								'inproceedings' => 'In Proceedings',
								'manual' => 'Manuals',
								'mastersthesis' => "Master's Theses",
								'misc' => 'Misc',
								'phdthesis' => 'PhD Theses',
								'proceedings' => 'Conference Proceedings',
								'techreport' => 'Technical Reports',
								'unpublished' => 'Unpublished',
								'_unknown' => 'Other');
	} 
	if($groupType === null) $groupType = true;
	if($groupYear === null) $groupYear = true;
	if($bibLink === null) $bibLink = '';
	if($abstractLink === null) $abstractLink = '';
	if($highlightName === null) $highlightName = '';
	if($numbersDesc === null) $numbersDesc = false;
	if($authorLimit === null) $authorLimit = 0;

	// Preparation
	$accentTable = make_accent_table();
	if(!is_array($fileContent)) $fileContent = explode("\n", $fileContent);
	
	// The $entries array will hold the formatted bibtex entries.
	// Structure:
 	//   - If grouping by types is activated, then it is first indexed by type, then by year
	//   - Otherwise, it is indexed by year first.
	// At each index is an assocative array with three keys 
	//   - text: the formatted bibtex entry
	//	 - bib: the original bibtex entry
	//   - key: the key of the entry
	$entries = array();
	$i = 0;
	$j = 0;
	$len = count($fileContent);
	for($i = 0; $i < $len; $i++) {
		if(substr($fileContent[$i], 0, 1) == '@') {
			// Start of new entry
			$type = trim(strtolower(substr($fileContent[$i], 1, strpos($fileContent[$i], '{') - 1)));
			
			// First we read the bibtex entry into the $bibEntry variable
			$braceLevel = 0;
			$bibEntry = '';
			$eoe = false;
			for($l = $i; $l < $len && !$eoe; $l++) {
				$fileContent[$l] = rtrim($fileContent[$l]);
				$braceLevel += substr_count($fileContent[$l], '{') - substr_count($fileContent[$l], '}');
				if($braceLevel == 0) {
					$eoe = true;
					$bibEntry .= substr($fileContent[$l], 0, strrpos($fileContent[$l], '}'))." ";
				} else {
					$bibEntry .= $fileContent[$l]." ";
				}
			}
			$i = $l-1;
			
			// Collect info about the entry
			$bibEntry = trim(str_replace(array("\n", "\r", "\t"), array(' ', ' ', ' '), $bibEntry));
			$key  = extractBibName($bibEntry);
			$year = extractBib("year", $bibEntry, $accentTable);
			$text = bibtex2html($bibEntry, $type, $accentTable, $highlightName, $authorLimit);
			
			if($abstractLink != '' && trim(extractBib("abstract", $bibEntry, $accentTable)) != '') {
				$text .= ' <span class="abstractlink"><a href="'.str_replace('%key', $key, $abstractLink).'">Abstract</a></span>';
			}
			
			if($bibLink != '') {
				$text .= ' <span class="bibtexlink"><a href="'.str_replace('%key', $key, $bibLink).'">BibTeX</a></span>';
			}
			
			// The index has to be unique; Entries will be sorted along this index before printed
			// it can be used to control the display order of the entries
			if($sorting == null) $index = sprintf("%08d", $j);
			else {
				if(!is_array($sorting)) $sorting = array($sorting);
				
				$index = '';
				foreach($sorting as $field) {
					if($field == 'key') $index .= $key;
					else if($field == 'citation') $index .= substr(strip_tags(html_entity_decode($text)), 0, 200);
					else if($field == 'year') $index .= sprintf("%04d", 9999 - (int) extractBib("year", $bibEntry, $accentTable));
					else if($field == 'timestamp') $index .= sprintf("%010d", 2147483647 - strtotime(extractBib("timestamp", $bibEntry, $accentTable)));
					else if($field == 'author') {
						$temp = html_entity_decode(extractBib("author", $bibEntry, $accentTable));
						if($temp == "") $temp = html_entity_decode(extractBib("editor", $bibEntry, $accentTable));
						$index .= formatAuthors($temp, "", $authorLimit);
					} else {
						$index .= html_entity_decode(extractBib($field, $bibEntry, $accentTable));
					}
				}
				
				$index .= sprintf(" %s %08d", $key, $j); // make it unique
			}

			$element = array('text' => $text,
		   					 'bib'  => $bibEntry,
		   					 'key'  => $key);
			
			// Save
			if(!array_key_exists($type, $displayTypes)) $type = '_unknown';
			if($year != '' && $text != '') {
				if($groupType && $groupYear) $entries[$type][$year][$index] = $element;
				elseif($groupYear) $entries[$year][$index] = $element;
				elseif($groupType) $entries[$type][$index] = $element;
				else $entries[$index] = $element;
				$j++;
			}
		}
	} 
	
	$ret = ''; // contains the output that will be returned
	if(!$numbersDesc) $j = 1;
	if($groupType && $groupYear) {
		foreach($displayTypes as $type => $typeName) {
			if(isset($entries[$type])) {
				krsort($entries[$type]);
				$ret .= '<h2>'.$typeName.'</h2>';
				foreach($entries[$type] as $year => $yearEntries) {
					$ret .= '<h3>'.$year.'</h3>';
					$ret .= '<ol start="'.$j.'">';
					uksort($yearEntries, 'strcoll');
					foreach($yearEntries as $index => $info) {
						if(trim($info['text']) != '') $ret .= '<li>'.$info['text'].'</li>';
						$j = $numbersDesc ? $j - 1 : $j + 1;
					}
					$ret .= '</ol>';
				}	
			}
		}
		
	} elseif($groupType) {
		foreach($displayTypes as $type => $typeName) {
			if(isset($entries[$type])) {
				uksort($entries[$type], 'strcoll');
				$ret .= '<h2>'.$typeName.'</h2>';
				$ret .= '<ol start="'.$j.'">';
				foreach($entries[$type] as $index => $info) {
					if(trim($info['text']) != '') $ret .= '<li>'.$info['text'].'</li>';
					$j = $numbersDesc ? $j - 1 : $j + 1;
				}
				$ret .= '</ol>';
			}
		}
		
	} elseif($groupYear) {	
		krsort($entries);
		foreach($entries as $year => $yearEntries) {
			$ret .= '<h2>'.$year.'</h2>';
			$ret .= '<ol start="'.$j.'">';
			uksort($yearEntries, 'strcoll');
			foreach($yearEntries as $index => $info) {
				if(trim($info['text']) != '') $ret .= '<li>'.$info['text'].'</li>';
				$j = $numbersDesc ? $j - 1 : $j + 1;
			}
			$ret .= '</ol>';
		}
		
	} else {	
		uksort($entries, 'strcoll');
		$ret .= '<ol start="'.$j.'">';
		foreach($entries as $index => $info) {
			if(trim($info['text']) != '') $ret .= '<li>'.$info['text'].'</li>';
			$j = $numbersDesc ? $j - 1 : $j + 1;
		}
		$ret .= '</ol>';
	}
	return $ret;
}

?>