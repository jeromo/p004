#!/bin/bash
 
# Author: Jorge Tomé Hernando <jorge@jorgetome.info>
# Creation date: November 2011
# Version: 1.1
#
# Description
# -----------
# This scripts creates a new Trac instance
#
# It creates the required MySQL database, the new Trac instance 
# a configuration file for the Apache2 web server.
#
# It also restart the Apache2 server in order to apply the new
# configuration.
#
# It has been developed and tested in an Ubuntu 11.04 environment.
#
# Modification date: 8th January 2013
# Modification desc: Modified to use LDAP authentication
 
usage()
{
    cat<<EOF
usage:$0 options
 
This script creates a new Trac instance adjusted to our
personalized configuration.
 
Options:
-h Shows this message
-p Name of the project
EOF
}
 
if [[ $EUID -ne 0 ]]; then
    echo "This script must be run as root" 1>&2
    exit 1
fi
 
PROJECT_NAME=
 
while getopts ":hp:" opt; do
    case $opt in
        h)
            usage
            exit 1
            ;;
        p)
            PROJECT_NAME=$OPTARG
            ;;
        ?)
            usage
            exit
        ;;
    esac
done
 
if [ -z $PROJECT_NAME ]
then
    usage
    exit 1
fi
 
# Configuration variables
RESOURCES_DIR=/home/jrojo/resources
TRAC_HOME=/srv/trac
DB_PREFIX=trac_
DB_USR=tracusr
DB_PWD=tracpwd
DB_HOST=localhost
DB_PORT=3306
TRAC_INI_TEMPLATE=trac.ini.template
TRAC_LOGO=PrisaDigital.jpg
TRAC_SITE_TEMPLATE=site.html.template
APACHE_USR=www-data
APACHE_CONF_DIR=/etc/apache2/projects.d
 
# Utility variables
PROJECT_DIR=`echo ${PROJECT_NAME,,}`
DB_NAME=${DB_PREFIX}${PROJECT_DIR}

# '.' in dbname hurts mysql. Get it out
DB_NAME=$(echo $DB_NAME |sed "s/\./_/g");
TRAC_DIR=${TRAC_HOME}/${PROJECT_DIR}
 
# First we have to create the MySQL database to support this new Trad instance
mysql -u root -pjrm1jrm <<QUERY_INPUT
CREATE DATABASE ${DB_NAME};
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO ${DB_USR}@${DB_HOST} IDENTIFIED BY '${DB_PWD}';
QUERY_INPUT
 
# Second we have to create the Trac instance...
trac-admin ${TRAC_DIR} initenv ${PROJECT_NAME} mysql://${DB_USR}:${DB_PWD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
#trac-admin ${TRAC_DIR} repository add ${PROJECT_DIR} ${SVN_DIR} svn
#trac-admin ${TRAC_DIR} repository resync ${PROJECT_DIR}
#trac-admin ${TRAC_DIR} permission add ${PROJECT_ADMIN} TRAC_ADMIN
trac-admin ${TRAC_DIR} permission add g_analyst TICKET_CREATE TICKET_MODIFY WIKI_CREATE WIKI_MODIFY
trac-admin ${TRAC_DIR} permission add g_developer TICKET_CREATE TICKET_MODIFY WIKI_CREATE WIKI_MODIFY
trac-admin ${TRAC_DIR} permission add g_manager MILESTONE_ADMIN TICKET_ADMIN WIKI_ADMIN
trac-admin ${TRAC_DIR} permission add g_productmanager TICKET_CREATE TICKET_MODIFY
trac-admin ${TRAC_DIR} permission add g_sqc TRAC_ADMIN
trac-admin ${TRAC_DIR} permission add jtome g_sqc
trac-admin ${TRAC_DIR} permission add jrojo g_sqc


# ...and adjust some parameters and configurations.
sed "s/##Projectname##/${PROJECT_NAME}/g" ${RESOURCES_DIR}/${TRAC_INI_TEMPLATE} >${TRAC_DIR}/conf/trac.tmp
sed "s/##projectname##/${PROJECT_DIR}/g" ${TRAC_DIR}/conf/trac.tmp  >${TRAC_DIR}/conf/trac.tmp2
sed "s/##database##/${DB_NAME}/g" ${TRAC_DIR}/conf/trac.tmp2  >${TRAC_DIR}/conf/trac.ini
rm -f ${TRAC_DIR}/conf/trac.tmp
rm -f ${TRAC_DIR}/conf/trac.tmp2



cp ${RESOURCES_DIR}/${TRAC_LOGO} ${TRAC_DIR}/htdocs/.
mkdir -p ${TRAC_DIR}/templates
cp ${RESOURCES_DIR}/${TRAC_SITE_TEMPLATE} ${TRAC_DIR}/templates/site.html

trac-admin ${TRAC_DIR} deploy ${TRAC_DIR}/deploy
trac-admin ${TRAC_DIR} upgrade
trac-admin ${TRAC_DIR} wiki upgrade

exit 
# Third we have to create the Apache2 configuration file
cat > ${APACHE_CONF_DIR}/${PROJECT_DIR}.conf <<EOF
WSGIScriptAlias /trac/${PROJECT_DIR} ${TRAC_DIR}/deploy/cgi-bin/trac.wsgi
 
<Directory ${TRAC_DIR}/deploy/cgi-bin>
    WSGIApplicationGroup %{GLOBAL}
    Order deny,allow
    Allow from all
</Directory>

<Location "/trac/${PROJECT_DIR}/login">
   AuthType Basic
   AuthName "Trac"
   AuthBasicProvider ldap
   AuthLDAPURL "ldap://sdc3w3k:3268/DC=prisacom,DC=int?sAMAccountName?sub?(objectClass=user)"
   AuthzLDAPAuthoritative Off
   require valid-user
   AuthLDAPBindDN LDapUser@prisacom.int
   AuthLDAPBindPassword k3alpc-
</Location>
EOF
 
# Last we have to adjust the permissions on the directories and
# restart the web server
chown -R ${APACHE_USR}:${APACHE_USR} ${TRAC_DIR}
apache2ctl restart
