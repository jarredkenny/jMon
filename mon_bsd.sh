#!/usr/local/bin/bash

# Gathers important server statistics and reports them a web interface using XML
# Author: Jarred Kenny (jarred@jr0d.com)


############## Settings ###############

#Primary network interface
iface="em0"

#Services that should be monitored (space seperated)
services=(sshd ircd httpd)

#URL to Monitor's listen.php
monitor="http://monitor.jr0d.com/listen.php"

#######################################

#Gather system information
OS=`uname -s`
HOSTNAME=`hostname -f`
LOAD=`uptime | grep -ohe 'load average[s:][: ].*' | awk '{ print $3 }'`
DISK_TOTAL=`df -hc | grep total | awk '{print $2}'`
DISK_USED=`df -hc | grep total | awk '{print $3}'`
DISK_FREE=`df -hc | grep total | awk '{print $4}'`
DISK_PERCENT_USED=`df -hc | grep total | awk '{print $5}'`
USERS=`uptime | grep -ohe '[0-9.*] user[s,]' | awk '{ print $1 }'`
UPTIME=$((`date +%s`-`sysctl kern.boottime | awk '{print $5}' | sed 's/,//'`))
RAM_FREE=`dmesg | grep 'avail memory' | awk '{print $5}' | sed 's/(//'`
RAM_TOTAL=`dmesg | grep 'real memory' | awk '{print $5}' | sed 's/(//'`
RX_BYTES=`netstat -I em0 -b | awk '{ if (/Link/) { print $8 } }'`
TX_BYTES=` netstat -I em0 -b | awk '{ if (/Link/) { print $11 } }'`
UPDATES_AVAIL="NA"

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
