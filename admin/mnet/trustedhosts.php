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

    // Allows the admin to configure services for remote hosts

    require(__DIR__.'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    admin_externalpage_setup('trustedhosts');


    if (!extension_loaded('openssl')) {
        echo $OUTPUT->header();
        throw new \moodle_exception('requiresopenssl', 'mnet', '', null, true);
    }

    $site = get_site();

    $trusted_hosts = '';//array();
    $old_trusted_hosts = get_config('mnet', 'mnet_trusted_hosts');
    if (!empty($old_trusted_hosts)) {
        $old_trusted_hosts =  explode(',', $old_trusted_hosts);
    } else {
        $old_trusted_hosts = array();
    }

    $test_ip_address = optional_param('testipaddress', NULL, PARAM_HOST);
    $in_range = false;
    if (!empty($test_ip_address)) {
        foreach($old_trusted_hosts as $host) {
            if (address_in_subnet($test_ip_address, $host)) {
                $in_range = true;
                $validated_by = $host;
                break;
            }
        }
    }

    /// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        $hostlist = preg_split("/[\s,]+/", $form->hostlist);
        foreach($hostlist as $host) {
            list($address, $mask) = explode('/', $host.'/');
            if (empty($address)) continue;
            if (strlen($mask) == 0) $mask = 32;
            $trusted_hosts .= trim($address).'/'.trim($mask)."\n";
            unset($address, $mask);
        }
        set_config('mnet_trusted_hosts', str_replace("\n", ',', $trusted_hosts), 'mnet');
    } elseif (!empty($old_trusted_hosts)) {
        foreach($old_trusted_hosts as $host) {
            list($address, $mask) = explode('/', $host.'/');
            if (empty($address)) continue;
            if (strlen($mask) == 0) $mask = 32;
            $trusted_hosts .= trim($address).'/'.trim($mask)."\n";
            unset($address, $mask);
        }
    }

    include('./trustedhosts.html');
