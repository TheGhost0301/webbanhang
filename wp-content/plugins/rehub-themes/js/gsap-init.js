jQuery(document).ready(function($) {
   'use strict';	
    //GSAP 
    if($('.rh-gsap-wrap').length > 0){
        var rhscroller = new ScrollMagic.Controller();
        $('.rh-gsap-wrap').each(function(){

            var scrollargs = {};
            var anargs = {};
            var current = $(this);
            if(current.hasClass('prehidden')){
                current.removeClass('prehidden');
            }

            var $duration = current.data('duration');
            var $duration = parseFloat($duration);
            anargs.duration = $duration;

            if(current.data('x')){
                anargs.x = current.data('x');
            }

            if(current.data('y')){
                anargs.y = current.data('y');
            }

            if(current.data('xo')){
                anargs.xPercent = current.data('xo');
            }

            if(current.data('yo')){
                anargs.yPercent = current.data('yo');
            }

            if(current.data('width')){
                anargs.width = current.data('width');
            }

            if(current.data('height')){
                anargs.height = current.data('height');
            }

            if(current.data('r')){
                anargs.rotation = current.data('r');
            }

            if(current.data('rx')){
                anargs.rotationX = current.data('rx');
            }

            if(current.data('ry')){
                anargs.rotationY = current.data('ry');
            }

            if(current.data('s')){
                anargs.scale = current.data('s');
            }

            if(current.data('sx')){
                anargs.scaleX = current.data('sx');
            }

            if(current.data('sy')){
                anargs.scaleY = current.data('sy');
            }

            if(current.data('o')){
                anargs.opacity = parseInt(current.data('o'))/100;
            }
            if(current.data('bg')){
                anargs.backgroundColor = current.data('bg');
            }
            if(current.data('origin')){
                anargs.transformOrigin = current.data('origin');
            }

            if(current.data('path')){
                anargs.motionPath = {
                    path: current.data('path'),
                    immediateRender: true
                }
                if(current.data('path-align')){
                    anargs.motionPath.align = current.data('path-align');
                    anargs.motionPath.alignOrigin = [];
                    if(current.data('path-alignx') !== null && current.data('path-alignx') !== undefined){
                        anargs.motionPath.alignOrigin[0] = parseFloat(current.data('path-alignx'));
                    }else{
                        anargs.motionPath.alignOrigin[0] = 0.5;
                    }
                    if(current.data('path-aligny') !== null && current.data('path-aligny') !== undefined){
                        anargs.motionPath.alignOrigin[1] = parseFloat(current.data('path-aligny'));
                    }else{
                        anargs.motionPath.alignOrigin[1] = 0.5;
                    }
                }
                if(current.data('path-orient')){
                    anargs.motionPath.autoRotate = true;
                }

            }

            if(current.data('ease')){
                var $ease = current.data('ease').split('-');
                anargs.ease = $ease[0]+'.'+$ease[1];
                if(anargs.ease === 'power0.none'){           
                    anargs.ease = 'none';
                }
            }

            if(current.data('stagger')){
                var stagerobj = current.data('stagger');
                if(stagerobj.indexOf(".") == 0 || stagerobj.indexOf("#") == 0){
                    var $anobj = $(stagerobj);
                }else{
                    var $anobj = $('.'+stagerobj);
                }
            }else if(current.data('text')){
                var $texttype = current.data('text');
                var splittextobj = current.children();
                var split = new SplitText(splittextobj, {type: $texttype});
                if($texttype == 'chars'){
                    var $anobj = split.chars;
                }else if($texttype == 'words'){
                    var $anobj = split.words;
                }else{
                    var $anobj = split.lines;
                }
            }else if(current.data('svgdraw')){
                var svgarray = [];
                var shapes = ['path', 'line', 'polyline', 'polygon', 'rect', 'ellipse', 'circle'];
                for (var shape in shapes){
                    if($(this).find(shapes[shape]).length > 0){
                        svgarray.push($(this).find(shapes[shape]));
                    }
                }
                $anobj = svgarray;
                if(current.data('from')=='yes'){
                    anargs.drawSVG = "0%";
                }else{
                    anargs.drawSVG = "100%";
                }
                if(current.data('bg')){
                    anargs.stroke = current.data('bg');
                }
                
            }else if(current.data('video')){
                if($(this).find('video').length > 0){
                    var $videoobj = $(this).find('video');
                    $videoobj[0].pause();
                    anargs.currentTime = current.data('video');
                    $anobj = $videoobj;

                }else{
                    $anobj = current;
                }
            }
            else{
                $anobj = current;
            }

            if(current.data('stagger') || current.data('text') || current.data('svgdraw')){
                anargs.stagger = {};
                if(current.data('stdelay')){
                    anargs.stagger.each = current.data('stdelay');
                }else{
                    anargs.stagger.each = 0.2;
                }
                if(current.data('strandom') == 'yes'){
                    anargs.stagger.from = "random";
                }
            }
            var animation = gsap.timeline();
            if(current.data('from')=='yes'){
                //var animation = gsap.from($anobj, anargs);
                animation.from($anobj, anargs);
                
            }else{
                animation.to($anobj, anargs);
                //var animation = gsap.to($anobj, anargs);
            }
            if(current.data('delay')){
                animation.delay(current.data('delay'));
            }
            if(current.data('loop')=='yes'){
                if(current.data('yoyo')=='yes'){
                    animation.yoyo(true);
                }
                animation.repeat(-1);
                if(current.data('delay') && current.data('repeatdelay')=='yes'){
                    animation.repeatDelay(current.data('delay'));
                }
            }

            var multianimations = current.data('multianimations');
            if(multianimations){
            
                for(var curr = 0; curr < multianimations.length; curr++){

                    let rx = multianimations[curr].multi_rx;
                    let ry = multianimations[curr].multi_ry;
                    let r = multianimations[curr].multi_r;
                    let px = multianimations[curr].multi_x;
                    let py = multianimations[curr].multi_y;
                    let pxo = multianimations[curr].multi_xo;
                    let pyo = multianimations[curr].multi_yo;
                    let sc = multianimations[curr].multi_scale;
                    let scx = multianimations[curr].multi_scale_x;
                    let scy = multianimations[curr].multi_scale_y;
                    let width = multianimations[curr].multi_width;
                    let height = multianimations[curr].multi_height;
                    let opacity = multianimations[curr].multi_opacity;
                    let bg = multianimations[curr].multi_bg;
                    let origin = multianimations[curr].multi_origin;
                    let de = multianimations[curr].multi_delay;
                    let ea = multianimations[curr].multi_ease;
                    let du = multianimations[curr].multi_duration;
                    let from = multianimations[curr].multi_from;
                    let customtime = multianimations[curr].multi_time;
                    let customobj = multianimations[curr].multi_obj;
                    
                    let multiargs = {};
                    if(rx) multiargs.rotationX = parseFloat(rx);
                    if(ry) multiargs.rotationY = parseFloat(ry);
                    if(r) multiargs.rotation = parseFloat(r);
                    if(px) multiargs.x = parseFloat(px);
                    if(py) multiargs.y = parseFloat(py);
                    if(pxo) multiargs.xPercent = parseFloat(pxo);
                    if(pyo) multiargs.yPercent = parseFloat(pyo);
                    if(sc) multiargs.scale = parseFloat(sc);
                    if(scx) multiargs.scaleX = parseFloat(scx);
                    if(scy) multiargs.scaleY = parseFloat(scy);
                    if(opacity) multiargs.opacity = parseInt(opacity)/100;
                    if(du) multiargs.duration = parseFloat(du);
                    if(de) multiargs.delay = parseFloat(de);
                    if(origin) multiargs.transformOrigin = parseFloat(origin);
                    if(!customtime) customtime = ">";
                    if(ea){
                        var $ease = ea.split("-");
                        multiargs.ease = $ease[0]+"."+$ease[1];
                        if(multiargs.ease === "power0.none"){           
                            multiargs.ease = "none";
                        }
                    }
                    if(customobj && $(customobj).length > 0){
                        $anobj = $(customobj);
                    }
                    if(from=="yes"){
                        animation.from($anobj, multiargs, customtime);
                    }else{
                        animation.to($anobj, multiargs, customtime);
                    }
                }
            
            }
            
            if(current.data('customtrigger')){
                var customtrigger = '#'+current.data('customtrigger');               
            }else{
                var customtrigger = $(this).closest('.elementor-widget');
            }
            scrollargs.triggerElement = customtrigger;

            if(current.data('triggerheight')){
                var $hookpos = parseInt(current.data('triggerheight'))/100;
                scrollargs.triggerHook = $hookpos;
            }else{
                scrollargs.triggerHook = 0.85;
            }

            if(current.data('scrollduration')){
                var $hookdur = current.data('scrollduration');
                scrollargs.duration = $hookdur;
            }   

            var scene = new ScrollMagic.Scene(scrollargs).setTween(animation).addTo(rhscroller);
            if(current.data('pin') && current.data('scrollduration')){
                var pin = '#'+current.data('pin'); 
                scene.setPin(pin);             
            }
            if(current.data('rev')){
                scene.reverse(false);
            } 
        });
    }

    //reveal
    if($('.rh-reveal-wrap').length > 0){
        $('.rh-reveal-wrap').each(function(){
            var tl = gsap.timeline({paused: true}); 
            var revealwrap = $(this); 
            var revealcover = $(this).find(".rh-reveal-block");
            var revealcontent = $(this).find(".rh-reveal-cont"); 
            revealwrap.removeClass('rhhidden');
            if(revealcover.data('reveal-speed')){
                var $coverspeed = revealcover.data('reveal-speed');
            }else{
                var $coverspeed = 0.5;
            } 
            if(revealcover.data('reveal-delay')){
                var $coverdelay = revealcover.data('reveal-delay');
            }else{
                var $coverdelay = 0;
            }  
            $(this).find('img.lazyload').each(function(){
                var source = $(this).attr("data-src");
                $(this).attr("src", source).css({'opacity': '1'});
            });            
            if(revealcover.data('reveal-dir')=='lr'){
                tl.from(revealcover,{ duration:$coverspeed, scaleX: 0, transformOrigin: "left", delay: $coverdelay  });
                tl.to(revealcover,{ duration:$coverspeed, scaleX: 0, transformOrigin: "right" }, "reveal");
            }else if(revealcover.data('reveal-dir')=='rl'){
                tl.from(revealcover,{ duration:$coverspeed, scaleX: 0, transformOrigin: "right", delay: $coverdelay });
                tl.to(revealcover,{ duration:$coverspeed, scaleX: 0, transformOrigin: "left" }, "reveal");
            }
            else if(revealcover.data('reveal-dir')=='tb'){
                tl.from(revealcover,{ duration:$coverspeed, scaleY: 0, transformOrigin: "top", delay: $coverdelay });
                tl.to(revealcover,{ duration:$coverspeed, scaleY: 0, transformOrigin: "bottom" }, "reveal");
            }
            else if(revealcover.data('reveal-dir')=='bt'){
                tl.from(revealcover,{ duration:$coverspeed, scaleY: 0, transformOrigin: "bottom", delay: $coverdelay });
                tl.to(revealcover,{ duration:$coverspeed, scaleY: 0, transformOrigin: "top" }, "reveal");
            }
            tl.from(revealcontent,{ duration:1, opacity: 0 }, "reveal"); 
            revealwrap.elementorWaypoint(function(direction) {
                tl.play();
            }, { offset: 'bottom-in-view' });          
        });
    }

    //mouse move
    if($('.rh-prlx-mouse').length > 0){
        $('#content').mousemove(function(event){

            var xPos = (event.clientX/$('#content').width())-0.5,
               yPos = (event.clientY/$('#content').height())-0.5;
          
            $(".rh-prlx-mouse").each(function(index, element){

                var mouseargs = {};
                var curmouse = $(this);

                if(curmouse.data('prlx-xy')){
                    var $speedx = curmouse.data('prlx-xy');
                    mouseargs.x = xPos * $speedx;
                    mouseargs.y = yPos * $speedx;
                }

                if(curmouse.data('prlx-tilt')){
                    var $speedtilt = curmouse.data('prlx-tilt');
                    mouseargs.rotationY = xPos * $speedtilt;
                    mouseargs.rotationX = yPos * $speedtilt;
                }

                mouseargs.ease = Power1.easeOut;            

                gsap.to(curmouse, mouseargs);
            });  
        });
    }

});