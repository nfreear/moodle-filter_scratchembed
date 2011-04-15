Scratch embed
=============

A Moodle filter plug-in to embed Scratch projects using the Scratch Java applet. It embeds community software projects hosted at, <http://scratch.mit.edu/galleries>

NOTICE: this software is in no way endorsed by or affiliated with the official MIT Scratch project or team.

Installation
------------
To install (on Moodle 2):

1. Un-compress the Zip/Gzip archive, and copy the folder renamed 'scratchembed' to your moodle/filter/ directory.
2. Log in to Moodle as admininstrator, go to Site Administration | Plugins | Filters | Manage Filters.
3. Choose 'On' or 'Off but available' in the drop-down menu next to 'Scratch embed'.

Usage
-----
The syntax to embed a project:

    [Scratch] http://scratch.mit.edu/projects/technoguyx/355353 [/Scratch]

Links
-----
* Moodle plugin page: <http://moodle.org/mod/data/view.php?d=13&rid=4714>
* Code, Git: <https://github.com/nfreear/moodle-filter_scratchembed>
* Also, Hg:  <https://bitbucket.org/nfreear/scratchembed>
* Demo (todo): <http://freear.org.uk/moodle>
* "Why square brackets?", <http://bitbucket.org/nfreear/timelinewidget/src/tip/filter.php#cl-36>

Notes
-----
* Tested in Moodle 1.9.7 and 2.0.2.
* No javascript, no database access - very simple!
* The ScratchApplet Java applet must be in the same domain as the Scratch project.
* Filter syntax is case-insensitive.
* The plug-in is internationalized in Moodle 1.9 and 2.
* If there is demand I'll look at embedding locally hosted Scratch projects (.sb files).
* Similarly, I may look at an alternative filter syntax.

Notices
-------
Scratch embed, Copyright © 2011 Nicholas Freear.

* License: <http://www.gnu.org/copyleft/gpl.html> GNU GPL v2 or later.

