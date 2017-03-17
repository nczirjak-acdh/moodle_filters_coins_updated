<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter to find COinS (ContextObject in Span http://ocoins.info/) and create an OpenURL
 *
 * @package    filter
 * @subpackage coins
 * @copyright Owen Stephens  {@link http://ostephens.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_coins extends moodle_text_filter {

	public function filter($text, array $options = array()) {
            
            global $CFG;

            if (strpos($text, 'z3988') === false && strpos($text, 'Z3988') === false) {
                    //no COinS
                    return $text;
            }
            /// There might be an COinS in here somewhere so continue ...
            $matches = array();
            
            /// regular expression to define a standard email string.
            $coinsregex = '/<span class="[Zz]3988" title="(.*)"><\/span>/';
            
            $text = preg_replace_callback($coinsregex, 'filter_coins_addlink', $text);

            return $text;
    }
}

function filter_coins_addlink($matches) {
	global $CFG;
        
        
        $returnString = "";
        
        $author = array();
        
        ///items/q/ABaC%3Aus – Austrian Baroque Corpus 2015
        //rft.atitle
        $matchesTitle = explode('&', $matches[1]);        
       
        $searchwordTitleA = 'rft.atitle';
        $searchwordTitleB = 'rft.btitle';
        $searchwordTitle = 'rft.title';
        $searchwordAuthor = 'rft.au';
        $searchwordPublication = 'rft.jtitle';
        $searchwordIssue = 'rft.issue';
        $searchwordDate = 'rft.date';
        $searchwordDoi = 'rft_id';
        
        foreach($matchesTitle as $k=>$v) {
            if(preg_match("/\b$searchwordTitleA\b/i", $v)) {
                $title = str_replace("amp;rft.atitle=","",$v);
            }else if(preg_match("/\b$searchwordTitleB\b/i", $v)) {
                $title = str_replace("amp;rft.btitle=","",$v);
            }elseif(preg_match("/\b$searchwordTitle\b/i", $v)) {
                $title = str_replace("amp;rft.title=","",$v);
            }
            
            if(preg_match("/\b$searchwordAuthor\b/i", $v)) {
                $author[] = str_replace("amp;rft.au=","",$v);
            }
            
            if(preg_match("/\b$searchwordPublication\b/i", $v)) {
                $publication = str_replace("amp;rft.jtitle=","",$v);
            }
            
            if(preg_match("/\b$searchwordIssue\b/i", $v)) {
                $issue = str_replace("amp;rft.issue=","",$v);
            }
            if(preg_match("/\b$searchwordDate\b/i", $v)) {
                $date = str_replace("amp;rft.date=","",$v);                
            }
            if(preg_match("/\b$searchwordDoi\b/i", $v)) {
                $doi = urldecode(str_replace("amp;rft_id=","",$v));                
                $doi = str_replace("info:", "", $doi);
            }
        }
       
        $authors = implode(",", $author);
       
        $returnString = '<div id="zotero_div">'.urldecode($authors).'. <a href="'.$CFG->filter_coins_baseurl.'/items/q/'.$title.
				'" target="_blank">'.urldecode($title).'.</a> '.urldecode($publication).'. '.urldecode($issue).'. '.urldecode(date("d-m-Y",strtotime($date))).'. </div>';
        
        return $returnString;
}
