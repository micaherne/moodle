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

namespace qbank_columnsortorder\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\url;

/**
 * Renderable for resetting customised column settings.
 *
 * This will display a link that resets all customised column settings and redirects back to the current page.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_columns implements renderable, templatable {
    /** @var \moodle_url The current page URL to redirect back to. */
    protected url $returnurl;

    /** @var bool True if we are changing global config, false for user preferences. */
    protected bool $global;

    /**
     * Store data for generating the template context.
     *
     * @param \moodle_url $returnurl
     * @param bool $global
     */
    public function __construct(url $returnurl, bool $global = false) {
        $this->returnurl = $returnurl;
        $this->global = $global;
    }

    public function export_for_template(renderer_base $output): array {
        $reseturl = new url('/question/bank/columnsortorder/actions.php', [
            'action' => 'reset',
            'global' => $this->global,
            'sesskey' => sesskey(),
            'returnurl' => $this->returnurl->out(),
        ]);
        return [
            'reseturl' => $reseturl->out(false),
        ];
    }
}
