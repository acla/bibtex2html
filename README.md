bibtex2html
===========

bibtex2html is an easy-to-use PHP script (also available as a Wordpress plugin) which converts a BibTeX file into Html code, producing a list of the publications contained in the file.

The script is particularly useful if you want to include a list of your publications on your website, without having to worry about updating the .html file each time a publication is added or something else changes. As you have your bibliography in a BibTeX file anyway, you simply replace the online BibTeX file (an operation which is quick and easy).

The script is also available as a *plugin for Wordpress*. The plugin, called bibtex adds a new shortcode [bibtex]...[/bibtex] which you can use in posts or pages to display a list of BibTeX entries in nice Html. The BibTeX entries are stored inside a regular Wordpress page or post, not inside a separate file. This makes it easy to edit them with the normal user interface.

This content was originally hosted at [classen.be/bibtex2html] [1].


Instructions for the script
---------------------------

The script is easy to use, you only need to add two lines of PHP code to your webpage; there is nothing to configure whatsoever. If you are used to managing your bibliography with a BibTeX-based management tool like [JabRef] [2], this is all quite straightforward.

There are several parameters that allow you to configure how the entries are grouped/ordered, and which group titles are used. This, and instructions about how to change the layout of a listing can be found in the short documentation (the first part of the php file).

It is also possible to place a link opening a popup showing the BibTeX of an entry (for the visitor to copy/paste).


Instructions for the Wordpress plugin
-------------------------------------

The Wordpress plugin is even easier to use. To instal it, unzip the archive and upload its contents into the wp-content/plugins/ folder; then activate the plugin in the plugin settings page. That's it. To show a list of BibTeX entries, just paste the raw BibTeX into the content of a page or post and enclose it with the shortcodes [bibtex]...[/bibtex], like so:

    This following is a list of nicely formatted publications:
    [bibtex]
    @article{foobar, 
       author = {...},
       ...
    } 
    ...
    @inproceedings{toto,
       author = {...},
       ...
    }
    [/bibtex] 

There can be multiple lists inside the same page.

The following shortcode parameters are available (they correspond to the parameters of the script):

* *groupyear:* entries are grouped by year (can be combined with grouptype);
* *grouptype:* entries are grouped by type (can be combined with groupyear);
* *desc:* numbers all entries in descending order;
* *authorlimit=integer:* limit the number of authors shown to integer, some integer value (the script prints "et al." when the limit is exceeded);
* *sort=list,of,sort,criteria:* sort the entries according to the specified criteria (have a look at the comments of the PHP script for further explanations);
* *highlight=secondname:* highlight the author with the specified second name (case insensitive). The script puts a span with class "highlight" around it, so you have to define the class in your CSS to format it appropriately. Note that the Wordpress plugin does not support spaces in the second name.

Multiple shortcode parameters are simply separated by spaces. There can be no spaces around the = sign. Example:

    [bibtex grouptype sort=year,author authorlimit=3 
            highlight=Classen]
    ...
    [/bibtex]

Other advanced settings of the raw script (like changing the names of the entry types) are not yet available in the Wordpress plugin version.

There will be a "BibTeX" link behind every entry; clicking on it will open a popup window with the raw BibTeX of the entry. There will also be a link titled "Abstract" if the abstract of an entry is specified; clicking it will open once again a popup with the abstract. The popup window includes a reference to the stylesheet of the current Wordpress theme and the body of the window has class bibtex-display, which can be used to format it appropriately.

Instructions about how to change the layout of a listing can be found in the short documentation (the first part of the bibtex2html.php file inside the plugin folder).


General remarks
---------------

bibtex2html interprets several non-standard BibTeX fields:

* *url:* The content of this field is assumed to be the url of the venue. It is used on the "in" part of the citation. If there is no "in" part, a "more..." link is displayed at the end.
* *webpdf:* This is assumed to be a link to the pdf file of the given publication (the title becomes clickable and a "pdf..." link is displayed).
* *publisherurl:* The url of the publisher, used on the "publisher" part of the citation. If there is none, a "publisher..." link is displayed at the end.
* *citeseerurl:* The citeseer url (displayed as "citeseer...").
* *doi:* This is supposed to be DOI name from dx.doi.org (displayed as "doi..."). 

Note that the script does not interpret BibTeX strings (abbreviations), crossrefs or similar things, maybe I'll add this one day. If you need a more comprehensive BibTeX parser, check out [bibliophile.sourceforge.net] [3].

Feel free to use/adapt this script like you want. If you detect errors in the presentation of a given entry, please send me a description of the error so I can correct it in subsequent versions.


Thanks to
---------

* [Johannes Knabe] [4] for the original script.
* [Eric Sommerlade] [5] who added support for accents in the correct BibTeX style.
* [Johnnie Chan] [6] who fixed some bugs.


  [1]: http://www.classen.be/bibtex2html/   "classen.be/bibtex2html"
  [2]: http://jabref.sourceforge.net/       "JabRef"
  [3]: http://bibliophile.sourceforge.net/  "bibliophile.sourceforge.net"
  [4]: http://bibscript.panmental.de/       "Johannes Knabe"
  [5]: http://www.sommerla.de/              "Eric Sommerlade"
  [6]: http://www.johnniechan.com/          "Johnnie Chan"

