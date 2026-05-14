import{ab as v,a3 as M,aJ as j,g as q,s as J,a as Y,b as Z,q as H,p as K,_ as d,l as _,c as Q,E as X,I as ee,N as te,e as ae,z as re,F as ne}from"./index.js";import{p as ie}from"./chunk-4BX2VUAB.js";import{p as se}from"./treemap-KZPCXAKY.js";import{d as L}from"./arc.js";import{o as le}from"./ordinal.js";import"./XCircleIcon.js";import"./_baseUniq.js";import"./_basePickBy.js";import"./clone.js";import"./init.js";(function(){try{var e=typeof window<"u"?window:typeof global<"u"?global:typeof globalThis<"u"?globalThis:typeof self<"u"?self:{};e.SENTRY_RELEASE={id:"294ba934db529ea05e88ea473943792c625c5022"}}catch{}})();try{(function(){var e=typeof window<"u"?window:typeof global<"u"?global:typeof globalThis<"u"?globalThis:typeof self<"u"?self:{},a=new e.Error().stack;a&&(e._sentryDebugIds=e._sentryDebugIds||{},e._sentryDebugIds[a]="960a7b39-0172-49ac-b006-766c2b1f126d",e._sentryDebugIdIdentifier="sentry-dbid-960a7b39-0172-49ac-b006-766c2b1f126d")})()}catch{}function oe(e,a){return a<e?-1:a>e?1:a>=e?0:NaN}function ce(e){return e}function ue(){var e=ce,a=oe,g=null,w=v(0),s=v(M),o=v(0);function l(t){var n,c=(t=j(t)).length,p,S,m=0,u=new Array(c),i=new Array(c),y=+w.apply(this,arguments),b=Math.min(M,Math.max(-M,s.apply(this,arguments)-y)),h,D=Math.min(Math.abs(b)/c,o.apply(this,arguments)),T=D*(b<0?-1:1),f;for(n=0;n<c;++n)(f=i[u[n]=n]=+e(t[n],n,t))>0&&(m+=f);for(a!=null?u.sort(function(x,A){return a(i[x],i[A])}):g!=null&&u.sort(function(x,A){return g(t[x],t[A])}),n=0,S=m?(b-c*T)/m:0;n<c;++n,y=h)p=u[n],f=i[p],h=y+(f>0?f*S:0)+T,i[p]={data:t[p],index:n,value:f,startAngle:y,endAngle:h,padAngle:D};return i}return l.value=function(t){return arguments.length?(e=typeof t=="function"?t:v(+t),l):e},l.sortValues=function(t){return arguments.length?(a=t,g=null,l):a},l.sort=function(t){return arguments.length?(g=t,a=null,l):g},l.startAngle=function(t){return arguments.length?(w=typeof t=="function"?t:v(+t),l):w},l.endAngle=function(t){return arguments.length?(s=typeof t=="function"?t:v(+t),l):s},l.padAngle=function(t){return arguments.length?(o=typeof t=="function"?t:v(+t),l):o},l}var de=ne.pie,z={sections:new Map,showData:!1},C=z.sections,F=z.showData,pe=structuredClone(de),fe=d(()=>structuredClone(pe),"getConfig"),ge=d(()=>{C=new Map,F=z.showData,re()},"clear"),he=d(({label:e,value:a})=>{if(a<0)throw new Error(`"${e}" has invalid value: ${a}. Negative values are not allowed in pie charts. All slice values must be >= 0.`);C.has(e)||(C.set(e,a),_.debug(`added new section: ${e}, with value: ${a}`))},"addSection"),me=d(()=>C,"getSections"),ye=d(e=>{F=e},"setShowData"),ve=d(()=>F,"getShowData"),O={getConfig:fe,clear:ge,setDiagramTitle:K,getDiagramTitle:H,setAccTitle:Z,getAccTitle:Y,setAccDescription:J,getAccDescription:q,addSection:he,getSections:me,setShowData:ye,getShowData:ve},we=d((e,a)=>{ie(e,a),a.setShowData(e.showData),e.sections.map(a.addSection)},"populateDb"),Se={parse:d(async e=>{const a=await se("pie",e);_.debug(a),we(a,O)},"parse")},be=d(e=>`
  .pieCircle{
    stroke: ${e.pieStrokeColor};
    stroke-width : ${e.pieStrokeWidth};
    opacity : ${e.pieOpacity};
  }
  .pieOuterCircle{
    stroke: ${e.pieOuterStrokeColor};
    stroke-width: ${e.pieOuterStrokeWidth};
    fill: none;
  }
  .pieTitleText {
    text-anchor: middle;
    font-size: ${e.pieTitleTextSize};
    fill: ${e.pieTitleTextColor};
    font-family: ${e.fontFamily};
  }
  .slice {
    font-family: ${e.fontFamily};
    fill: ${e.pieSectionTextColor};
    font-size:${e.pieSectionTextSize};
    // fill: white;
  }
  .legend text {
    fill: ${e.pieLegendTextColor};
    font-family: ${e.fontFamily};
    font-size: ${e.pieLegendTextSize};
  }
`,"getStyles"),xe=be,Ae=d(e=>{const a=[...e.values()].reduce((s,o)=>s+o,0),g=[...e.entries()].map(([s,o])=>({label:s,value:o})).filter(s=>s.value/a*100>=1).sort((s,o)=>o.value-s.value);return ue().value(s=>s.value)(g)},"createPieArcs"),De=d((e,a,g,w)=>{_.debug(`rendering pie chart
`+e);const s=w.db,o=Q(),l=X(s.getConfig(),o.pie),t=40,n=18,c=4,p=450,S=p,m=ee(a),u=m.append("g");u.attr("transform","translate("+S/2+","+p/2+")");const{themeVariables:i}=o;let[y]=te(i.pieOuterStrokeWidth);y??(y=2);const b=l.textPosition,h=Math.min(S,p)/2-t,D=L().innerRadius(0).outerRadius(h),T=L().innerRadius(h*b).outerRadius(h*b);u.append("circle").attr("cx",0).attr("cy",0).attr("r",h+y/2).attr("class","pieOuterCircle");const f=s.getSections(),x=Ae(f),A=[i.pie1,i.pie2,i.pie3,i.pie4,i.pie5,i.pie6,i.pie7,i.pie8,i.pie9,i.pie10,i.pie11,i.pie12];let $=0;f.forEach(r=>{$+=r});const N=x.filter(r=>(r.data.value/$*100).toFixed(0)!=="0"),E=le(A);u.selectAll("mySlices").data(N).enter().append("path").attr("d",D).attr("fill",r=>E(r.data.label)).attr("class","pieCircle"),u.selectAll("mySlices").data(N).enter().append("text").text(r=>(r.data.value/$*100).toFixed(0)+"%").attr("transform",r=>"translate("+T.centroid(r)+")").style("text-anchor","middle").attr("class","slice"),u.append("text").text(s.getDiagramTitle()).attr("x",0).attr("y",-400/2).attr("class","pieTitleText");const R=[...f.entries()].map(([r,I])=>({label:r,value:I})),k=u.selectAll(".legend").data(R).enter().append("g").attr("class","legend").attr("transform",(r,I)=>{const W=n+c,B=W*R.length/2,V=12*n,U=I*W-B;return"translate("+V+","+U+")"});k.append("rect").attr("width",n).attr("height",n).style("fill",r=>E(r.label)).style("stroke",r=>E(r.label)),k.append("text").attr("x",n+c).attr("y",n-c).text(r=>s.getShowData()?`${r.label} [${r.value}]`:r.label);const P=Math.max(...k.selectAll("text").nodes().map(r=>(r==null?void 0:r.getBoundingClientRect().width)??0)),G=S+t+n+c+P;m.attr("viewBox",`0 0 ${G} ${p}`),ae(m,p,G,l.useMaxWidth)},"draw"),Te={draw:De},Re={parser:Se,db:O,renderer:Te,styles:xe};export{Re as diagram};
//# sourceMappingURL=pieDiagram-SKSYHLDU.js.map
