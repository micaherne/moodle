<?php

require_once($CFG->libdir . '/composer/semver/src/Constraint/ConstraintInterface.php');
require_once($CFG->libdir . '/composer/semver/src/Constraint/Constraint.php');
require_once($CFG->libdir . '/composer/semver/src/Constraint/EmptyConstraint.php');
require_once($CFG->libdir . '/composer/semver/src/Constraint/MultiConstraint.php');
require_once($CFG->libdir . '/composer/semver/src/VersionParser.php');
require_once($CFG->libdir . '/composer/semver/src/Comparator.php');
require_once($CFG->libdir . '/composer/semver/src/Semver.php');


use Composer\Semver\Semver;

class core_versioning extends Semver {

    public static function satisfies($version, $constraints) {

        if (is_integer($constraints)) {
            $constraints = '>=' . $constraints;
        }

        if ($constraints == ANY_VERSION) {
            $constraints = '*';
        }

        return parent::satisfies($version, $constraints);
    }

}