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
 * TeX filter settings
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2007 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_admin\setting\setting\configcolourpicker;
use core_admin\setting\setting\configexecutable;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\configtextarea;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot.'/filter/tex/lib.php');

    $items = array();
    $items[] = new heading('filter_tex/latexheading', get_string('latexsettings', 'filter_tex'), '');
    $items[] = new configtextarea('filter_tex/latexpreamble', get_string('latexpreamble','filter_tex'),
                   '', "\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n");
    $items[] = new configcolourpicker('filter_tex/latexbackground', get_string('backgroundcolour', 'admin'), '', '#FFFFFF');
    $items[] = new configtext('filter_tex/density', get_string('density', 'admin'), '', '120', PARAM_INT);

    $default_filter_tex_pathlatex   = '';
    $default_filter_tex_pathdvips   = '';
    $default_filter_tex_pathdvisvgm = '';
    $default_filter_tex_pathconvert = '';
    if (PHP_OS=='Linux') {
        $default_filter_tex_pathlatex   = "/usr/bin/latex";
        $default_filter_tex_pathdvips   = "/usr/bin/dvips";
        $default_filter_tex_pathdvisvgm = "/usr/bin/dvisvgm";
        $default_filter_tex_pathconvert = "/usr/bin/convert";
    } else if (PHP_OS=='Darwin') {
        // most likely needs a fink install (fink.sf.net)
        $default_filter_tex_pathlatex   = "/sw/bin/latex";
        $default_filter_tex_pathdvips   = "/sw/bin/dvips";
        $default_filter_tex_pathdvisvgm = "/usr/bin/dvisvgm";
        $default_filter_tex_pathconvert = "/sw/bin/convert";

    } else if (PHP_OS=='WINNT' or PHP_OS=='WIN32' or PHP_OS=='Windows') {
        // note: you need Ghostscript installed (standard), miktex (standard)
        // and ImageMagick (install at c:\ImageMagick)
        $default_filter_tex_pathlatex   = "c:\\texmf\\miktex\\bin\\latex.exe";
        $default_filter_tex_pathdvips   = "c:\\texmf\\miktex\\bin\\dvips.exe";
        $default_filter_tex_pathdvisvgm   = "c:\\texmf\\miktex\\bin\\dvisvgm.exe";
        $default_filter_tex_pathconvert = "c:\\imagemagick\\convert.exe";
    }

    $pathlatex = get_config('filter_tex', 'pathlatex');
    $pathdvips = get_config('filter_tex', 'pathdvips');
    $pathconvert = get_config('filter_tex', 'pathconvert');
    $pathdvisvgm = get_config('filter_tex', 'pathdvisvgm');
    if (strrpos($pathlatex . $pathdvips . $pathconvert . $pathdvisvgm, '"') or
            strrpos($pathlatex . $pathdvips . $pathconvert . $pathdvisvgm, "'")) {
        set_config('pathlatex', trim($pathlatex, " '\""), 'filter_tex');
        set_config('pathdvips', trim($pathdvips, " '\""), 'filter_tex');
        set_config('pathconvert', trim($pathconvert, " '\""), 'filter_tex');
        set_config('pathdvisvgm', trim($pathdvisvgm, " '\""), 'filter_tex');
    }

    $items[] = new configexecutable('filter_tex/pathlatex', get_string('pathlatex', 'filter_tex'), '', $default_filter_tex_pathlatex);
    $items[] = new configexecutable('filter_tex/pathdvips', get_string('pathdvips', 'filter_tex'), '', $default_filter_tex_pathdvips);
    $items[] = new configexecutable('filter_tex/pathconvert', get_string('pathconvert', 'filter_tex'), '', $default_filter_tex_pathconvert);
    $items[] = new configexecutable('filter_tex/pathdvisvgm', get_string('pathdvisvgm', 'filter_tex'), '', $default_filter_tex_pathdvisvgm);

    // The update callback checks whether required paths actually point to executables.
    // If they don't, we force the setting to PNG as the default fallback format.
    $formats = ['png' => 'PNG', 'gif' => 'GIF', 'svg' => 'SVG'];
    $items[] = new configselect(
        'filter_tex/convertformat',
        get_string('convertformat', 'filter_tex'),
        get_string('configconvertformat', 'filter_tex'),
        'png',
        $formats
    );

    foreach ($items as $item) {
        $item->set_updatedcallback('filter_tex_updatedcallback');
        $settings->add($item);
    }
}
