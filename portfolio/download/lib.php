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

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/plugin.php');

class portfolio_plugin_download extends portfolio_plugin_pull_base {

    protected $exportconfig;

    public static function get_name() {
        return get_string('pluginname', 'portfolio_download');
    }

    public static function allows_multiple_instances() {
        return false;
    }

    public function expected_time($callertime) {
        return PORTFOLIO_TIME_LOW;
    }

    public function prepare_package() {

        $files = $this->exporter->get_tempfiles();

        if (count($files) == 1) {
            $this->set('file', array_shift($files));
        } else {
            $this->set('file', $this->exporter->zip_tempfiles());  // this will throw a file_exception which the exporter catches separately.
        }
    }

    public function steal_control($stage) {
        if ($stage == PORTFOLIO_STAGE_FINISHED) {
            global $CFG;
            return $CFG->wwwroot . '/portfolio/download/file.php?id=' . $this->get('exporter')->get('id');
        }
    }

    public function send_package() {}

    public function verify_file_request_params($params) {
        // for download plugin the only thing we need to verify is that
        // the logged in user is the same as the exporting user
        global $USER;
        if ($USER->id  != $this->user->id) {
            return false;
        }
        return true;
    }

    public function get_interactive_continue_url() {
        return false;
    }
}

