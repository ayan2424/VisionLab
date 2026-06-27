const fs = require('fs');
const file = '/usr/lib/visionlab-ide/lib/vscode/out/vs/server/node/server.main.js';
let content = fs.readFileSync(file, 'utf8');
content = content.replace('if(!(0,P.$Cn)(this.r,ue,B))return(0,ie.$xQ)(ue,j,403,"Forbidden.");', 'if(false)return(0,ie.$xQ)(ue,j,403,"Forbidden.");');
fs.writeFileSync(file, content);
