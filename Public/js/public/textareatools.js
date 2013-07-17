var textareaTools = {
        
        /**
         * 获取输入光标在页面中的坐标 【..】
         * @param                {HTMLElement}        输入框元素
         * @param                {Boolean}                返回焦点光标顶部的top值(默认false)
         * @return                {Object}                返回left和top
         */
        getInputOffset: function (elem, focusTop, addleftoffset) {
                var that = this,
                        cloneDiv = '{$clone_div}',
                        cloneLeft = '{$cloneLeft}',
                        cloneFocus = '{$cloneFocus}',
                        cloneRight = '{$cloneRight}',
                        none = '<span style="white-space:pre-wrap;"> </span>',
                        div = elem[cloneDiv] || document.createElement('div'),
                        left = elem[cloneLeft] || document.createElement('span'),
                        focus = elem[cloneFocus] || document.createElement('span'),
                        right = elem[cloneRight] || document.createElement('span'),
                        offset = that._offset(elem),
                        index = this._getFocus(elem),
                        focusOffset = {
                                left: 0,
                                top: 0
                        };
                
                if (!elem[cloneDiv]) {
                        elem[cloneDiv] = div;
                        elem[cloneLeft] = left;
                        elem[cloneFocus] = focus;
                        elem[cloneRight] = right;
                        div.appendChild(left);
                        div.appendChild(focus);
                        div.appendChild(right);
                        document.body.appendChild(div);
                        focus.innerHTML = '|';
                        focus.style.cssText = 'display:inline-block;width:0px;overflow:hidden';
                        div.className = this._cloneStyle(elem);
                        //div.setAttribute('contenteditable', true);
                        div.style.cssText = "/*visibility:hidden;*/display:inline-block;position:absolute;z-index:-1000;left:" + addleftoffset + "px;top:110px;word-wrap:break-word;word-break:break-all;overflow:hidden;";
                };
                
                div.style.width = this._getStyle(elem, 'width');
                div.style.height = this._getStyle(elem, 'height');
                
                try {
                        div.scrollLeft = elem.scrollLeft;
                        div.scrollTop = elem.scrollTop;
                } catch (e) {};// IE8

                
                left.innerHTML = elem.value.substring(0, index).
                        replace(/</g,'<').replace(/>/g,'>').replace(/\n/g,'<br/>').replace(/\s/g, none);
                right.innerHTML = elem.value.substring(index, elem.value.length).
                        replace(/</g,'<').replace(/>/g,'>').replace(/\n/g,'<br/>').replace(/\s/g, none);
                        
                focus.style.display = 'inline-block';
                try {focusOffset = this._offset(focus);} catch (e) {};
                focus.style.display = 'none';
                
                return {
                        left: offset.left + focusOffset.left,
                        top: offset.top + focusOffset.top + (focusTop ? focus.offsetHeight : 0)
                };
        },
        
        // 克隆元素样式并返回类
        _cloneStyle: function (elem, cache) {
                if (!cache && elem['${cloneName}']) return elem['${cloneName}'];
        
                var className, name,
                        rstyle = /^(number|string)$/,
                        rname = /^(content|outline|outlineWidth)$/, //Opera: content; IE8:outline && outlineWidth
                        cssText = [],
                        sStyle = elem.style;

                for (name in sStyle) {
                        if (!rname.test(name)) {
                                val = this._getStyle(elem, name);
                                if (val !== '' && rstyle.test(typeof val)) { // Firefox 4
                                        name = name.replace(/([A-Z])/g,"-$1").toLowerCase();
                                        cssText.push(name);
                                        cssText.push(':');
                                        cssText.push(val);
                                        cssText.push(';');
                                };
                        };
                };
                cssText = cssText.join('');
                
                elem['${cloneName}'] = className = 'clone' + (new Date).getTime();
                this._addHeadStyle('.' + className + '{' + cssText + '}');
                
                return className;
        },
        
        // 向页头插入样式
        _addHeadStyle: function (content) {
                var style = this._style[document];
                if (!style) {
                        style = this._style[document] = document.createElement('style');
                        document.getElementsByTagName('head')[0].appendChild(style);
                };
                style.styleSheet && (style.styleSheet.cssText += content) || style.appendChild(document.createTextNode(content));
        },
        _style: {},
        
        // 获取最终样式
        _getStyle: 'getComputedStyle' in window ? function (elem, name) {
                return getComputedStyle(elem, null)[name];
        } : function (elem, name) {
                return elem.currentStyle[name];
        },
        
        // 绑定事件
        _addEvent: function (elem, type, callback) {
                elem.addEventListener ?
                        elem.addEventListener(type, callback, false) :
                        elem.attachEvent('on' + type, callback);
        },
        
        // 获取光标在文本框的位置
        _getFocus: function (elem) {
                var index = 0;
                if (elem.nodeName === 'TEXTAREA') {
                        if (document.selection) { // IE Support 
                                elem.focus();
                                var Sel = document.selection.createRange();
                                var Sel2 = Sel.duplicate();
                                Sel2.moveToElementText(elem);
                                var index = -1;
                                while (Sel2.inRange(Sel)) {
                                        Sel2.moveStart('character');
                                        index++;
                                };
                        } else if (elem.selectionStart || elem.selectionStart == '0') { // Firefox support 
                                index = elem.selectionStart;
                        };
                        return (index);
                        
                } else { // input
                        if (document.selection) { // IE Support 
                                elem.focus();
                                var Sel = document.selection.createRange();
                                Sel.moveStart('character', -elem.value.length);
                                index = Sel.text.length;
                        } else if (elem.selectionStart || elem.selectionStart == '0') { // Firefox support 
                                index = elem.selectionStart;
                        };
                        return (index);
                };
        },
        
        // 获取元素在页面中位置
        _offset: function (elem) {
                var box = elem.getBoundingClientRect(),
                        doc = elem.ownerDocument,
                        body = doc.body,
                        docElem = doc.documentElement,
                        clientTop = docElem.clientTop || body.clientTop || 0,
                        clientLeft = docElem.clientLeft || body.clientLeft || 0,
                        top = box.top + (self.pageYOffset || docElem.scrollTop) - clientTop,
                        left = box.left + (self.pageXOffset || docElem.scrollLeft) - clientLeft;

                return {
                        left: left,
                        top: top
                };
        }
        
};
