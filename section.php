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

/**
 * Class local_rsync_file_push
 */
class local_rsync_section extends external_api {

    /**
     * 
     */
    public static function remove_all_files_from_section_parameters(){
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED)
            )
        );
    }

    /**
     * 
     */
    public static function remove_all_files_from_section($courseid, $sectionnumber){
        global $USER, $DB;

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

        $component = "user";
        $filearea = "course";

        $fs = get_file_storage();
        $coursefiles = $fs->get_area_files($context->id, $component, $filearea, 0, 'id', true);
        self::write_to_console($coursefiles);
    }

    public static function write_to_console($data) {
        $console = $data;
        if (is_array($console))
        $console = implode(',', $console);
       
        echo "<script>console.log('Console: " . $console . "' );</script>";
       }
}