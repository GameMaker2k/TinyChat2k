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

    $FileInfo: tinychat.py - Last Update: 12/28/2012 Ver. 0.0.1 - Author: cooldude2k $
'''

import re, os, sys, getpass, readline, curses, hashlib, httplib, urllib, urllib2, cookielib, threading, time, socket, platform, base64;

chatproverinfo = ["TinyChat2k", 0, 0, 1, None];
gettermtype=None;
if(sys.platform!="win32"):
 gettermtype=os.getenv('TERM');
 os.popen("stty sane");
 os.popen("clear");
 os.popen("reset");
if(sys.platform=="win32"):
 os.popen("cls");
chatprofullname = chatproverinfo[0]+" "+str(chatproverinfo[1])+"."+str(chatproverinfo[2])+"."+str(chatproverinfo[3]);
chatprouaname = chatproverinfo[0]+"/"+str(chatproverinfo[1])+"."+str(chatproverinfo[2])+"."+str(chatproverinfo[3]);
if(sys.platform!="win32" and gettermtype!="linux" and gettermtype!="bsdos" and gettermtype!="freebsd" and gettermtype!="netbsd"):
 sys.stdout.write("\x1b]2;"+chatprofullname+" - Login\x07");
if(sys.platform=="win32"):
 os.system("title "+chatprofullname+" - Login");
if(len(sys.argv)>=2):
 tinychaturl = sys.argv[1];
if(len(sys.argv)<2):
 tinychaturl = str(raw_input("TinyChat URL: ")).decode("utf-8");
 tinychaturl = tinychaturl.strip();
if(re.findall("(.*)\#([\da-z]+)", tinychaturl)):
 parseurl = re.findall("(.*)\#([\da-z]+)", tinychaturl);
 parseurl = parseurl[0];
 chatsiteurl = parseurl[0].strip();
 chatsiteurl = chatsiteurl.replace("#", "");
 chatroomname = parseurl[1].strip();
 chatroomname = chatroomname.replace("#", "");
 if(len(re.findall("([\da-z]+)", chatroomname))<1):
  sys.exit();
if(not re.findall("(.*)\#([\da-z]+)", tinychaturl)):
 chatsiteurl = tinychaturl.strip();
 chatsiteurl = chatsiteurl.replace("#", "");
 chatroomname = str(raw_input("Chat Room: ")).decode("utf-8");
 chatroomname = chatroomname.strip();
 chatroomname = chatroomname.replace("#", "");
 if(len(re.findall("([\da-z]+)", chatroomname))<1):
  sys.exit();
myusername = str(raw_input("User Name: ")).decode("utf-8");
myusername = myusername.strip();
if(len(re.findall("([\da-z]+)", myusername))<1):
 sys.exit();
mypass = getpass.getpass();
mypasshash = hashlib.sha512(mypass.encode("utf-8")).hexdigest();
chaturl = chatsiteurl;
chathostname = getpass.getuser()+"@"+socket.gethostname();
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
 chatua = "Mozilla/5.0 (compatible; "+chatprouaname+"; "+mywindowstype+"; +"+chathostname+")";
if(sys.platform!="win32"):
 chatua = "Mozilla/5.0 (compatible; "+chatprouaname+"; "+platform.system()+" "+platform.machine()+" "+platform.release()+"; +"+chathostname+")";
if(sys.platform!="win32" and gettermtype!="linux" and gettermtype!="bsdos" and gettermtype!="freebsd" and gettermtype!="netbsd"):
 sys.stdout.write("\x1b]2;"+chatprofullname+" - "+chatroomname+"\x07");
if(sys.platform=="win32"):
 os.system("title "+chatprofullname+" - "+chatroomname);
tinychat_cj = cookielib.CookieJar();
login_opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(tinychat_cj));
login_opener.addheaders = [("Referer", ""+chaturl), ("User-Agent", chatua)];
tinychatchk = login_opener.open(chaturl+"?act=check&room="+chatroomname);
tinychatcheck = tinychatchk.read()[:];
if(tinychatcheck!="{success:tinychat};"):
 sys.exit();
post_data = urllib.urlencode({'username': myusername, 'userpass' : mypasshash});
tinychattxt = login_opener.open(chaturl+"?act=login&room="+chatroomname, post_data);
signupcheck = tinychattxt.read()[:];
if(signupcheck=="{error:room};"):
 sys.exit();
if(signupcheck=="{error:loginuser};"):
 sys.exit();
if(signupcheck=="{warning:newuser};"):
 post_data = urllib.urlencode({'username': myusername, 'userpass': mypasshash});
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
chatwin = curses.newwin(win_maxy - 3,  win_maxx, 0, 0);
chatwin.clear();
chatwin.keypad(0);
chatwin.scrollok(True);
inputwin = curses.newwin(win_maxy,  win_maxx, win_maxy - 3, 0);
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
 while threadloopstop==False:
  tinychattxt = login_opener.open(chaturl+"?act=view&room="+chatroomname);
  chattext = tinychattxt.readlines();
  chatsize = len(chattext);
  chati = 0;
  chatwin.refresh();
  while(chati<chatsize): 
   chatarray = re.findall("\{timestamp\:([0-9\.]+)\,userid\:([0-9]+)\,username\:\"(.*)\"\,message\:\"(.*)\"};", chattext[chati]);
   if(re.findall("\{timestamp\:([0-9\.]+)\,userid\:([0-9]+)\,username\:\"(.*)\"\,message\:\"(.*)\"};", chattext[chati])):
    chatarray = chatarray[0];
    curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
    chatwin.addstr(base64.b64decode(chatarray[2])+": ", curses.color_pair(2));
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
 mymessage = mymessage.strip();
 mymessagelc = mymessage.lower();
 if(mymessagelc!="quit" and mymessagelc!="exit" and mymessagelc!="" and mymessagelc!=None):
  curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK);
  chatwin.addstr(myusername+": ", curses.color_pair(2));
  curses.init_pair(3, curses.COLOR_WHITE, curses.COLOR_BLACK);
  chatwin.addstr(mymessage+"\n", curses.color_pair(3));
  chatwin.refresh();
  inputwin.refresh();
  post_data = urllib.urlencode({'message': mymessage});
  tinychating = login_opener.open(chaturl+"?act=message&room="+chatroomname, post_data);
threadloopstop=True;
gnm.cancel();
curses.endwin();
tinychating = login_opener.open(chaturl+"?act=logout");
if(sys.platform=="win32"):
 os.popen("cls");
if(sys.platform!="win32"):
 os.popen("stty sane");
 os.popen("clear");
 os.popen("reset");
sys.exit();
