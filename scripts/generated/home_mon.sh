#!/bin/bash

# Gathers important server statistics and reports them a web interface using XML
# Author: Jarred Kenny (jarred@jr0d.com)


# This version of the script is tuned for Debian based systems.
# If you are not monitoring a Debian system, use the monitor to generate a script for your distrobution.

############## Settings ###############

#Primary network interface
iface="eth0"

#Services that should be monitored (space seperated)
services=(apache2 named sshd downservice anotherone)

#URL to Monitor's listen.php
monitor="http://monitor.jr0d.com/listen.php"

########################################

#Gather system information
OS=`uname -s`
HOSTNAME=`hostname -f`
LOAD="3.0"
#LOAD=`uptime | grep -ohe 'load average[s:][: ].*' | awk '{ print $3 }' | sed 's/,//'`
DISK_TOTAL=`df -h --total | grep total | awk '{print $2}'`
DISK_USED=`df -h --total | grep total | awk '{print $3}'`
DISK_FREE=`df -h --total | grep total | awk '{print $4}'`
#DISK_PERCENT_USED=`df -h --total | grep total | awk '{print $5}'`
DISK_PERCENT_USED="100%"
USERS=`uptime | grep -ohe '[0-9.*] user[s,]' | awk '{ print $1 }'`
UPTIME=`cat /proc/uptime | awk '{print $1}'`
RAM_FREE=`free -m | grep -v shared | awk '/buffers/ {printf $4 }'`
RAM_TOTAL=`free -m | grep -v shared | awk '/Mem/ {printf $2 }'`
RX_BYTES=`/sbin/ifconfig $iface | awk '{ gsub(/\:/," ") } ; { print  } ' | awk '/RX\ b/ { print $3 }'`
TX_BYTES=`/sbin/ifconfig $iface | awk '{ gsub(/\:/," ") } ; { print  } ' | awk '/RX\ b/ { print $8 }'`
UPDATES_AVAIL=`apt-get -s upgrade | awk '/[0-9]+ upgraded,/ {print $1}'`

#Create temporary XML file to write data to
XML_FILE=$$.xml

#Output values in fancy XML
echo -n "<Monitor>" >> $XML_FILE
echo -n "<Access_Key>$accesskey</Access_Key>" >> $XML_FILE
echo -n "<OS>$OS</OS>" >> $XML_FILE
echo -n "<Hostname>$HOSTNAME</Hostname>" >> $XML_FILE
echo -n "<Load>$LOAD</Load>" >> $XML_FILE
echo -n "<Disk_Total>$DISK_TOTAL</Disk_Total>" >> $XML_FILE
echo -n "<Disk_Used>$DISK_USED</Disk_Used>" >> $XML_FILE
echo -n "<Disk_Free>$DISK_FREE</Disk_Free>" >> $XML_FILE
echo -n "<Disk_Percent_Used>$DISK_PERCENT_USED</Disk_Percent_Used>" >> $XML_FILE
echo -n "<Users>$USERS</Users>" >> $XML_FILE
echo -n "<Uptime>$UPTIME</Uptime>" >> $XML_FILE
echo -n "<Ram_Free>$RAM_FREE</Ram_Free>" >> $XML_FILE
echo -n "<Ram_Total>$RAM_TOTAL</Ram_Total>" >> $XML_FILE
echo -n "<Rx_Bytes>$RX_BYTES</Rx_Bytes>" >> $XML_FILE
echo -n "<Tx_Bytes>$TX_BYTES</Tx_Bytes>" >> $XML_FILE
echo -n "<Updates_Avail>$UPDATES_AVAIL</Updates_Avail>" >> $XML_FILE

#Check on services defined in settings above and output in XML
echo -n "<Services>" >> $XML_FILE
for service in ${services[*]}
do
	echo -n "<Service>" >> $XML_FILE
	echo -n "<Name>$service</Name>" >> $XML_FILE
	echo -n "<Status>" >> $XML_FILE
	if ps ax | grep -v grep | grep $service > /dev/null; then echo -n "UP" >> $XML_FILE; else echo -n "DOWN" >> $XML_FILE; fi
	echo -n "</Status>" >> $XML_FILE
	echo -n "</Service>" >> $XML_FILE
done
echo -n "</Services>" >> $XML_FILE
echo -n "</Monitor>" >> $XML_FILE

#Post data to listener
curl --request POST $monitor --data "XML_DATA=`cat $XML_FILE`"

#Remove XML file
rm $XML_FILE
