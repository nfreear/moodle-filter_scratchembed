<?php
/**
 * Scratch Embed
 * A Moodle filter to embed Scratch projects hosted at
 *     http://mit.scratch/edu/galleries/
 *
 * Compatible with Moodle 1.9 and 2.0.
 *
 * NOTICE: this software is in no way endorsed by or affiliated with
 *     the official MIT Scratch project or team.
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
      'code'    => 'ScratchApplet', //(.class)
      'license' => 'http://scratch.mit.edu/pages/license',

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

    $str_attrib = get_string('attrib', 'filter_scratchembed', $conf->author);
    $str_rights = get_string('rights', 'filter_scratchembed');
    $str_nojava = get_string('nojava', 'filter_scratchembed');
    $script = _filter_scratchembed_panel_script($conf);

    // This is a Java Applet embedded using <object> that is compatible with
    // HTML5, and backwards compatible with all browsers.
    //     See, http://freear.org.uk/content/embed-scratch-applet-html5
    $newtext = <<<EOF

<div class="scratchembed  yui-skin-sam">
<object tabindex="0" type="application/x-java-applet" height="387" width="482">
 <param name="codebase" value="$conf->codebase" /><!--Generic Applet params. -->
 <param name="archive" value="$conf->archive" />
 <param name="code" value="$conf->code" />
 <param name="project" value="$conf->project_url" /><!--Scratch params. -->
 <pre>[ $str_nojava ]</pre>
 <img alt="" src="$conf->image_url" />
</object><div><a rel="bookmark" href="$conf->page_url">$str_attrib</a> &bull;
<a rel="license" style="background:url($license_icon)no-repeat left; padding-left:35px;" href="http://scratch.mit.edu/pages/license">$str_rights</a>
$script
</div></div>

EOF;
    return $newtext;
}

/** Click a button to show embed code in a panel.
*/
function _filter_scratchembed_panel_script($conf) {
    global $CFG;
    static $count = 0;
    $count++;
    $prefix = "scratchem_{$count}_";
    $str_embedbtn= get_string('embedbutton', 'filter_scratchembed');
    $str_toembed = get_string('toembed', 'filter_scratchembed');

    //Involved!  http://docs.moodle.org/en/Development:JavaScript_guidelines
    $script ='';

    if (1==$count) {
        $yui_dir = $CFG->dirroot."/lib/yui/2.8.2/build/";
        if (file_exists($yui_dir)) {
            // Moodle 2.
            $yui_base = $CFG->wwwroot."/lib/yui/2.8.2/build";
        } else {
            // Moodle 1.9 fallback.
            $yui_base = $CFG->wwwroot."/lib/yui";
        }

        // http://developer.yahoo.com/yui/container/panel/
        // http://developer.yahoo.com/yui/examples/container/panel.html
        //yui.yahooapis.com/2.9.0/build/container/assets/skins/sam/container.css
        //Optional draggable: $yui_base/dragdrop/dragdrop-min.js.
        $script .= <<<EOF
<script src="$yui_base/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="$yui_base/container/container-min.js" type="text/javascript"></script>
EOF;
    }

    $script .= <<<EOF
  <div id="{$prefix}panel">
    <div class="hd">$str_toembed</div>
	<pre class="bd">[Scratch] $conf->page_url [/Scratch]</pre><!--<div class="ft">Foot</div>-->
  </div>
  <button id="{$prefix}show">$str_embedbtn</button>
  <script type="text/javascript">
   YAHOO.namespace("scratch.em");
   YAHOO.scratch.em.panel$count=new YAHOO.widget.Panel("{$prefix}panel", {width:"590px", visible:false, constraintoviewport:true});
   YAHOO.scratch.em.panel$count.render();
   YAHOO.util.Event.addListener("{$prefix}show", "click",
   YAHOO.scratch.em.panel$count.show, YAHOO.scratch.em.panel$count, true);
  </script>
EOF;
    return $script;
}

#End.
