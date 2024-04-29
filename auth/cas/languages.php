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

$caslangconstprefix = 'PHPCAS_LANG_';
$caslangprefixlen = strlen('CAS_Languages_');
$CASLANGUAGES = array ();

$consts = get_defined_constants(true);
foreach ($consts['user'] as $key => $value) {
    if (preg_match("/^$caslangconstprefix/", $key)) {
        $CASLANGUAGES[$value] = substr($value, $caslangprefixlen);
    }
}
if (empty($CASLANGUAGES)) {
    $CASLANGUAGES = array (PHPCAS_LANG_ENGLISH => 'English',
                           PHPCAS_LANG_FRENCH => 'French');
}
