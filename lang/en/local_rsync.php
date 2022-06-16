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
 * Plugin strings are defined here.
 *
 * @package     local_rsync
 * @category    string
 * @copyright   2022, Joel Robles <joelgabriel.roblesgasser@students.bfh.ch> Vithursan Thayananthan <vithursan.thayananthan@students.bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'rsync web service';
$string['local/rsync:managefiles'] = 'Manage courses using the rsync web service';
$string['successmessage_file_upload'] = 'Added file {$a->file} to course id {$a->courseid} in section {$a->coursesection} by user {$a->username}.';

$string['successmessage_section_visibility'] = 'Successfully {$a->visibility} section {$a->sectionnumber} in course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_remove_file'] = 'Successfully removed file {$a->filename} in section {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_rename'] = 'Successfully renamed section with the id {$a->sectionnumber} to {$a->newsectionname} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_remove'] = 'Successfully removed section with the id {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_file_visibility'] = 'Successfully {$a->visibility} file with the name {$a->filename} in section with the id {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_file_removal'] = 'Successfully removed all files from the section with the id {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_module_movement'] = 'Successfully moved module with the name {$a->modulename} from section with the id {$a->sectionid} to the section with the id {$a->targetsectionid} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_all_module_movement'] = 'Successfully moved all modules from section with the id {$a->sectionid} to the section with the id {$a->targetsectionid} in the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_remove_all_sections'] = 'Successfully removed all sections from the course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_copy_module'] = 'Successfully copied module {$a->modulename} from section {$a->section} to section {$a->targetsection} in the course with the id {$a->courseid}by the user {$a->username}';
$string['successmessage_create_section'] = 'Successfully created section named {$a->sectionname} with the nummer {$a->sectionnumner} in the course with the id {$a->courseid} by the user {$a->username}';

$string['errormessage_section_rename'] = 'An error occured while removing section with the id {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';
$string['errormessage_section_file_visibility'] = 'An error occured while changing visiblity of file with the name {$a->filename} in section with the id {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';

$string['successmessage_course_visibility'] = 'Successfully {$a->visibility} course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_course_copy'] = 'Successfully copied the course with the id {$a->courseid} to the course with the id {$a->newcourseid} by user {$a->username}.';
