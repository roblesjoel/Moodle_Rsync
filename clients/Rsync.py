from posixpath import split
from optparse import OptionParser
from tkinter.messagebox import NO
import os
from os import listdir
from os.path import isfile, join
import requests
import json

url = 'http://192.168.64.3/moodle/webservice/rest/server.php'
web_service_token = 'f039dbb7831dbede493ce6f18065ed55'
wstoken = '36271651ba925af6b783c8c45994dfd4'
error = 'ERRORCODE'

# usage = "usage: %prog [options] arg1 arg2"
# parser = OptionParser(usage=usage)

parser = OptionParser()
# course -c
# Visibility: v,courseid,visibility -> v,2,1
# Copy: cp,courseid,targetcourseid -> cp,2,3
coursehelp = 'Command to change a course. Visibility: v,courseid,visibility -> v,2,1. Copy: cp,courseid,targetcourseid -> cp,2,3'
parser.add_option("-c", "--course", dest="course", type="string", help=coursehelp)

# section -s
# Visibility: v,courseid,sectionnumber,visibility -> v,2,2,1
# Rename: rn,courseid,sectionumber,sectionname -> rn,2,2,newname
# Remove: rm,courseid,sectionumber -> rm,2,2
# Remove all: rma,courseid -> rma,2
sectionhelp = 'Command to change a section. Visibility: v,courseid,sectionnumber,visibility -> v,2,2,1. Rename: rn,courseid,sectionumber,sectionname -> rn,2,2,newname. Remove: rm,courseid,sectionumber -> rm,2,2. Remove all: rma,courseid -> rma,2'
parser.add_option("-s", "--section", dest="section", type="string", help=sectionhelp)

# module -m
# Visibility: v,courseid,sectionnumer,visibility,modulename -> v,2,2,test.pdf,1
# Remove from section: rm,courseid,sectionnumber,modulename -> rs,2,2,test.pdf
# Remove all from section: rma,courseid,sectionnumber -> rma,2,2
# Move to other section: mv,courseid,sectionnumber,targetsectionnumber,modulename -> mv,2,2,3,test.pdf
# Move all to other section: mva,courseid,sectionnumber,targetsectionumber -> mva,2,2,3
# Duplicate module: cp,courseid,sectionnunber,targetsectionumber,modulename -> cp,2,2,3,test.pdf
modulehelp = 'Command to change a module. Visibility: v,courseid,sectionnumer,visibility,modulename -> v,2,2,test.pdf,1. Remove from section: rm,courseid,sectionnumber,modulename -> rm,2,2,test.pdf. Remove all from section: rma,courseid,sectionnumber -> rma,2,2. Move to other section: mv,courseid,sectionnumber,targetsectionnumber,modulename -> mv,2,2,3,test.pdf. Move all to other section: mva,courseid,sectionnumber,targetsectionumber -> mva,2,2,3. Duplicate module: cp,courseid,sectionnunber,targetsectionumber,modulename -> cp,2,2,3,test.pdf.'
parser.add_option("-m", "--module", dest="module", type="string", help=modulehelp)

# push -p
# File: f,filepath,filename,courseid,sectionnumber,displayname -> f,files/test.pdf,test.pdf,2,2,curl.txt
# Directory: d,directory_path,courseid -> d,files/,2
pushhelp = 'Command to push something. File: f,filepath,filename,courseid,sectionnumber,displayname -> f,files/test.pdf,test.pdf,2,2,curl.txt. Directory: d,directory_path,courseid -> d,files/,2'
parser.add_option("-p", "--push", dest="push", type="string", help=pushhelp)

# wstoken -t
parser.add_option("-t", "--token", dest="token", type="string", help="Command to change the default token.")

# host -u
parser.add_option("-u", "--url", dest="host", type="string", help="Command to change the default host url.")

(options, args) = parser.parse_args()

commandlist = ['cv', 'ccp', 'sv', 'srn', 'srm', 'srma', 'mv', 'mrm', 'mrma', 'mmv', 'mmva', 'mcp', 'pf', 'pd', 'q']
commandlistText = ['Change the visibility of a course', 'Copy a course to an other (removes target course content)', 'Change the visibility of a section', 'Rename target section', 'Remove target section', 'Remove all sections from course', 'Change the visibility of a module',
                   'Remove module from section', 'Remove all modules from section', 'Move module to other section', 'Move all modules from a section to an other', 'Duplicates a module an puts it in the target section', 'Pushes a file to a given course and section', 'Pushes a directory into a course. Directory name = new section name.', 'Quit Moodle rsync']


def printerror(errordata):
    spliterror = errordata.split('<')[5]
    errorcode = spliterror.replace('MESSAGE>', '')
    print(errorcode)


def printsuccess(successdata):
    splitsuccess = successdata.split('<')[3]
    successmessage = splitsuccess.replace('VALUE>', '')
    print(successmessage)


def set_course_visibility(courseid, visibility):
    if(visibility != '0' and visibility != '1'):
        print("The visibility value needs to be 0 or 1")
        return 1

    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_change_course_visibility',
            'courseid': courseid, 'visibility': visibility}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def set_section_visibility(courseid, sectionnumber, visibility):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_set_section_visibility' -d 'courseid=2' -d 'sectionnumber=2' -d 'visibility=1' http://192.168.64.3/moodle/webservice/rest/server.php
    if(visibility != '0' and visibility != '1'):
        print("The visibility value needs to be 0 or 1")
        return 1

    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_set_section_visibility',
            'courseid': courseid, 'sectionnumber': sectionnumber, 'visibility': visibility}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def set_module_visibility(courseid, sectionnumber, modulename, visibility):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_set_file_visibility' -d 'courseid=2' -d 'sectionnumber=2' -d 'filename=new.pdf' -d 'visibility=1' http://192.168.64.3/moodle/webservice/rest/server.php
    if(visibility != '0' and visibility != '1'):
        print("The visibility value needs to be 0 or 1")
        return 1

    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_set_file_visibility', 'courseid': courseid,
            'sectionnumber': sectionnumber, 'filename': modulename, 'visibility': visibility}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def remove_module_from_section(courseid, sectionnumber, modulename):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_remove_file_from_section' -d 'courseid=2' -d 'sectionnumber=5' -d 'filename=show.pdf' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_remove_file_from_section', 'courseid': courseid,
            'sectionnumber': sectionnumber, 'filename': modulename}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def rename_section(courseid, sectionnumber, sectionname):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_rename_section' -d 'courseid=2' -d 'sectionnumber=1' -d 'sectionname=Name' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_rename_section',
            'courseid': courseid, 'sectionnumber': sectionnumber, 'sectionname': sectionname}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def remove_section(courseid, sectionnumber):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_remove_section' -d 'courseid=2' -d 'sectionnumber=5' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_remove_section',
            'courseid': courseid, 'sectionnumber': sectionnumber}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def remove_all_modules_from_section(courseid, sectionnumber):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_remove_all_files_from_section' -d 'courseid=2' -d 'sectionnumber=1' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_remove_all_files_from_section',
            'courseid': courseid, 'sectionnumber': sectionnumber}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def move_module_to_other_section(courseid, sectionnumber, targetsectionnumber, modulename):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_move_file_to_other_section' -d 'courseid=2' -d 'sectionnumber=2' -d 'targetsectionnumber=1' -d 'modulename=test3.pdf' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_move_file_to_other_section', 'courseid': courseid,
            'sectionnumber': sectionnumber, 'targetsectionnumber': targetsectionnumber, 'modulename': modulename}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def move_all_modules_to_other_section(courseid, sectionnumber, targetsectionnumber):
    # curl -d 'wstoken=e09069aa9854dcc77ed4ad0f70626474' -d 'wsfunction=local_rsync_move_all_modules_to_other_section' -d 'courseid=2' -d 'sectionnumber=1' -d 'targetsectionnumber=2' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_move_all_modules_to_other_section',
            'courseid': courseid, 'sectionnumber': sectionnumber, 'targetsectionnumber': targetsectionnumber}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def copy_course(courseid, targetcourseid):
    # curl -d 'wstoken=e843dfbb8dd013334246dfaaa76b3f2c' -d 'wsfunction=local_rsync_copy_course' -d 'courseid=2' -d 'newcourseid=3' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_copy_course',
            'courseid': courseid, 'newcourseid': targetcourseid}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def remove_all_sections(courseid):
    # curl -d 'wstoken=b8c810369a24363721069cdeff46d37e' -d 'wsfunction=local_rsync_remove_all_sections' -d 'courseid=3' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_remove_all_sections', 'courseid': courseid}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def copy_module(courseid, sectionnumber, targetsectionnumber, modulename):
    # curl -d 'wstoken=f0706afc83a345a071d357caaf06e376' -d 'wsfunction=local_rsync_copy_module' -d 'courseid=4' -d 'sectionnumber=1' -d 'targetsectionnumber=10' -d 'modulename=test2.pdf' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_copy_module',
            'courseid': courseid, 'sectionnumber': sectionnumber, 'targetsectionnumber': targetsectionnumber, 'modulename': modulename}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def upload_file(filename, filepath, courseid, sectionumber, displayname):
    uploadurl = 'http://192.168.64.3/moodle/webservice/upload.php?token={}'.format(web_service_token)
    files = {'file_1': (filename, open(filepath, 'rb'))}
    response = requests.post(uploadurl, files=files)
    responsedata = json.loads(response.text)[0]
    itemid = responsedata["itemid"]

    data = {'wstoken': web_service_token, 'moodlewsrestformat': 'json',
            'wsfunction': 'core_user_add_user_private_files', 'draftid': itemid}
    response = requests.post(url, data=data)
    responsedata = response.text

    # curl - d 'wstoken=18ca69e1f635e64e104a24353f060780' - d 'wsfunction=local_rsync_create_file_resource' - d 'filename=raport.pdf' - d 'courseid=2' - d 'sectionnumber=1' - d 'displayname=test2.pdf' http: // 192.168.64.3/moodle/webservice/rest/server.php

    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_create_file_resource', 'filename': filename,
            'courseid': courseid, 'sectionnumber': sectionumber, 'displayname': displayname}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    printsuccess(responsedata)
    return 0


def upload_folder(courseid, directory_path):
    dirname = directory_path.split('/')[-1]
    # curl -d 'wstoken=36271651ba925af6b783c8c45994dfd4' -d 'wsfunction=local_rsync_create_section' -d 'courseid=4' -d 'sectionname=test Section' http://192.168.64.3/moodle/webservice/rest/server.php
    data = {'wstoken': wstoken, 'wsfunction': 'local_rsync_create_section',
            'courseid': courseid, 'sectionname': dirname}
    response = requests.post(url, data=data)
    responsedata = response.text

    if(error in responsedata):
        printerror(responsedata)
        return 1

    splitdata = responsedata.split(' ')

    sectionnumber = -2
    print(splitdata)

    for i in range(len(splitdata)):
        if(splitdata[i] == "number"):
            sectionnumber = splitdata[i+1]
            break

    onlyfiles = [f for f in listdir(directory_path) if isfile(join(directory_path, f))]

    for file in onlyfiles:
        filename = file
        filepath = os.path.join(directory_path, file)
        upload_file(filename, filepath, courseid, sectionnumber, filename)

    return 0


if(options.course == options.section == options.module == options.push is None):
    print('Welcome to Moodle Rsync!')
    token = input('Enter a custum token (leave blank for default): ')
    hosturl = input('Enter a custum url (leave blank for default): ')
    if(token != ''):
        wstoken = token
    if(hosturl != ''):
        url = hosturl
    print('Possible commands:')

    optionscount = len(commandlist)

    for i in range(optionscount):
        print('{}\t{}'.format(commandlist[i], commandlistText[i]))

    keepgoing = True
    while(keepgoing):
        print('Choose a command: ', end='')
        chosencommand = input()

        if (chosencommand in commandlist):
            if(chosencommand == 'q'):
                keepgoing = False
            elif(chosencommand == 'cv'):
                courseid = input('Enter the id of the course: ')
                visibility = input('Enter the visibility value (0 for hidden, 1 for shown): ')
                set_course_visibility(courseid, visibility)
            elif(chosencommand == 'cp'):
                courseid = input('Enter the id of the course to be copied: ')
                targetcourseid = input('Enter the id of the target course: ')
                copy_course(courseid, targetcourseid)
            elif(chosencommand == 'sv'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                visibility = input('Enter the visibility value (0 for hidden, 1 for shown): ')
                set_section_visibility(courseid, sectionnumber, visibility)
            elif(chosencommand == 'srn'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section to be renamed: ')
                sectionname = input('Enter the new name of the section: ')
                rename_section(courseid, sectionnumber, sectionname)
            elif(chosencommand == 'srm'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section to be removed: ')
                remove_section(courseid, sectionnumber)
            elif(chosencommand == 'srma'):
                courseid = input('Enter the id of the course: ')
                remove_all_sections(courseid)
            elif(chosencommand == 'mv'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                modulename = input('Enter the name of the module: ')
                visibility = input('Enter the visibility value (0 for hidden, 1 for shown): ')
                set_module_visibility(courseid, sectionnumber, modulename, visibility)
            elif(chosencommand == 'mrm'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                modulename = input('Enter the name of the module to be removed: ')
                remove_module_from_section(courseid, sectionnumber, modulename)
            elif(chosencommand == 'mrma'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                remove_all_modules_from_section(courseid, sectionnumber)
            elif(chosencommand == 'mmv'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                targetsectionumber = input('Enter the number of the target section: ')
                modulename = input('Enter the name of the module: ')
                move_module_to_other_section(courseid, sectionnumber, targetsectionumber, modulename)
            elif(chosencommand == 'mmva'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                targetsectionumber = input('Enter the number of the target section: ')
                move_all_modules_to_other_section(courseid, sectionnumber, targetsectionumber)
            elif(chosencommand == 'mcp'):
                courseid = input('Enter the id of the course: ')
                sectionnumber = input('Enter the number of the section: ')
                targetsectionumber = input('Enter the number of the target section: ')
                modulename = input('Enter the name of the module: ')
                copy_module(courseid, sectionnumber, targetsectionumber, modulename)
            elif(chosencommand == 'pf'):
                filename = input('Enter the name of the file: ')
                filepath = input('Enter the path of the file: ')
                courseid = input('Enter the id of the target course: ')
                sectionnumber = input('Enter the sectionnumber where the file will be placed: ')
                displayname = input('Enter how the file should be called: ')
                upload_file(filename, filepath, courseid, sectionnumber, displayname)
            elif(chosencommand == 'pd'):
                directory_path = input('Enter the path of the directory: ')
                courseid = input('Enter the id of the target course: ')
                upload_folder(courseid, directory_path)
        else:
            print('Command not found! Please try again')

# if there are command optionsget them all
courseoptions = options.course
sectionoptions = options.section
moduleoptions = options.module
pushoptions = options.push

token = options.token
hosturl = options.host
if(token is not None):
    wstoken = token
if(hosturl is not None):
    url = hosturl

if(pushoptions is not None):
    optionssplits = pushoptions.split('-')

    for optionsplit in optionssplits:
        infos = optionsplit.split(',')
        if(infos[0] == 'f'):
            upload_file(infos[1], infos[2], infos[3], infos[4], infos[5])
        if(infos[0] == 'd'):
            upload_folder(infos[1], infos[2])
if(moduleoptions is not None):
    # split at -
    optionssplits = moduleoptions.split('-')

    for optionsplit in optionssplits:
        infos = optionsplit.split(',')
        if(infos[0] == 'v'):
            set_module_visibility(infos[1], infos[2], infos[3], infos[4])
        if(infos[0] == 'rm'):
            remove_module_from_section(infos[1], infos[2], infos[3])
        if(infos[0] == 'rma'):
            remove_all_modules_from_section(infos[1], infos[2])
        if(infos[0] == 'mv'):
            move_module_to_other_section(infos[1], infos[2], infos[3], infos[4])
        if(infos[0] == 'mva'):
            move_all_modules_to_other_section(infos[1], infos[2], infos[3])
        if(infos[0] == 'cp'):
            copy_module(infos[1], infos[2], infos[3], infos[4])
if(sectionoptions is not None):
    # split at -
    optionssplits = sectionoptions.split('-')

    for optionsplit in optionssplits:
        infos = optionsplit.split(',')
        if(infos[0] == 'v'):
            set_section_visibility(infos[1], infos[2], infos[3])
        if(infos[0] == 'rn'):
            rename_section(infos[1], infos[2], infos[3])
        if(infos[0] == 'rm'):
            remove_section(infos[1], infos[2])
        if(infos[0] == 'rma'):
            remove_all_sections(infos[1])
if(courseoptions is not None):
    # split at -
    optionssplits = courseoptions.split('-')

    for optionsplit in optionssplits:
        infos = optionsplit.split(',')
        if(infos[0] == 'cp'):
            copy_course(infos[1], infos[2])
        if(infos[0] == 'v'):
            set_course_visibility(infos[1], infos[2])
