#!/bin/bash
CONF_FILE="/www/server/panel/vhost/nginx/15.207.144.48.nip.io.conf"

sudo sed -i '/#PROXY-CONF-START/,/#PROXY-CONF-END/d' $CONF_FILE
sudo sed -i '/EOF/d' $CONF_FILE

cat << 'INNER_EOF' > /tmp/nginx_proxy.txt
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
INNER_EOF

sudo sed -i '/#WEBSOCKET-SUPPORT END/r /tmp/nginx_proxy.txt' $CONF_FILE
sudo nginx -t && sudo systemctl reload nginx
