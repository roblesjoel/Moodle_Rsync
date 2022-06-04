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
class local_rsync_file_push extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function push_file_to_private_files_paramenters(){
        return new external_function_parameters(
            array('filename' => new external_value(PARAM_TEXT, 'A file in a user\'s \'private files\', ' .
                  'default in / when no filepath provided', VALUE_REQUIRED),
                  'filepath' => new external_value(PARAM_TEXT, 'A path to a file in a user\'s \'private files\'', VALUE_DEFAULT,
                      '/'),
                  'courseid' => new external_value(PARAM_INT, 'The course id the file is to be handeled in', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the file is to be added', VALUE_REQUIRED),
                  'displayname' => new external_value(PARAM_TEXT, 'The name to display for the file', VALUE_DEFAULT, '')
            )
        );
    }


    /**
     * Uploads file from local machine to private files
     */
    public static function push_file_to_private_files($filepath, $displayname){
        /*global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        /*$params = self::validate_parameters(self::create_file_resource_parameters(),
            array('filename' => $filename,
                'filepath' => $filepath,
                'courseid' => $courseid,
                'sectionnumber' => $sectionnumber,
                'displayname' => $displayname));

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
        $filearea = "private";
        if ($filepath == '' OR !isset($filepath)) {
            $filepath = "/";
        }
        if ($displayname == '') {
            $futurefilename = $filename;
        } else {
            $futurefilename = $displayname;
        }

        $fs = get_file_storage();*/

        // Prepare file record object
        $fileinfo = array(
            'contextid' => $context->id,       // ID of context
            'component' => 'mod_mymodule',     // usually = table name
            'filearea' => 'myarea',            // usually = table name
            'itemid' => 0,                     // usually = ID of row in table
            'filepath' => '/',                 // any path beginning and ending in
            'filename' => 'myfile.txt');       // any filename

        // Create file containing text 'hello world'
        $fs->create_file_from_string($fileinfo, 'hello world');
    }
}