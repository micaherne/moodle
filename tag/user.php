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

require_once('../config.php');
require_once('lib.php');

$action = optional_param('action', '', PARAM_ALPHA);

require_login();

if (empty($CFG->usetags)) {
    throw new \moodle_exception('tagdisabled');
}

if (isguestuser()) {
    throw new \moodle_exception('noguest');
}

if (!confirm_sesskey()) {
    throw new \moodle_exception('sesskey');
}

$usercontext = context_user::instance($USER->id);

switch ($action) {
    case 'addinterest':
        if (!core_tag_tag::is_enabled('core', 'user')) {
            throw new \moodle_exception('tagdisabled');
        }
        $tag = required_param('tag', PARAM_TAG);
        core_tag_tag::add_item_tag('core', 'user', $USER->id, $usercontext, $tag);
        $tc = core_tag_area::get_collection('core', 'user');
        redirect(core_tag_tag::make_url($tc, $tag));
        break;

    case 'removeinterest':
        if (!core_tag_tag::is_enabled('core', 'user')) {
            throw new \moodle_exception('tagdisabled');
        }
        $tag = required_param('tag', PARAM_TAG);
        core_tag_tag::remove_item_tag('core', 'user', $USER->id, $tag);
        $tc = core_tag_area::get_collection('core', 'user');
        redirect(core_tag_tag::make_url($tc, $tag));
        break;

    case 'flaginappropriate':
        require_capability('moodle/tag:flag', context_system::instance());
        $id = required_param('id', PARAM_INT);
        $tagobject = core_tag_tag::get($id, '*', MUST_EXIST);
        $tagobject->flag();
        redirect($tagobject->get_view_url(), get_string('responsiblewillbenotified', 'tag'));
        break;

    default:
        throw new \moodle_exception('unknowaction');
        break;
}
