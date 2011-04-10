<?php
/**
 * Scratch Embed
 * A Moodle filter to embed MIT Scratch projects from the galleries at
 *     http://mit.scratch/edu/galleries/
 *
 * Compatible with Moodle 1.9 and 2.0.
 *
 * NOTE: this software is in no way endorsed by or affiliated with
 *     the official MIT Scratch project.
 *
 * @category  Moodle4-9
 * @author    N.D.Freear, April 2011 <nfreear @ yahoo.co.uk>
 * @copyright (c) 2011 Nicholas Freear {@link http://freear.org.uk}.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @link      http://freear.org.uk/#moodle
 */
/**
Usage:

Enable the filter (admin). Then, type the following:

  [Scratch] http://scratch.mit.edu/projects/technoguyx/355353 [/Scratch]

*/

//  This filter will replace any [Scratch] URL_OF_PROJECT_PAGE [/Speak] with
//  the Java applet.


/** Moodle 1.9.x filter function (also works with Moodle 2.0).
*/
function scratchembed_filter($courseid, $text) {

    if (!is_string($text)) {
        // non string data can not be filtered anyway
        return $text;
    }
    // Copy the input text. Fullclone is slow and not needed here.
    $newtext = $text;

    $search = "#\[Scratch\] *(http:\/\/scratch.mit.edu\/projects\/(\w+)/(\d*))\/? *\[\/?Scratch\]#ims";
    $newtext = preg_replace_callback($search, '_scratchembed_callback', $newtext);

    if (is_null($newtext) or $newtext === $text) {
        // error or not filtered
        return $text;
    }

    return $newtext;
}

function _scratchembed_callback($matches) {
    static $call_count = 0;
    $call_count++;

    // $proj_base must be relative to work successfully in the applet.
    $proj_base= '../../static/projects';
    $imag_base = 'http://scratch.mit.edu/static/projects';

    $defaults = array(
      'codebase'=> 'http://scratch.mit.edu/static/misc',
      'archive' => 'ScratchApplet.jar',
      'code'    => 'ScratchApplet', #.class.
      /*'author'=> 'technoguyx',
      'project_id'=> 355353,
      'project_url'=>'../../static/projects/technoguyx/355353.sb',
      'image_url' => "$proj_base/technoguyx/355353_med.png",
      'thumb_url' => "$proj_base/technoguyx/355353_sm.png",*/
    );

    $config['page_url'] = $matches[1];
    $config['author']   = $matches[2];
    $config['project_id']=$matches[3];

    $conf = (object) array_merge($defaults, $config);

    $conf->project_url="$proj_base/$conf->author/$conf->project_id.sb";
    $conf->image_url = "$imag_base/$conf->author/{$conf->project_id}_med.png";
    $conf->thumb_url = "$imag_base/$conf->author/{$conf->project_id}_sm.png";

    return _scratchembed_markup($conf);
}

function _scratchembed_markup($conf) {
    global $CFG;
    $license_icon = "$CFG->wwwroot/filter/scratchembed/cc-by-sa.png";

    // This is a Java Applet embedded using <object> that is compatible with
    // HTML5, and backwards compatible with all browsers.
    //     See, http://freear.org.uk/content/embed-scratch-applet-html5
    $newtext = <<<EOF

<div class="scratchembed" >
<object tabindex="0" type="application/x-java-applet" height="387" width="482">
 <param name="codebase" value="$conf->codebase" /><!--Generic Applet params. -->
 <param name="archive" value="$conf->archive" />
 <param name="code" value="$conf->code" />
 <param name="project" value="$conf->project_url" /><!--Scratch params. -->
 <pre>[ Your browser needs Java enabled to view Scratch projects. ]</pre>
 <img alt="" src="$conf->image_url" />
</object><div>Explore <a rel="bookmark" href="$conf->page_url">the project by $conf->author on Scratch</a> &bull;
<a rel="license" style="background:url($license_icon)no-repeat left; padding-left:34px;" href="http://scratch.mit.edu/pages/license">Some rights reserved</a></div> 
</div>

EOF;
    /*Styles.
    style="background:url(http://scratch.mit.edu/favicon.ico)no-repeat bottom left;"
    style="padding:0 18px;"
    */
    return $newtext;
}

#End.
