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

require_once($CFG->dirroot. '/course/lib.php'); // contains section visibility, section info, moveto_module, course_change_visibility

/**
 * Class local_rsync_section
 */
class local_rsync_section extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_section_visibilty_parameters(){
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
    public static function remove_file_from_section_parameters(){
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
    public static function rename_section_parameters(){
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
    public static function remove_section_parameters(){
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
    public static function set_file_visibility_parameters(){
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
    public static function remove_all_files_from_section_parameters(){
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
    public static function move_file_to_other_section_parameters(){
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course id', VALUE_REQUIRED),
                  'sectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'targetsectionnumber' => new external_value(PARAM_INT, 'In which section the files should be deleted', VALUE_REQUIRED),
                  'modulename' => new external_value(PARAM_TEXT, 'In which section the files should be deleted', VALUE_REQUIRED),
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
    public static function set_section_visibilty($courseid, $sectionnumber, $visibility){
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

        $visibility_long = '';

        if ($visibility == 0){
            $visibility_long = 'hidden';
        }
        else{
            $visibility_long = 'unhidden';
        }

        return get_string('successmessage_section_visibility', 'local_rsync', array('visibility' => $visibility_long, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user remove a file from a section
     * 
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $filename the name of the file to be removed
     * @return string A string describing the result
     */
    public static function remove_file_from_section($courseid, $sectionnumber, $filename){
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

        foreach($modules as $module){
            if($module->section == $sectionnumber && $module->name == $filename){
                course_delete_module($module->cm);
            }
        }

        return get_string('successmessage_section_remove_file', 'local_rsync', array('filename' => $filename, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user renane a section
     * 
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @return string A string describing the result
     */
    public static function rename_section($courseid, $sectionnumber, $sectionname){
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

        //course_update_section($courseid, $sectionnumber, array('name' => $sectionname));

        if ($section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionnumber))) {
            course_update_section($courseid, $section, array('name' => $sectionname));
    
            // Determine which modules are visible for AJAX update
            /*$modules = !empty($section->sequence) ? explode(',', $section->sequence) : array();
            if (!empty($modules)) {
                list($insql, $params) = $DB->get_in_or_equal($modules);
                $select = 'id ' . $insql . ' AND visible = ?';
                array_push($params, $visibility);
                if (!$visibility) {
                    $select .= ' AND visibleold = 1';
                }
                $resourcestotoggle = $DB->get_fieldset_select('course_modules', 'id', $select, $params);
            }*/
        }

        return get_string('successmessage_section_rename', 'local_rsync', array('sectionnumber' => $sectionnumber, 'newsectionname' => $sectionname, 'courseid' => $courseid, 'username' => fullname($USER)));
    }

    /**
     * Lets the user remove a section
     * 
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @return string A string describing the result
     */
    public static function remove_section($courseid, $sectionnumber){
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

        if ($section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionnumber))) {
            $removed = course_delete_section($courseid, $section);
        }

        if($removed){
            return get_string('successmessage_section_remove', 'local_rsync', array('sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
        }
        else{
            return get_string('errormessage_section_rename', 'local_rsync', array('sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
        }
    }

    /**
     * Lets the user set the visiblity of a file in a section
     * 
     * @param int $courseud course id
     * @param int $sectionnumber section number
     * @param string $sectionname the new name of the section
     * @param int $visiblity the visiblity of the file
     * @return string A string describing the result
     */
    public static function set_file_visibility($courseid, $sectionnumber, $filename, $visibility){
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

        foreach($modules as $module){
            if($module->section == $sectionnumber && $module->name == $filename){
                $foundmodule = set_coursemodule_visible($module->cm, $visibility, $visibility);
            }
        }

        $visibility_long = '';

        if ($visibility == 0){
            $visibility_long = 'hidden';
        }
        else{
            $visibility_long = 'unhidden';
        }

        if($foundmodule){
            return get_string('successmessage_section_file_visibility', 'local_rsync', array('visibility' => $visibility_long,'filename' => $filename, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
        }
        else{
            return get_string('errormessage_section_file_visibility', 'local_rsync', array('filename' => $filename, 'sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
        }        
    }

    /**
     * Lets the user remove all files from a section
     * 
     * @param int $courseid course id
     * @param int $sectionnumber section number
     * @return string A string describing the result
     */
    public static function remove_all_files_from_section($courseid, $sectionnumber){
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

        foreach($modules as $module){
            if($module->section == $sectionnumber){
                course_delete_module($module->cm);
            }
        }

        return get_string('successmessage_section_file_removal', 'local_rsync', array('sectionnumber' => $sectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
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
    public static function move_file_to_other_section($courseid, $sectionnumber, $targetsectionnumber, $modulename){
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

        foreach($modules as $module){
            if($module->section == $sectionnumber && $module->name == $modulename){
                $coursemodinfo = get_fast_modinfo($courseid, 0, false);
                $coursesections = $coursemodinfo->get_sections();
                $section = $coursesections[$targetsectionnumber];
                $firstmodid = $section[0];
                $mod = $coursemodinfo->get_cm($module->cm);
                $sectioninfo = $coursemodinfo->get_section_info($targetsectionnumber);
                moveto_module($mod, $sectioninfo);

                return get_string('successmessage_section_module_movement', 'local_rsync', array('modulename' => $modulename, 'sectionid' => $sectionnumber, 'targetsectionid' => $targetsectionnumber, 'courseid' => $courseid, 'username' => fullname($USER)));
            }
        }

        return 'noup';
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
}