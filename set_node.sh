#!/bin/bash
sudo ln -sf /www/server/nodejs/v20.20.2/bin/node /usr/local/bin/node
sudo ln -sf /www/server/nodejs/v20.20.2/bin/npm /usr/local/bin/npm
sudo ln -sf /www/server/nodejs/v20.20.2/bin/npx /usr/local/bin/npx
sudo ln -sf /www/server/nodejs/v20.20.2/bin/yarn /usr/local/bin/yarn || true

# Just in case nvm is used by ubuntu user
su - ubuntu -c '
if [ -s "$HOME/.nvm/nvm.sh" ]; then
    source "$HOME/.nvm/nvm.sh"
    nvm alias default v20.20.2 || true
fi
'

# Just in case nvm is used by root user
sudo su -c '
if [ -s "$HOME/.nvm/nvm.sh" ]; then
    source "$HOME/.nvm/nvm.sh"
    nvm alias default v20.20.2 || true
fi
'
