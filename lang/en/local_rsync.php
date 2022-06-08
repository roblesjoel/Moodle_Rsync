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
$string['successmessage'] = 'Added file {$a->folder}{$a->file} by user {$a->username} to course id {$a->courseid} in section {$a->coursesection} now having name {$a->newname} and resource id {$a->resourceid}.';
$string['successmessage_file_upload'] = 'Added file {$a->file} by user {$a->username} to course id {$a->courseid} in section {a->coursesection} with the name {$a->newname}.';
$string['successmessage_section_visibility'] = 'Successfully {$a->visibility} section {$a->sectionnumber} in course with the id {$a->courseid} by user {$a->username}.';
$string['successmessage_section_remove_file'] = 'Successfully removed file {$a->filename} in section {$a->sectionnumber} in the course with the id {$a->courseid} by user {$a->username}.';