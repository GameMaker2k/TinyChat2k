#!/usr/bin/python

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

    $FileInfo: tinychat.py - Last Update: 12/20/2012 Ver. 1.0.0 - Author: cooldude2k $
'''

import re, os, sys, getpass, readline, curses, hashlib, httplib, urllib, urllib2, cookielib, threading, time;

if(len(sys.argv)<5):
 sys.exit();
if(len(re.findall("([\da-z]+)", sys.argv[4]))<1):
 sys.exit();
myusername = str(raw_input("User: ")).decode("utf-8");
if(len(re.findall("([\da-z]+)", myusername))<1):
 sys.exit();
mypass = getpass.getpass();
mypasshash = hashlib.sha512(mypass.encode("utf-8")).hexdigest();
chaturl = sys.argv[1]+"://"+sys.argv[2]+sys.argv[3];
tinychat_cj = cookielib.CookieJar();
login_opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(tinychat_cj));
login_opener.addheaders = [("Referer", ""+chaturl+"api.php"), ("User-Agent", "Mozilla/5.0 (compatible; TinyChat2k/1.0.0; +"+chaturl+")")];
post_data = urllib.urlencode({'username': myusername, 'userpass' : mypasshash});
tinychattxt = login_opener.open(chaturl+"api.php?act=login&room="+sys.argv[4], post_data);
signupcheck = tinychattxt.read()[:];
if(signupcheck=="{error:room};"):
 sys.exit();
if(signupcheck=="{error:loginuser};"):
 sys.exit();
if(signupcheck=="{warning:newuser};"):
 post_data = urllib.urlencode({'username': myusername, 'userpass': mypass});
 tinychattxt = login_opener.open(chaturl+"api.php?act=signup&room="+sys.argv[4], post_data);
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

def getstr_prompt(txt_screen, txt_prompt): 
 curses.init_pair(1, curses.COLOR_GREEN, curses.COLOR_BLACK);
 txt_screen.addstr(txt_prompt, curses.color_pair(1));
 return str(txt_screen.getstr().decode("utf-8"))[:];

refreshtime=1;
threadloopstop=False;
def getnewmessages():
 global threadloopstop, refreshtime, login_opener;
 while threadloopstop==False:
  timestampstart = int(re.findall("([0-9]+)\.", str(time.time()))[0]) - 1;
  tinychattxt = login_opener.open(chaturl+"api.php?act=view&room="+sys.argv[4]+"&tsstart="+str(timestampstart));
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
    chatwin.addstr(chatarray[3]+"\n", curses.color_pair(3));
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
 mymessage = getstr_prompt(inputwin, myusername+": ");
 curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
 chatwin.addstr(myusername+": ", curses.color_pair(2));
 curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK);
 chatwin.addstr(mymessage+"\n", curses.color_pair(3));
 chatwin.refresh();
 inputwin.refresh();
 mymessagelc = mymessage.lower();
 if(mymessagelc!="quit" and mymessagelc!="exit"):
  post_data = urllib.urlencode({'message': mymessage});
  tinychating = login_opener.open(chaturl+"api.php?act=message&room="+sys.argv[4], post_data);

threadloopstop=True;
gnm.cancel();
curses.endwin();
sys.exit();
