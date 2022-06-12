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
 * @copyright   2022, Joel Robles <joelgabriel.roblesgasser@students.bfh.ch> Vithursan Thayananthan <vithursan.thayananthan@students.bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->dirroot. '/course/modlib.php');
require_once($CFG->dirroot . '/files/externallib.php');
require_once($CFG->dirroot . '/mod/resource/lib.php');
require_once($CFG->dirroot . '/mod/resource/locallib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

/**
 * Class local_rsync_course
 */
class local_rsync_course extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function change_course_visibility_parameters(){
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'visibility' => new external_value(PARAM_INT, 'Visibility to set the course', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function copy_course_parameters(){
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'newcourseid' => new external_value(PARAM_INT, 'The id of the new course', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Lets the user set visibility of a course
     * 
     * @param int $courseid course id
     * @param int $visibility the visibility of the course
     * @return string A string describing the result
     */
    public static function change_course_visibility($courseid, $visibility){
        global $USER;

        $params = self::validate_parameters(self::change_course_visibility_parameters(),
        array('courseid' => $courseid,
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

        $visibilityboolean = ($visibility) ? true : false;

        $visibility_long = '';

        if ($visibility == 0){
            $visibility_long = 'hidden';
        }
        else{
            $visibility_long = 'unhidden';
        }

        course_change_visibility($courseid, $visibilityboolean);

        return get_string('successmessage_course_visibility', 'local_rsync', array('visibility' => $visibility_long, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user copy all the contents of one course into an other.
     * All the data in the new course will be deleted before the copy
     * 
     * @param int $courseid course id
     * @param int $newcourseid id of the new course
     */
    public static function copy_course($courseid, $newcourseid){
        global $USER, $DB, $CFG;

        $params = self::validate_parameters(self::copy_course_parameters(),
        array('courseid' => $courseid,
            'newcourseid' => $newcourseid));

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

        //backup
        $course = $DB->get_record('course', array('id' => $courseid));
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE, backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);

        $format = $bc->get_format();
        $type = $bc->get_type();
        $id = $bc->get_id();
        $users = $bc->get_plan()->get_setting('users')->get_value();
        $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
        $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised);
        $bc->get_plan()->get_setting('filename')->set_value($filename);

        // Execution.
        $bc->finish_ui();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];

        $filepath = $CFG->dataroot.'/temp/'.$filename;

        if ($file->copy_content_to($filepath)) {
            $file->delete();
        } else {
            throw new moodle_exception('directoryerror');
        }

        $bc->destroy();

        //restore
        $backupdir = "restore_" . uniqid();
        $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;

        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($filepath, $path);

        try {
            $rc = new restore_controller($backupdir, $newcourseid, backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id, backup::TARGET_EXISTING_DELETING);
            $rc->execute_precheck();
            $rc->execute_plan();
            $rc->destroy();

        } catch (Exception $e) {
            fulldelete($path);
            print_error('generalexceptionmessage', 'error', '', $e->getMessage());
            throw new moodle_exception($e->getMessage());
        }

        return get_string('successmessage_course_copy', 'local_rsync', array('courseid' => $courseid, 'newcourseid' => $newcourseid, 'username' => fullname($USER)));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function change_course_visibility_returns() {
        return new external_value(PARAM_TEXT, 'Course id, visibility and username');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function copy_course_returns() {
        return new external_value(PARAM_TEXT, 'Course id, new course id and username');
    }
}