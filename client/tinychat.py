#!/usr/bin/python2

'''
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2012 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2012 Game Maker 2k - http://intdb.sourceforge.net/
    Copyright 2012 Kazuki Przyborowski - https://github.com/KazukiPrzyborowski

    $FileInfo: tinychat.py - Last Update: 12/24/2012 Ver. 1.0.0 - Author: cooldude2k $
'''

import re, os, sys, getpass, readline, curses, hashlib, httplib, urllib, urllib2, cookielib, threading, time, socket, platform, base64;

if(len(sys.argv)<2):
 sys.exit();
parseurl = re.findall("(.*)\#([\da-z]+)", sys.argv[1]);
parseurl = parseurl[0];
chatsiteurl = parseurl[0];
chatroomname = parseurl[1];
if(len(re.findall("([\da-z]+)", chatroomname))<1):
 sys.exit();
myusername = str(raw_input("User: ")).decode("utf-8");
if(len(re.findall("([\da-z]+)", myusername))<1):
 sys.exit();
mypass = getpass.getpass();
mypasshash = hashlib.sha512(mypass.encode("utf-8")).hexdigest();
chaturl = chatsiteurl;
chathostname = getpass.getuser()+"@"+socket.gethostname();
chatproverinfo = ["TinyChat2k", 1, 0, 0, None];
if(sys.platform=="win32"):
 getwinver = sys.getwindowsversion();
 if(getwinver[3]==0):
  mywindowstype = "Windows 3.1";
 if(getwinver[3]==1):
  mywindowstype = "Windows 9x "+str(getwinver[0])+" "+str(getwinver[1]);
 if(getwinver[3]==2):
  mywindowstype = "Windows NT "+str(getwinver[0])+" "+str(getwinver[1]);
 if(getwinver[3]==3):
  mywindowstype = "Windows CE "+str(getwinver[0])+" "+str(getwinver[1]);
 chatua = "Mozilla/5.0 (compatible; "+chatproverinfo[0]+"/"+str(chatproverinfo[1])+"."+str(chatproverinfo[2])+"."+str(chatproverinfo[3])+"; "+mywindowstype+"; +"+chathostname+")";
if(sys.platform!="win32"):
 chatua = "Mozilla/5.0 (compatible; "+chatproverinfo[0]+"/"+str(chatproverinfo[1])+"."+str(chatproverinfo[2])+"."+str(chatproverinfo[3])+"; "+platform.system()+" "+platform.machine()+" "+platform.release()+"; +"+chathostname+")";
tinychat_cj = cookielib.CookieJar();
login_opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(tinychat_cj));
login_opener.addheaders = [("Referer", ""+chaturl), ("User-Agent", chatua)];
post_data = urllib.urlencode({'username': myusername, 'userpass' : mypasshash});
tinychattxt = login_opener.open(chaturl+"?act=login&room="+chatroomname, post_data);
signupcheck = tinychattxt.read()[:];
if(signupcheck=="{error:room};"):
 sys.exit();
if(signupcheck=="{error:loginuser};"):
 sys.exit();
if(signupcheck=="{warning:newuser};"):
 post_data = urllib.urlencode({'username': myusername, 'userpass': mypass});
 tinychattxt = login_opener.open(chaturl+"?act=signup&room="+chatroomname, post_data);
 signupcheck = tinychattxt.read()[:];
if(signupcheck=="{error:room};"):
 sys.exit();
if(signupcheck=="{error:loginuser};"):
 sys.exit();
screen = curses.initscr();
curses.echo();
curses.curs_set(1);
curses.start_color();
screen.clear();
screen.keypad(0);
screen.scrollok(True);
(win_maxy, win_maxx) = screen.getmaxyx();
chatwin = curses.newwin(win_maxy - 4,  win_maxx, 0, 0);
chatwin.clear();
chatwin.keypad(0);
chatwin.scrollok(True);
inputwin = curses.newwin(win_maxy,  win_maxx, win_maxy - 4, 0);
inputwin.clear();
inputwin.keypad(1);
inputwin.scrollok(True);
curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
chatwin.addstr("message: ", curses.color_pair(2));
curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK);
chatwin.addstr("Hello "+myusername+" welcome to chat room: "+chatroomname+"\n", curses.color_pair(3));
chatwin.refresh();
def getstr_prompt(txt_screen, txt_prompt): 
 curses.init_pair(1, curses.COLOR_GREEN, curses.COLOR_BLACK);
 txt_screen.addstr(txt_prompt, curses.color_pair(1));
 return str(txt_screen.getstr().decode("utf-8"))[:];
refreshtime=1;
threadloopstop=False;
def getnewmessages():
 global threadloopstop, refreshtime, login_opener;
 oldtimestamp = None;
 while threadloopstop==False:
  tinychattxt = login_opener.open(chaturl+"?act=view&room="+chatroomname);
  chattext = tinychattxt.readlines();
  chatsize = len(chattext);
  chati = 0;
  chatwin.refresh();
  while(chati<chatsize): 
   chatarray = re.findall("([0-9]+)\, ([0-9]+)\, \"([\da-z]+)\"\, \"(.*)\";", chattext[chati]);
   if(re.findall("([0-9]+)\, ([0-9]+)\, \"([\da-z]+)\"\, \"(.*)\";", chattext[chati])):
    chatarray = chatarray[0];
    curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
    chatwin.addstr(chatarray[2]+": ", curses.color_pair(2));
    curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK);
    chatwin.addstr(base64.b64decode(chatarray[3])+"\n", curses.color_pair(3));
    chatwin.refresh();
   chati = chati + 1;
  if(threadloopstop==False):
   time.sleep(refreshtime);
gnm = threading.Timer(refreshtime, getnewmessages);
gnm.start();
mymessagelc = None;
while(mymessagelc!="quit" and mymessagelc!="exit"):
 inputwin.clear();
 inputwin.refresh();
 try:
  mymessage = getstr_prompt(inputwin, myusername+": ");
 except Exception:
  break;
 curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
 chatwin.addstr(myusername+": ", curses.color_pair(2));
 curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK);
 chatwin.addstr(mymessage+"\n", curses.color_pair(3));
 chatwin.refresh();
 inputwin.refresh();
 mymessagelc = mymessage.lower();
 if(mymessagelc!="quit" and mymessagelc!="exit"):
  post_data = urllib.urlencode({'message': mymessage});
  tinychating = login_opener.open(chaturl+"?act=message&room="+chatroomname, post_data);
threadloopstop=True;
gnm.cancel();
curses.endwin();
tinychating = login_opener.open(chaturl+"?act=logout");
sys.exit();
