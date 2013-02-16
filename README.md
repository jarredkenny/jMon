Server Monitor
==============

Server Monitor is a simple server monitors written in PHP and Bash. Server Monitor takes a different approch to server monitoring. Instead of querying a server's services from the monitor, the monitor simply parses data sent to it by a script installed on the server we are monitoring. This data contains server statistics such as uptime, disk usage, ram usage, load, and more. A threshold can be configured for each value to trigger an alert. If the monitor does not receive an update from the server in a given amount of time, you will then be alerted as well. 

![Server Monitor](http://i.imgur.com/r85nerU.png?1)

Monitor Server Requirements
-------------------
  - PHP 5+
  - MySQL


Monitored Server Requirements
-----------------------------
The server which is being monitored needs only a few basic tools that are availible by default on most Linux servers to run the monitoring script. 

  - uptime
  - df
  - free
  - ifconfig
  - curl

    
