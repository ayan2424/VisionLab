#!/bin/bash
set -e

sudo su -c '
set -e

CONF_FILE="/www/server/panel/vhost/nginx/15.207.144.48.nip.io.conf"

# Remove everything after WEBSOCKET-SUPPORT END to clean up any garbage
sed -i "/#WEBSOCKET-SUPPORT END/,$d" $CONF_FILE

# Append the correct proxy block and the rest of the file
cat << "EOF" >> $CONF_FILE
    #WEBSOCKET-SUPPORT END

    #PROXY-CONF-START
    location ^~ /stable- {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection upgrade;
        proxy_set_header Accept-Encoding gzip;
    }

    location ^~ /webview/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection upgrade;
        proxy_set_header Accept-Encoding gzip;
    }
    
    location ^~ / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-Port $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header REMOTE-HOST $remote_addr;
        
        proxy_connect_timeout 60s;
        proxy_send_timeout 600s;
        proxy_read_timeout 600s;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
    #PROXY-CONF-END

    #SERVER-BLOCK START
    #SERVER-BLOCK END

    #Prohibited access to files or directories
    location ~ ^/(\.user.ini|\.htaccess|\.git|\.env|\.svn|\.project|LICENSE|README.md)
    {
        return 404;
    }

    #One click application for SSL certificate verification directory related settings
    location /.well-known{
        allow all;
        root /www/wwwroot/15.207.144.48.nip.io;
    }

    #Prohibit placing sensitive files in the certificate verification directory
    if ( $uri ~ "^/\.well-known/.*\.(php|jsp|py|js|css|lua|ts|go|zip|tar\.gz|rar|7z|sql|bak)$" ) {
        return 403;
    }

    #LOG START
    access_log  /www/wwwlogs/15.207.144.48.nip.io.log;
    error_log  /www/wwwlogs/15.207.144.48.nip.io.error.log;
    #LOG END
}
EOF

nginx -t && systemctl reload nginx
'
