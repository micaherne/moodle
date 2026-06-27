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
 * @package moodlecore
 * @subpackage backup-includes
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the backup needed stuff
require_once(__DIR__ . '/../interfaces/checksumable.class.php');
require_once(__DIR__ . '/../interfaces/loggable.class.php');
require_once(__DIR__ . '/../interfaces/executable.class.php');
require_once(__DIR__ . '/../interfaces/processable.class.php');
require_once(__DIR__ . '/../../backup.class.php');
require_once(__DIR__ . '/../structure/restore_path_element.class.php');
require_once(__DIR__ . '/../helper/async_helper.class.php');
require_once(__DIR__ . '/../helper/backup_anonymizer_helper.class.php');
require_once(__DIR__ . '/../helper/backup_file_manager.class.php');
require_once(__DIR__ . '/../helper/copy_helper.class.php');
require_once(__DIR__ . '/../helper/restore_prechecks_helper.class.php');
require_once(__DIR__ . '/../helper/restore_questionbanks_parser_processor.php');
require_once(__DIR__ . '/../helper/restore_moodlexml_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_inforef_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_users_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_roles_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_questions_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_structure_parser_processor.class.php');
require_once(__DIR__ . '/../helper/restore_decode_rule.class.php');
require_once(__DIR__ . '/../helper/restore_decode_content.class.php');
require_once(__DIR__ . '/../helper/restore_decode_processor.class.php');
require_once(__DIR__ . '/../helper/restore_logs_processor.class.php');
require_once(__DIR__ . '/../helper/restore_log_rule.class.php');
require_once(__DIR__ . '/../xml/parser/progressive_parser.class.php');
require_once(__DIR__ . '/../output/output_controller.class.php');
require_once(__DIR__ . '/../dbops/backup_dbops.class.php');
require_once(__DIR__ . '/../dbops/restore_dbops.class.php');
require_once(__DIR__ . '/../dbops/backup_controller_dbops.class.php');
require_once(__DIR__ . '/../dbops/restore_controller_dbops.class.php');
require_once(__DIR__ . '/../checks/restore_check.class.php');
require_once(__DIR__ . '/../loggers/base_logger.class.php');
require_once(__DIR__ . '/../loggers/error_log_logger.class.php');
require_once(__DIR__ . '/../loggers/file_logger.class.php');
require_once(__DIR__ . '/../loggers/core_backup_html_logger.class.php');
require_once(__DIR__ . '/../loggers/database_logger.class.php');
require_once(__DIR__ . '/../loggers/output_indented_logger.class.php');
require_once(__DIR__ . '/../factories/backup_factory.class.php');
require_once(__DIR__ . '/../factories/restore_factory.class.php');
require_once(__DIR__ . '/../helper/backup_helper.class.php');
require_once(__DIR__ . '/../helper/backup_general_helper.class.php');
require_once(__DIR__ . '/../settings/setting_dependency.class.php');
require_once(__DIR__ . '/../settings/base_setting.class.php');
require_once(__DIR__ . '/../settings/backup_setting.class.php');
require_once(__DIR__ . '/../settings/root/root_backup_setting.class.php');
require_once(__DIR__ . '/../settings/activity/activity_backup_setting.class.php');
require_once(__DIR__ . '/../settings/section/section_backup_setting.class.php');
require_once(__DIR__ . '/../settings/course/course_backup_setting.class.php');
require_once(__DIR__ . '/../plan/base_plan.class.php');
require_once(__DIR__ . '/../plan/restore_plan.class.php');
require_once(__DIR__ . '/../plan/base_task.class.php');
require_once(__DIR__ . '/../plan/restore_task.class.php');
require_once(__DIR__ . '/../plan/base_step.class.php');
require_once(__DIR__ . '/../plan/restore_step.class.php');
require_once(__DIR__ . '/../plan/restore_structure_step.class.php');
require_once(__DIR__ . '/../plan/restore_execution_step.class.php');
require_once(__DIR__ . '/../../moodle2/restore_plan_builder.class.php');
require_once(__DIR__ . '/../../controller/base_controller.class.php');
require_once(__DIR__ . '/../../controller/restore_controller.class.php');
require_once(\core\component::component_path('core_backup', 'base_moodleform.class.php'));
require_once(\core\component::component_path('core_backup', 'base_ui.class.php'));
require_once(\core\component::component_path('core_backup', 'base_ui_stage.class.php'));
require_once(\core\component::component_path('core_backup', 'backup_ui_setting.class.php'));
require_once(\core\component::component_path('core_backup', 'restore_ui_stage.class.php'));
require_once(\core\component::component_path('core_backup', 'restore_ui.class.php'));
require_once(\core\component::component_path('core_backup', 'restore_moodleform.class.php'));
require_once(\core\component::component_path('core_backup', 'restore_ui_components.php'));

// And some moodle stuff too
require_once(\core\component::component_path('core_tag', 'lib.php'));
require_once(\core\component::component_path('core', 'gradelib.php'));
require_once(\core\component::component_path('core', '/questionlib.php'));
require_once(\core\component::component_path('core_course', 'lib.php'));
require_once (\core\component::component_path('core_block', 'moodleblock.class.php'));
