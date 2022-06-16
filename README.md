<h1 align="center">Moodle_Rsync</h1>

Thsis Plugin was developed as a part of the course Project 1 at [BFH](https://www.bfh.ch/de/).
It was made with the intention to help teachers with their file and course management.

If you want to test out this plugin, just download the newest [Release](https://github.com/RockstaYT/Moodle_Rsync/releases) and install it.
If you dont know how to install plugins in Moodle, please refer to this [help page](https://docs.moodle.org/400/en/Installing_plugins).

## How to use it

There are many different ways to talk to the endpoints.
In this project we used [cURL](https://curl.se/) for testing. It is still a possible option, if that's you thing.
Documentation for how to use [cURL](https://curl.se/) with this plugin is [here](#curl).
If it's not your thing there is also a python script, which can found in the [clients folder](clients).
This makes use of the requests library. How to use it without options is documented [here](#python-without-options) and with options [here](#python-with-options).

## Endpoints

These are the endpoints that were introduced in this plugin.

| Function name                                 | Function usage                                                                                                                             | Parameters                                               |
| --------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ | -------------------------------------------------------- |
| local_rsync_create_file_resource              | Creates a file in a course. The file needs to be in the private files of the user.                                                         | filename, courseid, sectionnumber, displayname           |
| local_rsync_set_section_visibility            | Sets the visibility of a section.                                                                                                          | courseid, sectionnumber, visibility (0 or 1)             |
| local_rsync_remove_file_from_section          | Removes a module from a section.                                                                                                           | courseid, sectionnumber, filename                        |
| local_rsync_rename_section                    | Renames a section.                                                                                                                         | courseid, sectionumber, sectionname                      |
| local_rsync_remove_section                    | Removes a section.                                                                                                                         | courseid, sectionnumber                                  |
| local_rsync_set_file_visibility               | Sets the visibility of a module.                                                                                                           | courseid, sectionumber, filename, visibility (0 or 1)    |
| local_rsync_remove_all_files_from_section     | Removes all modules from a section.                                                                                                        | courseid, sectionnumber                                  |
| local_rsync_move_file_to_other_section        | Moves a module from a section to an other.                                                                                                 | courseid, sectionnumber, targetsectionnumber, modulename |
| local_rsync_move_all_modules_to_other_section | Moves all modules from a section to an other.                                                                                              | courseid, sectionnumber, targetsectionnumber             |
| local_rsync_remove_all_sections               | Removes all section of a course.                                                                                                           | courseid                                                 |
| local_rsync_copy_module                       | Copies a module and puts it in the specified section.                                                                                      | courseid, sectionnumber, targetsectionumber, modulename  |
| local_rsync_change_course_visibility          | Changes the visibility of a course.                                                                                                        | courseid, visibility (0 or 1)                            |
| local_rsync_copy_course                       | Copies a module into an other module. The data in the target module is overwritten.                                                        | courseid, newcourseid                                    |
| local_rsync_copy_all_section_modules          | Copies all modules in a section and puts it in the specified section. Still in dev.                                                        |                                                          |
| local_rsync_create_section                    | Creates a new section. Is used when a user wants to upload a whole folder, so the foldername is the name of the new section. Still in dev. |                                                          |

## cURL

## Python without options

## Python with options

## Acknowledgements

Thanks a lot to @lucaboesch for helping us in this project.
