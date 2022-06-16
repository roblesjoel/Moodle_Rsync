<h1 align="center">Moodle_Rsync</h1>

Thsis Plugin was developed as a part of the course Project 1 at [BFH](https://www.bfh.ch/de/).
It was made with the intention to help teachers with their file and course management.

If you want to test out this plugin, just download the newest [Release](https://github.com/RockstaYT/Moodle_Rsync/releases) and install it.
If you dont know how to install plugins in Moodle, please refer to this [help page](https://docs.moodle.org/400/en/Installing_plugins).

## How to use it

There are many different ways to talk to the endpoints.
In this project we used [cURL](https://curl.se/) for testing. It is still a possible option, if that's you thing. </br>
Documentation for how to use [cURL](https://curl.se/) with this plugin is [here](#curl). </br>
If it's not your thing there is also a python script, which can found in the [clients folder](clients). </br>
This makes use of the requests library. How to use it without options is documented [here](#python-without-options) and with options [here](#python-with-options). </br>
If you want, you can make your own scripts, so that it mimics your workflow.

## Endpoints

These are the endpoints that were introduced in this plugin.

| Function name                                 | Function usage                                                                                                               | Parameters                                               |
| --------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------- |
| local_rsync_create_file_resource              | Creates a file in a course. The file needs to be in the private files of the user.                                           | filename, courseid, sectionnumber, displayname           |
| local_rsync_set_section_visibility            | Sets the visibility of a section.                                                                                            | courseid, sectionnumber, visibility (0 or 1)             |
| local_rsync_remove_file_from_section          | Removes a module from a section.                                                                                             | courseid, sectionnumber, filename                        |
| local_rsync_rename_section                    | Renames a section.                                                                                                           | courseid, sectionumber, sectionname                      |
| local_rsync_remove_section                    | Removes a section.                                                                                                           | courseid, sectionnumber                                  |
| local_rsync_set_file_visibility               | Sets the visibility of a module.                                                                                             | courseid, sectionumber, filename, visibility (0 or 1)    |
| local_rsync_remove_all_files_from_section     | Removes all modules from a section.                                                                                          | courseid, sectionnumber                                  |
| local_rsync_move_file_to_other_section        | Moves a module from a section to an other.                                                                                   | courseid, sectionnumber, targetsectionnumber, modulename |
| local_rsync_move_all_modules_to_other_section | Moves all modules from a section to an other.                                                                                | courseid, sectionnumber, targetsectionnumber             |
| local_rsync_remove_all_sections               | Removes all section of a course.                                                                                             | courseid                                                 |
| local_rsync_copy_module                       | Copies a module and puts it in the specified section.                                                                        | courseid, sectionnumber, targetsectionumber, modulename  |
| local_rsync_change_course_visibility          | Changes the visibility of a course.                                                                                          | courseid, visibility (0 or 1)                            |
| local_rsync_copy_course                       | Copies a module into an other module. The data in the target module is overwritten.                                          | courseid, newcourseid                                    |
| local_rsync_copy_all_section_modules          | Copies all modules in a section and puts it in the specified section. Still in dev.                                          |                                                          |
| local_rsync_create_section                    | Creates a new section. Is used when a user wants to upload a whole folder, so the foldername is the name of the new section. | courseid, sectionname                                    |

## Parameter list

Here is a list of all possible parameters and what they do:

| Parameter           | Description                                                  |
| ------------------- | ------------------------------------------------------------ |
| courseid            | Id of the course. Can be found in the url.                   |
| sectionumber        | Number of the section. Can be found in the url.              |
| targetsectionnumber | Number of the target section. Can be found in the url.       |
| filename/modulename | Name of the module.                                          |
| newcourseid         | Id of the target course. Can be found in the url.            |
| visibility          | If the target is visibile or not. 0 for hidden, 1 for shown. |
| sectionname         | Name of the section                                          |

## cURL

A cURL request to an endpoint may look like this:

`curl -d 'wstoken=<rsync_token>' -d 'wsfunction=<function_name>' -d 'courseid=<courseid>' <more_data> <moodlehost>/webservice/rest/server.php`

You can find the <rsync_token> in Preferences->Seurity Keys.
The <function_name> is documented in the above table.

## Python without options

The script in the [clients](/clients/) folder can be invoked without commands:

`python3 Rsync.py`

After the start of the script, you will be asked to input a token and a host url.
If you leave both inputs empty, the script wil lused the default values, which are coded in the script ([Will be changed](#12))

## Python with options

The script in the [clients](/clients/) folder can also be invoked with commands:

`python3 Rsync.py <option> <command>`

Option and command list:

| Options | Description          | Commands                |
| ------- | -------------------- | ----------------------- |
| -c      | Course options.      | v, cp                   |
| -s      | Section options.     | v, rn, rm, rma          |
| -m      | Module options.      | v, rm, rma, mv, mva, cp |
| -t      | Rsync token.         |                         |
| -u      | Host url.            |                         |
| -p      | Push.                | f, d                    |
| -h      | Shows the help page. |                         |

Command description:

| Option + command | Description                                                                                                                      | Invocation                                                  |
| ---------------- | -------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------- |
| -c v             | Changes course visibility.                                                                                                       | -c v,courseid,visibility                                    |
| -c cp            | Copies a course.                                                                                                                 | -c cp,courseid,newcourseid                                  |
| -s v             | Changes section visibility.                                                                                                      | -s v,courseid,sectionnumber,visibilty                       |
| -s rn            | Renames a section.                                                                                                               | -s rn,courseid,sectionnumber,sectionname                    |
| -s rm            | Removes a section.                                                                                                               | -s rm,courseid,sectionnumber                                |
| -s rma           | Removes all sections in a course.                                                                                                | -s rma,courseid                                             |
| -m v             | Changes module visibility.                                                                                                       | -m v,courseid,sectionnumber,modulename,visibility           |
| -m rm            | Removes a module from a section.                                                                                                 | -m rm,courseid,sectionnumber,modulename                     |
| -m rma           | Removes all modules from a section.                                                                                              | -m rma,courseid,sectionnumber                               |
| -m mv            | Moves a modules from a section to an other.                                                                                      | -m mv,courseid,sectionnumber,targetsectionnumber,modulename |
| -m mva           | Moves all modules from a section to an other.                                                                                    | -m mva,courseid,sectionnumber,targetsectionnumber           |
| -m cp            | Copies a module from a section to a target section.                                                                              | -m cp,courseid,sectionnumber,targetsectionnumber,modulename |
| -p f             | Pushes a file from the local machine to a section.                                                                               | -p f,filepath,filename,courseid,sectionnumber,displayname   |
| -p d             | Pushes a directory from the local machine to a section. The name of the directory is the name of the new section. Not recursive. | -p d,directory_path,courseid                                |

## Uploading files

There is a possibility to upload files to a moodle course. For that you need the `Moodle mobile web service`.
If you use the script u can change the variable `web_service_token` in the Rsync.py file.
An option to override the default is comming in the future.

If you use cURL you need to make 3 calls:

1. `curl -F "file_1=@$<filename>" "<moodle_host>/webservice/upload.php?token=$<web_service_token>`
   1.1 Copy the item id of the response.
2. `curl "<moodle_host>/webservice/rest/server.php?wstoken=$<web_service_token>&moodlewsrestformat=json&wsfunction=core_user_add_user_private_files&draftid=$<itemid>`
3. `curl -sS "<moodle_host>/webservice/rest/server.php?wstoken=$<rysnc_token>&wsfunction=local_ws_fileassistant_create_file_resource&filename=$<filename>&courseid=$<courseid>&sectionnumber=$<sectionnumber>&displayname=$<displayname>"`

## Acknowledgements

Thanks a lot to @lucaboesch for helping us in this project.
