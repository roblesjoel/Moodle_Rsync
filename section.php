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
 * External Web Service Template
 *
 * @package     local_rsync
 * @copyright   2022, Joel Robles <joelgabriel.roblesgasser@students.bfh.ch>
 *              Vithursan Thayananthan <vithursan.thayananthan@students.bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

/**
 * Class local_rsync_section
 */
class local_rsync_section extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_section_visibilty_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'visibility' => new external_value(PARAM_INT, 'Visibility to set the section', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function remove_file_from_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'filename' => new external_value(PARAM_TEXT, 'The name of the file that should be deleted', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function rename_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'sectionname' => new external_value(PARAM_TEXT, 'The new name of the section', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function remove_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_file_visibility_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'filename' => new external_value(PARAM_TEXT, 'The name of the file', VALUE_REQUIRED),
                  'visibility' => new external_value(PARAM_INT, 'The state of visibility to set the file in', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function remove_all_files_from_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function move_file_to_other_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'targetsectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted',
                      VALUE_REQUIRED),
                  'modulename' => new external_value(PARAM_TEXT, 'In which section the files should be deleted', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function move_all_modules_to_other_section_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the modules should be deleted',
                      VALUE_REQUIRED),
                  'targetsectionnumber' => new external_value(PARAM_INT, 'In which section the modules should be moved to',
                      VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function remove_all_sections_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function copy_module_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the modules should be deleted',
                            VALUE_REQUIRED),
                  'targetsectionnumber' => new external_value(PARAM_INT, 'In which section the modules should be copied to',
                      VALUE_REQUIRED),
                  'modulename' => new external_value(PARAM_TEXT, 'Name of the module to be copied',
                      VALUE_REQUIRED),
            )
        );
    }

    /**
     * Lets the user set the visibilty of a section
     *
     * @param int $courseid course id
     * @param int $sectionnumber section number
     * @param int $visibility visibility of the section. 0 = hidden, 1 = shown
     * @return string A string describing the result.
     */
    public static function set_section_visibilty($courseid, $sectionnumber, $visibility) {
        global $USER;

        $params = self::validate_parameters(self::set_section_visibilty_parameters(),
            array('courseid' => $courseid,
                'sectionnumber' => $sectionnumber,
                'visibility' => $visibility));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        set_section_visible($courseid, $sectionnumber, $visibility);

        $visibilitytext = '';

        if ($visibility == 0) {
            $visibilitytext = 'hidden';
        } else {
            $visibilitytext = 'unhidden';
        }

        return get_string('successmessage_section_visibility', 'local_rsync',
            array('visibility' => $visibilitytext, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid,
                'username' => fullname($USER)));
    }

    /**
     * Lets the user remove a file from a section
     *
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $filename the name of the file to be removed
     * @return string A string describing the result
     * @
     */
    public static function remove_file_from_section($courseid, $sectionnumber, $filename) {
        global $USER;

        $params = self::validate_parameters(self::remove_file_from_section_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber,
            'filename' => $filename));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $modules = get_array_of_activities($courseid);

        foreach ($modules as $module) {
            if ($module->section == $sectionnumber && $module->name == $filename) {
                course_delete_module($module->cm);
            }
        }

        return get_string('successmessage_section_remove_file', 'local_rsync', array('filename' => $filename,
            'sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user renane a section
     *
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @return string A string describing the result
     */
    public static function rename_section($courseid, $sectionnumber, $sectionname) {
        global $USER, $DB;

        $params = self::validate_parameters(self::rename_section_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber,
            'sectionname' => $sectionname));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        if ($section = $DB->get_record("course_sections", array("course" => $courseid, "section" => $sectionnumber))) {
            course_update_section($courseid, $section, array('name' => $sectionname));
        }

        return get_string('successmessage_section_rename', 'local_rsync', array('sectionnumber' => $sectionnumber,
            'newsectionname' => $sectionname, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user remove a section
     *
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @return string A string describing the result
     */
    public static function remove_section($courseid, $sectionnumber) {
        global $USER, $DB;

        $params = self::validate_parameters(self::remove_section_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $removed = false;

        if ($section = $DB->get_record("course_sections", array("course" => $courseid, "section" => $sectionnumber))) {
            $removed = course_delete_section($courseid, $section);
        }

        if ($removed) {
            return get_string('successmessage_section_remove', 'local_rsync', array('sectionnumber' => $sectionnumber,
                'courseid' => $courseid, 'username' => fullname($USER)));
        } else {
            return get_string('errormessage_section_rename', 'local_rsync', array('sectionnumber' => $sectionnumber,
                'courseid' => $courseid, 'username' => fullname($USER)));
        }
    }

    /**
     * Lets the user remove all sections from course
     *
     * @param int $course id
     * @return string A string describing the result
     */
    public static function remove_all_sections($courseid) {
        global $USER;

        $params = self::validate_parameters(self::remove_section_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $coursesections = get_fast_modinfo($courseid)->get_section_info_all();

        $lenght = count($coursesections);

        for ($i = $lenght - 1; $i >= 1; $i--) {
            $result = course_delete_section($courseid, $i);
            if (!$result) {
                throw new moodle_exception('removalfailed');
            }
        }
        return get_string('successmessage_remove_all_sections', 'local_rsync', array('courseid' => $courseid,
            'username' => fullname($USER)));
    }

    /**
     * Lets the user set the visibility of a file in a section
     *
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @param int $visiblity the visiblity of the file
     * @return string A string describing the result
     */
    public static function set_file_visibility($courseid, $sectionnumber, $filename, $visibility) {
        global $USER;

        $params = self::validate_parameters(self::set_file_visibility_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber,
            'filename' => $filename,
            'visibility' => $visibility));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $modules = get_array_of_activities($courseid);

        $foundmodule = false;

        foreach ($modules as $module) {
            if ($module->section == $sectionnumber && $module->name == $filename) {
                $foundmodule = set_coursemodule_visible($module->cm, $visibility, $visibility);
            }
        }

        $visibilitytext = '';

        if ($visibility == 0) {
            $visibilitytext = 'hidden';
        } else {
            $visibilitytext = 'unhidden';
        }

        if ($foundmodule) {
            return get_string('successmessage_section_file_visibility', 'local_rsync',
                array('visibility' => $visibilitytext, 'filename' => $filename, 'sectionnumber' => $sectionnumber,
                    'courseid' => $courseid, 'username' => fullname($USER)));
        } else {
            return get_string('errormessage_section_file_visibility', 'local_rsync',
                array('filename' => $filename, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid,
                    'username' => fullname($USER)));
        }
    }

    /**
     * Lets the user remove all files from a section
     *
     * @param int $courseid course id
     * @param int $sectionnumber section number
     * @return string A string describing the result
     */
    public static function remove_all_files_from_section($courseid, $sectionnumber) {
        global $USER;

        $params = self::validate_parameters(self::remove_all_files_from_section_parameters(),
        array('courseid' => $courseid,
            'sectionnumber' => $sectionnumber));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $modules = get_array_of_activities($courseid);

        foreach ($modules as $module) {
            if ($module->section == $sectionnumber) {
                course_delete_module($module->cm);
            }
        }

        return get_string('successmessage_section_file_removal', 'local_rsync',
            array('sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user move a module from a section to an other
     *
     * @param int $courseid course id
     * @param int $sectionnumber section number
     * @param int $targetsectionnumber target section number
     * @param string $modulename name of the module to be moved
     * @return string A string describing the result
     */
    public static function move_file_to_other_section($courseid, $sectionnumber, $targetsectionnumber, $modulename) {
        global $USER;

        $params = self::validate_parameters(self::move_file_to_other_section_parameters(),
        array('courseid' => $courseid,
        'sectionnumber' => $sectionnumber,
        'targetsectionnumber' => $targetsectionnumber,
        'modulename' => $modulename));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $modules = get_array_of_activities($courseid);

        foreach ($modules as $module) {
            if ($module->section == $sectionnumber && $module->name == $modulename) {
                $coursemodinfo = get_fast_modinfo($courseid, 0, false);
                $coursesections = $coursemodinfo->get_sections();
                $section = $coursesections[$targetsectionnumber];
                $firstmodid = $section[0];
                $mod = $coursemodinfo->get_cm($module->cm);
                $sectioninfo = $coursemodinfo->get_section_info($targetsectionnumber);
                moveto_module($mod, $sectioninfo);

                return get_string('successmessage_section_module_movement', 'local_rsync',
                    array('modulename' => $modulename, 'sectionid' => $sectionnumber, 'targetsectionid' => $targetsectionnumber,
                        'courseid' => $courseid, 'username' => fullname($USER)));
            }
        }

        return 'noup';
    }

    /**
     * Lets the user move all modules from a section to an other
     *
     * @param int $courseid course id
     * @param int $sectionumber section number
     * @param int $targetsectionnumber target section number
     * @return string A string describing the result
     */
    public static function move_all_modules_to_other_section($courseid, $sectionnumber, $targetsectionnumber) {
        global $USER;

        $params = self::validate_parameters(self::move_all_modules_to_other_section_parameters(),
        array('courseid' => $courseid,
        'sectionnumber' => $sectionnumber,
        'targetsectionnumber' => $targetsectionnumber));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $modules = get_array_of_activities($courseid);

        foreach ($modules as $module) {
            if ($module->section == $sectionnumber) {
                $coursemodinfo = get_fast_modinfo($courseid, 0, false);
                $mod = $coursemodinfo->get_cm($module->cm);
                $sectioninfo = $coursemodinfo->get_section_info($targetsectionnumber);
                moveto_module($mod, $sectioninfo);
            }
        }

        return get_string('successmessage_section_all_module_movement', 'local_rsync',
            array('sectionid' => $sectionnumber, 'targetsectionid' => $targetsectionnumber, 'courseid' => $courseid,
                'username' => fullname($USER)));
    }

    /**
     * Lets the user copy a module and insert it into a section
     * 
     * @param int $courseid course id
     * @param int $sectionnumber section number
     * @param int $targetsectionnumber target section number
     * @param string $modulename name of the module to be copied
     * @return string A string describing the result
     * @throws moodle_exception if the course, the section, the target section or the module doesnt exist.
     * @throws moodle_exception if the user isn't allowed to perfom the action
     */
    public static function copy_module($courseid, $sectionnumber, $targetsectionnumber, $modulename) {
        global $USER, $DB;

        // Check parameters
        $params = self::validate_parameters(self::copy_module_parameters(),
        array('courseid' => $courseid,'sectionnumber' => $sectionnumber,'targetsectionnumber' => $targetsectionnumber,'modulename' => $modulename));

        // Context validation.
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        // OPTIONAL but in most web service it should present.
        if (!has_capability('repository/user:view', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        if (!has_capability('moodle/user:manageownfiles', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        $coursecontext = \context_course::instance($courseid);
        if (!has_capability('moodle/course:manageactivities', $coursecontext)) {
            throw new moodle_exception('cannotaddcoursemodule');
        }

        $course = $DB->get_record('course', array('id' => $courseid));
        if($course == null){
            throw new moodle_exception('coursenotfound');
        }

        // Get all modules of course.
        $modules = get_array_of_activities($courseid);

        // Loop through them.
        foreach ($modules as $module) {
            // If module section and name is the same as given.
            if ($module->name == $modulename && $module->section == $sectionnumber) {
                // Create a backup.
                $bc = new backup_controller(backup::TYPE_1ACTIVITY, $module->cm, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
                $backupid       = $bc->get_backupid();;
                $bc->execute_plan();
                $bc->destroy();

                // And restore it.
                $rc = new restore_controller($backupid, $courseid, backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
                $cmcontext = context_module::instance($module->cm);
                $rc->execute_precheck();
                $rc->execute_plan();

                // Get the new module id.
                $newcmid = null;
                $tasks = $rc->get_plan()->get_tasks();
                foreach ($tasks as $task) {
                    if (is_subclass_of($task, 'restore_activity_task')) {
                        if ($task->get_old_contextid() == $cmcontext->id) {
                            $newcmid = $task->get_moduleid();
                            break;
                        }
                    }
                }
                $rc->destroy();
                
                // If no module is found, throw exception.
                if(!$newcmid) {
                    throw new moodle_exception('newmodulenotfound');
                }

                // Get module info of course.
                $coursemodinfo = get_fast_modinfo($courseid, 0, false);
                // Get the new module.
                $mod = $coursemodinfo->get_cm($newcmid);
                // And get info of the target section.
                $sectioninfo = $coursemodinfo->get_section_info($targetsectionnumber);

                // If that section doesn't exist, throw error.
                if(!$sectioninfo) {
                    throw new moodle_exception('targetsectionnotfound');
                }

                // Else move new module to target section and leave the loop.
                moveto_module($mod, $sectioninfo);
                break;
            }
        }

        // Return the success message.
        return get_string('successmessage_copy_module', 'local_rsync',
            array('modulename' => $modulename, 'section' => $sectionnumber, 'targetsection' => $targetsectionnumber, 'courseid' => $courseid,
                'username' => fullname($USER)));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function set_section_visibilty_returns() {
        return new external_value(PARAM_TEXT, 'Section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function remove_file_from_section_returns() {
        return new external_value(PARAM_TEXT, 'File name, section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function rename_section_returns() {
        return new external_value(PARAM_TEXT, 'Section name, section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function remove_section_returns() {
        return new external_value(PARAM_TEXT, 'Section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function set_file_visibility_returns() {
        return new external_value(PARAM_TEXT, 'Section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function remove_all_files_from_section_returns() {
        return new external_value(PARAM_TEXT, 'Section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function move_file_to_other_section_returns() {
        return new external_value(PARAM_TEXT, 'Module name, section number, target section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function move_all_modules_to_other_section_returns() {
        return new external_value(PARAM_TEXT, 'Section number, target section number, course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function remove_all_sections_returns() {
        return new external_value(PARAM_TEXT, 'Course id and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function copy_module_returns() {
        return new external_value(PARAM_TEXT, 'Course id and username');
    }
}
