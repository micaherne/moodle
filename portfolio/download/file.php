<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

// this script is a slightly more user friendly way to 'send' the file to them
// (using portfolio/file.php) but still give them the 'return to where you were' link
// to go back to their assignment, or whatever

require(__DIR__.'/../../config.php');

if (empty($CFG->enableportfolios)) {
    throw new \moodle_exception('disabled', 'portfolio');
}

require_once($CFG->libdir.'/portfoliolib.php');
require_once($CFG->libdir.'/portfolio/exporter.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/portfolio/download/file.php', array('id' => $id));

$exporter = portfolio_exporter::rewaken_object($id);
portfolio_export_pagesetup($PAGE, $exporter->get('caller'));
$exporter->verify_rewaken();

$exporter->print_header(get_string('downloading', 'portfolio_download'), false);
$returnurl = $exporter->get('caller')->get_return_url();
echo $OUTPUT->notification('<a href="' . $returnurl . '">' . get_string('returntowhereyouwere', 'portfolio') . '</a><br />');


// if they don't have javascript, they can submit the form here to get the file.
// if they do, it does it nicely for them.
echo '<div id="redirect">
    <form action="' . $exporter->get('instance')->get_base_file_url() . '" method="post" id="redirectform" target="download-iframe">
      <input type="submit" value="' . get_string('downloadfile', 'portfolio_download') . '" />
    </form>
    <iframe class="d-none" name="download-iframe" src=""></iframe>
    </div>
';

$PAGE->requires->js_amd_inline("
require(['jquery'], function($) {
    $('#redirectform').submit(function() {
        $('#redirect').addClass('hide');
    }).submit();
});");
echo $OUTPUT->footer();
