!function(e,i){"object"==typeof exports&&"undefined"!=typeof module?i(exports):"function"==typeof define&&define.amd?define(["exports"],i):i((e=e||self).window=e.window||{})}(this,function(e){"use strict";function g(){return i||"undefined"!=typeof window&&(i=window.gsap)&&i.registerPlugin&&i}function j(e,i,t){t=!!t,e.visible!==t&&(e.visible=t,e.traverse(function(e){return e.visible=t}))}function k(e){return("string"==typeof e&&"="===e.charAt(1)?e.substr(0,2)+parseFloat(e.substr(2)):e)*t}function l(e){(i=e||g())&&(d=i.core.PropTween,f=1)}var i,f,d,u={x:"position",y:"position",z:"position"},t=Math.PI/180;"position,scale,rotation".split(",").forEach(function(e){return u[e+"X"]=u[e+"Y"]=u[e+"Z"]=e});var n={version:"3.0.0",name:"three",register:l,init:function init(e,i){var t,n,o,r,s,a;for(r in f||l(),i){if(t=u[r],o=i[r],t)n=~(s=r.charAt(r.length-1).toLowerCase()).indexOf("x")?"x":~s.indexOf("z")?"z":"y",this.add(e[t],n,e[t][n],~r.indexOf("rotation")?k(o):o);else if("scale"===r)this.add(e[r],"x",e[r].x,o),this.add(e[r],"y",e[r].y,o),this.add(e[r],"z",e[r].z,o);else if("opacity"===r)for(s=(a=e.material.length?e.material:[e.material]).length;-1<--s;)a[s].transparent=!0,this.add(a[s],r,a[s][r],o);else"visible"===r?e.visible!==o&&(this._pt=new d(this._pt,e,r,o?0:1,o?1:-1,0,0,j)):this.add(e,r,e[r],o);this._props.push(r)}}};g()&&i.registerPlugin(n),e.ThreePlugin=n,e.default=n;if (typeof(window)==="undefined"||window!==e){Object.defineProperty(e,"__esModule",{value:!0})} else {delete e.default}});