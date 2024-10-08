#!/usr/bin/python2

'''
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2012-2013 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2012-2013 Game Maker 2k - http://intdb.sourceforge.net/
    Copyright 2012-2013 Kazuki Przyborowski - https://github.com/KazukiPrzyborowski

    $FileInfo: tinychat.py - Last Update: 01/01/2013 Ver. 0.0.1 - Author: cooldude2k $
'''

import argparse
import base64
import curses
import getpass
import gzip
import hashlib
import os
import platform
import re
import readline
import socket
import sys
import threading
import time
import urllib

import cookielib
import httplib
import StringIO
import urllib2

chatproverinfo = ["TinyChat2k", 0, 0, 1, None]
gettermtype = None
parser = argparse.ArgumentParser()
parser.add_argument("--username", help="user name")
parser.add_argument("--password", help="user password")
parser.add_argument("--chatroom", help="chatroom name")
parser.add_argument("--url", help="tinychat url location")
parser.add_argument("--useragent", help="user agent to use")
tcargs = parser.parse_args()
if (sys.platform != "win32"):
    gettermtype = os.getenv('TERM')
    os.popen("stty sane")
    os.popen("clear")
    os.popen("reset")
if (sys.platform == "win32"):
    os.popen("cls")
chatprofullname = chatproverinfo[0] + " " + str(chatproverinfo[1]) + "." + str(
    chatproverinfo[2]) + "." + str(chatproverinfo[3])
chatprouaname = chatproverinfo[0] + "/" + str(chatproverinfo[1]) + "." + str(
    chatproverinfo[2]) + "." + str(chatproverinfo[3])
if (sys.platform != "win32" and gettermtype != "linux" and gettermtype !=
        "bsdos" and gettermtype != "freebsd" and gettermtype != "netbsd"):
    sys.stdout.write("\x1b]2;" + chatprofullname + " - Login\x07")
if (sys.platform == "win32"):
    os.system("title " + chatprofullname + " - Login")
if (tcargs.url is not None):
    tinychaturl = tcargs.url
if (tcargs.url is None):
    tinychaturl = str(raw_input("TinyChat URL: ")).decode("utf-8")
    tinychaturl = tinychaturl.strip()
myusername = None
mypass = None
if (re.findall("(.*)\\#([\\da-z]+)", tinychaturl)):
    parseurl = re.findall("(.*)\\#([\\da-z]+)", tinychaturl)
    parseurl = parseurl[0]
    chatsiteurl = parseurl[0].strip()
    chatroomname = parseurl[1].strip()
    if (len(re.findall("([\\da-z]+)", chatroomname)) < 1):
        sys.exit()
if (re.findall("(.*)\\#([\\da-z]+)\\@([\\da-z]+)", tinychaturl)):
    parseurl = re.findall("(.*)\\#([\\da-z]+)\\@([\\da-z]+)", tinychaturl)
    parseurl = parseurl[0]
    chatsiteurl = parseurl[0].strip()
    chatroomname = parseurl[2].strip()
    myusername = parseurl[1].strip()
    if (len(re.findall("([\\da-z]+)", chatroomname)) < 1):
        sys.exit()
    if (len(re.findall("([\\da-z]+)", myusername)) < 1):
        sys.exit()
if (re.findall("(.*)\\#([\\da-z]+)\\:(.*)\\@([\\da-z]+)", tinychaturl)):
    parseurl = re.findall(
        "(.*)\\#([\\da-z]+)\\:(.*)\\@([\\da-z]+)",
        tinychaturl)
    parseurl = parseurl[0]
    chatsiteurl = parseurl[0].strip()
    chatroomname = parseurl[3].strip()
    myusername = parseurl[1].strip()
    mypass = parseurl[2].strip()
    if (len(re.findall("([\\da-z]+)", chatroomname)) < 1):
        sys.exit()
    if (len(re.findall("([\\da-z]+)", myusername)) < 1):
        sys.exit()
    if (len(re.findall("([\\da-z]+)", mypass)) < 1):
        sys.exit()
if (
    not re.findall(
        "(.*)\\#([\\da-z]+)",
        tinychaturl) and not re.findall(
            "(.*)\\#([\\da-z]+)\\@([\\da-z]+)",
            tinychaturl) and not re.findall(
                "(.*)\\#([\\da-z]+)\\:(.*)\\@([\\da-z]+)",
        tinychaturl)):
    chatsiteurl = tinychaturl.strip()
    chatsiteurl = chatsiteurl.replace("#", "")
    if (tcargs.chatroom is not None):
        chatroomname = tcargs.chatroom.strip()
    if (tcargs.chatroom is None):
        chatroomname = str(raw_input("Chat Room: ")).decode("utf-8")
        chatroomname = chatroomname.strip()
    chatroomname = chatroomname.replace("#", "")
    if (len(re.findall("([\\da-z]+)", chatroomname)) < 1):
        sys.exit()
if (tcargs.username is not None and myusername is None):
    myusername = tcargs.username.strip()
if (tcargs.username is None and myusername is None):
    myusername = str(raw_input("User Name: ")).decode("utf-8")
    myusername = myusername.strip()
if (len(re.findall("([\\da-z]+)", myusername)) < 1):
    sys.exit()
if (tcargs.password is not None and mypass is None):
    mypass = tcargs.password
if (tcargs.password is None and mypass is None):
    mypass = getpass.getpass()
mypasshash = hashlib.sha512(mypass.encode("utf-8")).hexdigest()
chaturl = chatsiteurl
chathostname = getpass.getuser() + "@" + socket.gethostname()
if (sys.platform == "win32"):
    getwinver = sys.getwindowsversion()
    if (getwinver[3] == 0):
        mywindowstype = "Windows 3.1"
    if (getwinver[3] == 1):
        mywindowstype = "Windows 9x " + \
            str(getwinver[0]) + " " + str(getwinver[1])
    if (getwinver[3] == 2):
        mywindowstype = "Windows NT " + \
            str(getwinver[0]) + " " + str(getwinver[1])
    if (getwinver[3] == 3):
        mywindowstype = "Windows CE " + \
            str(getwinver[0]) + " " + str(getwinver[1])
    if (tcargs.useragent is not None):
        chatua = tcargs.useragent.strip()
    if (tcargs.useragent is None):
        chatua = "Mozilla/5.0 (compatible; " + chatprouaname + \
            "; " + mywindowstype + "; +" + chathostname + ")"
if (sys.platform != "win32"):
    if (tcargs.useragent is not None):
        chatua = tcargs.useragent.strip()
    if (tcargs.useragent is None):
        chatua = "Mozilla/5.0 (compatible; " + chatprouaname + "; " + platform.system(
        ) + " " + platform.machine() + " " + platform.release() + "; +" + chathostname + ")"
if (sys.platform != "win32" and gettermtype != "linux" and gettermtype !=
        "bsdos" and gettermtype != "freebsd" and gettermtype != "netbsd"):
    sys.stdout.write(
        "\x1b]2;" +
        chatprofullname +
        " - " +
        chatroomname +
        "\x07")
if (sys.platform == "win32"):
    os.system("title " + chatprofullname + " - " + chatroomname)
tinychat_cj = cookielib.CookieJar()
login_opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(tinychat_cj))
login_opener.addheaders = [
    ("Referer",
     "" + chaturl),
    ("User-Agent",
     chatua),
    ("Accept-Encoding",
     "gzip, deflate"),
    ("Accept-Language",
     "en-US,en-CA,en-GB,en-UK,en-AU,en-NZ,en-ZA,en"),
    ("Accept-Charset",
     "utf-8"),
    ("Accept",
     "text/plain")]
tinychatchk = login_opener.open(chaturl + "?act=check&room=" + chatroomname)
if (tinychatchk.info().get("Content-Encoding") ==
        "gzip" or tinychatchk.info().get("Content-Encoding") == "deflate"):
    strbuf = StringIO.StringIO(tinychatchk.read())
    gzstrbuf = gzip.GzipFile(fileobj=strbuf)
    tinychatcheck = gzstrbuf.read()[:]
if (tinychatchk.info().get("Content-Encoding") !=
        "gzip" and tinychatchk.info().get("Content-Encoding") != "deflate"):
    tinychatcheck = tinychatchk.read()[:]
if (tinychatcheck != "{success:tinychat};"):
    sys.exit()
usrjustsignedup = False
post_data = urllib.urlencode({'username': myusername, 'userpass': mypasshash})
tinychattxt = login_opener.open(
    chaturl + "?act=login&room=" + chatroomname, post_data)
if (tinychattxt.info().get("Content-Encoding") ==
        "gzip" or tinychattxt.info().get("Content-Encoding") == "deflate"):
    strbuf = StringIO.StringIO(tinychattxt.read())
    gzstrbuf = gzip.GzipFile(fileobj=strbuf)
    signupcheck = gzstrbuf.read()[:]
if (tinychattxt.info().get("Content-Encoding") !=
        "gzip" and tinychattxt.info().get("Content-Encoding") != "deflate"):
    signupcheck = tinychattxt.read()[:]
if (signupcheck == "{error:room};"):
    sys.exit()
if (signupcheck == "{error:loginuser};"):
    sys.exit()
if (signupcheck == "{warning:newuser};"):
    usrjustsignedup = True
    post_data = urllib.urlencode(
        {'username': myusername, 'userpass': mypasshash})
    tinychattxt = login_opener.open(
        chaturl + "?act=signup&room=" + chatroomname, post_data)
    if (tinychattxt.info().get("Content-Encoding") ==
            "gzip" or tinychattxt.info().get("Content-Encoding") == "deflate"):
        strbuf = StringIO.StringIO(tinychattxt.read())
        gzstrbuf = gzip.GzipFile(fileobj=strbuf)
        signupcheck = gzstrbuf.read()[:]
    if (tinychattxt.info().get("Content-Encoding") !=
            "gzip" and tinychattxt.info().get("Content-Encoding") != "deflate"):
        signupcheck = tinychattxt.read()[:]
    if (signupcheck == "{success:signupuser};"):
        post_data = urllib.urlencode(
            {'username': myusername, 'userpass': mypasshash})
        tinychattxt = login_opener.open(
            chaturl + "?act=login&room=" + chatroomname, post_data)
        if (tinychattxt.info().get("Content-Encoding") ==
                "gzip" or tinychattxt.info().get("Content-Encoding") == "deflate"):
            strbuf = StringIO.StringIO(tinychattxt.read())
            gzstrbuf = gzip.GzipFile(fileobj=strbuf)
            signupcheck = gzstrbuf.read()[:]
        if (tinychattxt.info().get("Content-Encoding") !=
                "gzip" and tinychattxt.info().get("Content-Encoding") != "deflate"):
            signupcheck = tinychattxt.read()[:]
    if (signupcheck == "{error:signupuser};"):
        sys.exit()
if (signupcheck == "{error:room};"):
    sys.exit()
if (signupcheck == "{error:loginuser};"):
    sys.exit()
screen = curses.initscr()
curses.echo()
curses.curs_set(1)
curses.start_color()
screen.clear()
screen.keypad(0)
screen.scrollok(True)
(win_maxy, win_maxx) = screen.getmaxyx()
chatwin = curses.newwin(win_maxy - 3, win_maxx, 0, 0)
chatwin.clear()
chatwin.keypad(0)
chatwin.scrollok(True)
inputwin = curses.newwin(win_maxy, win_maxx, win_maxy - 3, 0)
inputwin.clear()
inputwin.keypad(1)
inputwin.scrollok(True)
if (usrjustsignedup == False):
    tinychating = login_opener.open(
        chaturl + "?act=status&type=hello&room=" + chatroomname)
if (usrjustsignedup):
    tinychating = login_opener.open(
        chaturl + "?act=status&type=signup&room=" + chatroomname)
tinychattxt = login_opener.open(
    chaturl + "?act=status&type=welcome&room=" + chatroomname)
if (tinychattxt.info().get("Content-Encoding") ==
        "gzip" or tinychattxt.info().get("Content-Encoding") == "deflate"):
    strbuf = StringIO.StringIO(tinychattxt.read())
    gzstrbuf = gzip.GzipFile(fileobj=strbuf)
    welcometext = gzstrbuf.read()[:]
if (tinychattxt.info().get("Content-Encoding") !=
        "gzip" and tinychattxt.info().get("Content-Encoding") != "deflate"):
    welcometext = tinychattxt.read()[:]
if (
    re.findall(
        "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
        welcometext)):
    welcomearray = re.findall(
        "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
        welcometext)
    welcomearray = welcomearray[0]
    curses.init_pair(4, curses.COLOR_BLUE, curses.COLOR_BLACK)
    chatwin.addstr(base64.b64decode(
        welcomearray[2]) + ": ", curses.color_pair(4))
    curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
    chatwin.addstr(base64.b64decode(
        welcomearray[3]) + "\n", curses.color_pair(3))
if (
    not re.findall(
        "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
        welcometext) and re.findall(
            "([0-9\\.]+)\\, ([0-9]+)\\, \"([\\da-z]+)\"\\, \"(.*)\";",
        welcometext)):
    welcomearray = re.findall(
        "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
        welcometext)
    welcomearray = welcomearray[0]
    curses.init_pair(4, curses.COLOR_BLUE, curses.COLOR_BLACK)
    chatwin.addstr(base64.b64decode(
        welcomearray[2]) + ": ", curses.color_pair(4))
    curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
    chatwin.addstr(base64.b64decode(
        welcomearray[3]) + "\n", curses.color_pair(3))
if (
    not re.findall(
        "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
        welcometext) and not re.findall(
            "([0-9\\.]+)\\, ([0-9]+)\\, \"([\\da-z]+)\"\\, \"(.*)\";",
        welcometext)):
    curses.init_pair(4, curses.COLOR_BLUE, curses.COLOR_BLACK)
    chatwin.addstr("message: ", curses.color_pair(4))
    curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
    chatwin.addstr("Hello " + myusername + " welcome to chat room: " +
                   chatroomname + "\n", curses.color_pair(3))
chatwin.refresh()


def getstr_prompt(txt_screen, txt_prompt):
    curses.init_pair(1, curses.COLOR_GREEN, curses.COLOR_BLACK)
    txt_screen.addstr(txt_prompt, curses.color_pair(1))
    return str(txt_screen.getstr().decode("utf-8"))[:]


refreshtime = 1
threadloopstop = False


def getnewmessages():
    global threadloopstop, refreshtime, login_opener
    while threadloopstop == False:
        tinychattxt = login_opener.open(
            chaturl + "?act=view&room=" + chatroomname)
        if (tinychattxt.info().get("Content-Encoding") ==
                "gzip" or tinychattxt.info().get("Content-Encoding") == "deflate"):
            strbuf = StringIO.StringIO(tinychattxt.read())
            gzstrbuf = gzip.GzipFile(fileobj=strbuf)
            chattext = gzstrbuf.readlines()[:]
        if (tinychattxt.info().get("Content-Encoding") !=
                "gzip" and tinychattxt.info().get("Content-Encoding") != "deflate"):
            chattext = tinychattxt.readlines()[:]
        chatsize = len(chattext)
        chati = 0
        chatwin.refresh()
        while (chati < chatsize):
            if (
                re.findall(
                    "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
                    chattext[chati])):
                chatarray = re.findall(
                    "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
                    chattext[chati])
                chatarray = chatarray[0]
                if (int(chatarray[1]) > 0):
                    curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK)
                    chatwin.addstr(base64.b64decode(
                        chatarray[2]) + ": ", curses.color_pair(2))
                if (int(chatarray[1]) == 0):
                    curses.init_pair(4, curses.COLOR_BLUE, curses.COLOR_BLACK)
                    chatwin.addstr(base64.b64decode(
                        chatarray[2]) + ": ", curses.color_pair(4))
                curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
                chatwin.addstr(base64.b64decode(
                    chatarray[3]) + "\n", curses.color_pair(3))
                chatwin.refresh()
            if (
                not re.findall(
                    "\\{timestamp\\:([0-9\\.]+)\\,userid\\:([0-9]+)\\,username\\:\"(.*)\"\\,message\\:\"(.*)\"};",
                    chattext[chati]) and re.findall(
                    "([0-9\\.]+)\\, ([0-9]+)\\, \"([\\da-z]+)\"\\, \"(.*)\";",
                    chattext[chati])):
                chatarray = re.findall(
                    "([0-9\\.]+)\\, ([0-9]+)\\, \"([\\da-z]+)\"\\, \"(.*)\";",
                    chattext[chati])
                chatarray = chatarray[0]
                if (int(chatarray[1]) > 0):
                    curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK)
                    chatwin.addstr(base64.b64decode(
                        chatarray[2]) + ": ", curses.color_pair(2))
                if (int(chatarray[1]) == 0):
                    curses.init_pair(4, curses.COLOR_BLUE, curses.COLOR_BLACK)
                    chatwin.addstr(base64.b64decode(
                        chatarray[2]) + ": ", curses.color_pair(4))
                curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK)
                chatwin.addstr(base64.b64decode(
                    chatarray[2]) + ": ", curses.color_pair(2))
                curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
                chatwin.addstr(base64.b64decode(
                    chatarray[3]) + "\n", curses.color_pair(3))
                chatwin.refresh()
            chati = chati + 1
        if (threadloopstop == False):
            time.sleep(refreshtime)


gnm = threading.Timer(refreshtime, getnewmessages)
gnm.start()
mymessagelc = None
while (mymessagelc != "quit" and mymessagelc != "exit"):
    inputwin.clear()
    inputwin.refresh()
    try:
        mymessage = getstr_prompt(inputwin, myusername + ": ")
    except Exception:
        break
    mymessage = mymessage.strip()
    mymessagelc = mymessage.lower()
    if (mymessagelc != "quit" and mymessagelc !=
            "exit" and mymessagelc != "" and mymessagelc is not None):
        curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK)
        chatwin.addstr(myusername + ": ", curses.color_pair(2))
        curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK)
        chatwin.addstr(mymessage + "\n", curses.color_pair(3))
        chatwin.refresh()
        inputwin.refresh()
        post_data = urllib.urlencode({'message': mymessage})
        tinychating = login_opener.open(
            chaturl + "?act=message&room=" + chatroomname, post_data)
threadloopstop = True
gnm.cancel()
curses.endwin()
tinychating = login_opener.open(
    chaturl + "?act=status&type=goodbye&room=" + chatroomname)
tinychating = login_opener.open(chaturl + "?act=logout")
if (sys.platform == "win32"):
    os.popen("cls")
if (sys.platform != "win32"):
    os.popen("stty sane")
    os.popen("clear")
    os.popen("reset")
sys.exit()
