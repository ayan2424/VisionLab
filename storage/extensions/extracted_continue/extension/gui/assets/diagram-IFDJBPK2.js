import{s as E,g as _,q as O,p as R,a as k,b as D,_ as l,I as F,z,E as b,F as G,G as $,l as P,K as W,e as B}from"./index.js";import{p as V}from"./chunk-4BX2VUAB.js";import{p as H}from"./treemap-KZPCXAKY.js";import"./XCircleIcon.js";import"./_baseUniq.js";import"./_basePickBy.js";import"./clone.js";(function(){try{var t=typeof window<"u"?window:typeof global<"u"?global:typeof globalThis<"u"?globalThis:typeof self<"u"?self:{};t.SENTRY_RELEASE={id:"294ba934db529ea05e88ea473943792c625c5022"}}catch{}})();try{(function(){var t=typeof window<"u"?window:typeof global<"u"?global:typeof globalThis<"u"?globalThis:typeof self<"u"?self:{},e=new t.Error().stack;e&&(t._sentryDebugIds=t._sentryDebugIds||{},t._sentryDebugIds[e]="48389fd1-3db4-42aa-a274-3cfdcbc5eac9",t._sentryDebugIdIdentifier="sentry-dbid-48389fd1-3db4-42aa-a274-3cfdcbc5eac9")})()}catch{}var m={showLegend:!0,ticks:5,max:null,min:0,graticule:"circle"},w={axes:[],curves:[],options:m},h=structuredClone(w),j=G.radar,N=l(()=>b({...j,...$().radar}),"getConfig"),C=l(()=>h.axes,"getAxes"),Y=l(()=>h.curves,"getCurves"),q=l(()=>h.options,"getOptions"),K=l(t=>{h.axes=t.map(e=>({name:e.name,label:e.label??e.name}))},"setAxes"),U=l(t=>{h.curves=t.map(e=>({name:e.name,label:e.label??e.name,entries:X(e.entries)}))},"setCurves"),X=l(t=>{if(t[0].axis==null)return t.map(a=>a.value);const e=C();if(e.length===0)throw new Error("Axes must be populated before curves for reference entries");return e.map(a=>{const r=t.find(n=>{var o;return((o=n.axis)==null?void 0:o.$refText)===a.name});if(r===void 0)throw new Error("Missing entry for axis "+a.label);return r.value})},"computeCurveEntries"),Z=l(t=>{var a,r,n,o,i;const e=t.reduce((s,c)=>(s[c.name]=c,s),{});h.options={showLegend:((a=e.showLegend)==null?void 0:a.value)??m.showLegend,ticks:((r=e.ticks)==null?void 0:r.value)??m.ticks,max:((n=e.max)==null?void 0:n.value)??m.max,min:((o=e.min)==null?void 0:o.value)??m.min,graticule:((i=e.graticule)==null?void 0:i.value)??m.graticule}},"setOptions"),J=l(()=>{z(),h=structuredClone(w)},"clear"),v={getAxes:C,getCurves:Y,getOptions:q,setAxes:K,setCurves:U,setOptions:Z,getConfig:N,clear:J,setAccTitle:D,getAccTitle:k,setDiagramTitle:R,getDiagramTitle:O,getAccDescription:_,setAccDescription:E},Q=l(t=>{V(t,v);const{axes:e,curves:a,options:r}=t;v.setAxes(e),v.setCurves(a),v.setOptions(r)},"populate"),ee={parse:l(async t=>{const e=await H("radar",t);P.debug(e),Q(e)},"parse")},te=l((t,e,a,r)=>{const n=r.db,o=n.getAxes(),i=n.getCurves(),s=n.getOptions(),c=n.getConfig(),d=n.getDiagramTitle(),p=F(e),u=ae(p,c),g=s.max??Math.max(...i.map(y=>Math.max(...y.entries))),x=s.min,f=Math.min(c.width,c.height)/2;re(u,o,f,s.ticks,s.graticule),ne(u,o,f,c),M(u,o,i,x,g,s.graticule,c),L(u,i,s.showLegend,c),u.append("text").attr("class","radarTitle").text(d).attr("x",0).attr("y",-c.height/2-c.marginTop)},"draw"),ae=l((t,e)=>{const a=e.width+e.marginLeft+e.marginRight,r=e.height+e.marginTop+e.marginBottom,n={x:e.marginLeft+e.width/2,y:e.marginTop+e.height/2};return B(t,r,a,e.useMaxWidth??!0),t.attr("viewBox",`0 0 ${a} ${r}`),t.append("g").attr("transform",`translate(${n.x}, ${n.y})`)},"drawFrame"),re=l((t,e,a,r,n)=>{if(n==="circle")for(let o=0;o<r;o++){const i=a*(o+1)/r;t.append("circle").attr("r",i).attr("class","radarGraticule")}else if(n==="polygon"){const o=e.length;for(let i=0;i<r;i++){const s=a*(i+1)/r,c=e.map((d,p)=>{const u=2*p*Math.PI/o-Math.PI/2,g=s*Math.cos(u),x=s*Math.sin(u);return`${g},${x}`}).join(" ");t.append("polygon").attr("points",c).attr("class","radarGraticule")}}},"drawGraticule"),ne=l((t,e,a,r)=>{const n=e.length;for(let o=0;o<n;o++){const i=e[o].label,s=2*o*Math.PI/n-Math.PI/2;t.append("line").attr("x1",0).attr("y1",0).attr("x2",a*r.axisScaleFactor*Math.cos(s)).attr("y2",a*r.axisScaleFactor*Math.sin(s)).attr("class","radarAxisLine"),t.append("text").text(i).attr("x",a*r.axisLabelFactor*Math.cos(s)).attr("y",a*r.axisLabelFactor*Math.sin(s)).attr("class","radarAxisLabel")}},"drawAxes");function M(t,e,a,r,n,o,i){const s=e.length,c=Math.min(i.width,i.height)/2;a.forEach((d,p)=>{if(d.entries.length!==s)return;const u=d.entries.map((g,x)=>{const f=2*Math.PI*x/s-Math.PI/2,y=A(g,r,n,c),S=y*Math.cos(f),I=y*Math.sin(f);return{x:S,y:I}});o==="circle"?t.append("path").attr("d",T(u,i.curveTension)).attr("class",`radarCurve-${p}`):o==="polygon"&&t.append("polygon").attr("points",u.map(g=>`${g.x},${g.y}`).join(" ")).attr("class",`radarCurve-${p}`)})}l(M,"drawCurves");function A(t,e,a,r){const n=Math.min(Math.max(t,e),a);return r*(n-e)/(a-e)}l(A,"relativeRadius");function T(t,e){const a=t.length;let r=`M${t[0].x},${t[0].y}`;for(let n=0;n<a;n++){const o=t[(n-1+a)%a],i=t[n],s=t[(n+1)%a],c=t[(n+2)%a],d={x:i.x+(s.x-o.x)*e,y:i.y+(s.y-o.y)*e},p={x:s.x-(c.x-i.x)*e,y:s.y-(c.y-i.y)*e};r+=` C${d.x},${d.y} ${p.x},${p.y} ${s.x},${s.y}`}return`${r} Z`}l(T,"closedRoundCurve");function L(t,e,a,r){if(!a)return;const n=(r.width/2+r.marginRight)*3/4,o=-(r.height/2+r.marginTop)*3/4,i=20;e.forEach((s,c)=>{const d=t.append("g").attr("transform",`translate(${n}, ${o+c*i})`);d.append("rect").attr("width",12).attr("height",12).attr("class",`radarLegendBox-${c}`),d.append("text").attr("x",16).attr("y",0).attr("class","radarLegendText").text(s.label)})}l(L,"drawLegend");var se={draw:te},oe=l((t,e)=>{let a="";for(let r=0;r<t.THEME_COLOR_LIMIT;r++){const n=t[`cScale${r}`];a+=`
		.radarCurve-${r} {
			color: ${n};
			fill: ${n};
			fill-opacity: ${e.curveOpacity};
			stroke: ${n};
			stroke-width: ${e.curveStrokeWidth};
		}
		.radarLegendBox-${r} {
			fill: ${n};
			fill-opacity: ${e.curveOpacity};
			stroke: ${n};
		}
		`}return a},"genIndexStyles"),ie=l(t=>{const e=W(),a=$(),r=b(e,a.themeVariables),n=b(r.radar,t);return{themeVariables:r,radarOptions:n}},"buildRadarStyleOptions"),le=l(({radar:t}={})=>{const{themeVariables:e,radarOptions:a}=ie(t);return`
	.radarTitle {
		font-size: ${e.fontSize};
		color: ${e.titleColor};
		dominant-baseline: hanging;
		text-anchor: middle;
	}
	.radarAxisLine {
		stroke: ${a.axisColor};
		stroke-width: ${a.axisStrokeWidth};
	}
	.radarAxisLabel {
		dominant-baseline: middle;
		text-anchor: middle;
		font-size: ${a.axisLabelFontSize}px;
		color: ${a.axisColor};
	}
	.radarGraticule {
		fill: ${a.graticuleColor};
		fill-opacity: ${a.graticuleOpacity};
		stroke: ${a.graticuleColor};
		stroke-width: ${a.graticuleStrokeWidth};
	}
	.radarLegendText {
		text-anchor: start;
		font-size: ${a.legendFontSize}px;
		dominant-baseline: hanging;
	}
	${oe(e,a)}
	`},"styles"),xe={parser:ee,db:v,renderer:se,styles:le};export{xe as diagram};
//# sourceMappingURL=diagram-IFDJBPK2.js.map
