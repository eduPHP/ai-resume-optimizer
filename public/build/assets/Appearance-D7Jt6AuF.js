import{d as u,A as y,c as i,o as t,F as g,i as x,n as m,b as n,j as h,l as b,t as f,u as r,w as d,a as e,m as v}from"./app-B6d0tt0q.js";import{c}from"./Button.vue_vue_type_script_setup_true_lang-CJS8Fvkc.js";import{_ as M,a as w}from"./Layout.vue_vue_type_script_setup_true_lang-C1uzcYY8.js";import{_ as A}from"./AppLayout.vue_vue_type_script_setup_true_lang-CBw-3bSA.js";import"./Label.vue_vue_type_script_setup_true_lang-bNMI4UD1.js";import"./AppLogoIcon.vue_vue_type_script_setup_true_lang-DKJQt26z.js";/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const I=c("MonitorIcon",[["rect",{width:"20",height:"14",x:"2",y:"3",rx:"2",key:"48i651"}],["line",{x1:"8",x2:"16",y1:"21",y2:"21",key:"1svkeh"}],["line",{x1:"12",x2:"12",y1:"17",y2:"21",key:"vw1qmm"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=c("MoonIcon",[["path",{d:"M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z",key:"a7tn18"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $=c("SunIcon",[["circle",{cx:"12",cy:"12",r:"4",key:"4exip2"}],["path",{d:"M12 2v2",key:"tus03m"}],["path",{d:"M12 20v2",key:"1lh1kg"}],["path",{d:"m4.93 4.93 1.41 1.41",key:"149t6j"}],["path",{d:"m17.66 17.66 1.41 1.41",key:"ptbguv"}],["path",{d:"M2 12h2",key:"1t8f8n"}],["path",{d:"M20 12h2",key:"1q8mjw"}],["path",{d:"m6.34 17.66-1.41 1.41",key:"1m8zz5"}],["path",{d:"m19.07 4.93-1.41 1.41",key:"1shlcs"}]]),B=["onClick"],S={class:"ml-1.5 text-sm"},j=u({__name:"AppearanceTabs",props:{class:{default:""}},setup(o){const{appearance:a,updateAppearance:l}=y(),p=[{value:"light",Icon:$,label:"Light"},{value:"dark",Icon:C,label:"Dark"},{value:"system",Icon:I,label:"System"}];return(D,L)=>(t(),i("div",{class:m(["inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800",o.class])},[(t(),i(g,null,x(p,({value:s,Icon:_,label:k})=>n("button",{key:s,onClick:q=>r(l)(s),class:m(["flex items-center rounded-md px-3.5 py-1.5 transition-colors",r(a)===s?"bg-white shadow-sm dark:bg-neutral-700 dark:text-neutral-100":"text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60"])},[(t(),h(b(_),{class:"-ml-1 h-4 w-4"})),n("span",S,f(k),1)],10,B)),64))],2))}}),z={class:"space-y-6"},Z=u({__name:"Appearance",setup(o){const a=[{title:"Appearance settings",href:"/settings/appearance"}];return(l,p)=>(t(),h(A,{breadcrumbs:a},{default:d(()=>[e(r(v),{title:"Appearance settings"}),e(M,null,{default:d(()=>[n("div",z,[e(w,{title:"Appearance settings",description:"Update your account's appearance settings"}),e(j)])]),_:1})]),_:1}))}});export{Z as default};
