window.CodiqaControls = {
  types: {},
  instances: {},

  define: function(type, control) {
    control._type = type;
    this.types[type] = control;
  },

  register: function(type, id, opts) {
    var instance = new this.types[type]();
    instance._type = type;
    instance._id = id;
    instance._opts = opts;
    this.instances[id] = instance;

    if(!this.types[type].prototype._isInited) {
      this.types[type].prototype.initType();
    }
    return instance;
  },

  init: function() {
    for(var type in this.types) {
      this.types[type].prototype.initType();
    }
  },

  refresh: function() {
    for(var x in this.instances) {
      this.instances[x].refresh && this.instances[x].refresh();
    }
  },

  callbackInit: function() {

  },

  getInstances: function(type) {
    var x, instance, instances = [];
    for(x in this.instances) {
      instance = this.instances[x];
      if(instance._type === type) {
        instances.push(instance);
      }
    }
    return instances;
  }

};


CodiqaControls.GoogleMap = function () {};
CodiqaControls.GoogleMap.prototype.initType = function() {
  if( window.CodiqaControls.getInstances('googlemaps').length ) {
    if(this._isInited) {
      if(window.google && window.google.maps) {
        CodiqaControls.GoogleMap.prototype.callbackInit();
      }
    } else {
      var script = document.createElement('script');
      script.type = "text/javascript";
      script.src = "https://maps.googleapis.com/maps/api/js?sensor=true&callback=CodiqaControls.types.googlemaps.prototype.callbackInit";
      document.getElementsByTagName("head")[0].appendChild(script);
      this._isInited = true;
    }
  }
};
CodiqaControls.GoogleMap.prototype.callbackInit = function() {
  var x, instances = window.CodiqaControls.getInstances('googlemaps');
  for(x = 0; x < instances.length; x++) {
    instances[x]._opts.ready(instances[x]);
  }
};
CodiqaControls.GoogleMap.prototype.refresh = function() {
  if( this.map && this.el && $(this.el).closest('.ui-page-active').length ) {
    google.maps.event.trigger(this.map, 'resize');
    this.center && this.map.setCenter(this.center);
  }
};
window.CodiqaControls.define('googlemaps', CodiqaControls.GoogleMap);


(function($) {
  $.widget('mobile.tabbar', $.mobile.navbar, {
    _create: function() {
      // Set the theme before we call the prototype, which will 
      // ensure buttonMarkup() correctly grabs the inheritied theme.
      // We default to the "a" swatch if none is found
      var theme = this.element.jqmData('theme') || "a";
      this.element.addClass('ui-footer ui-footer-fixed ui-bar-' + theme);

      // Make sure the page has padding added to it to account for the fixed bar
      this.element.closest('[data-role="page"]').addClass('ui-page-footer-fixed');


      // Call the NavBar _create prototype
      $.mobile.navbar.prototype._create.call(this);
    },

    // Set the active URL for the Tab Bar, and highlight that button on the bar
    setActive: function(url) {
      // Sometimes the active state isn't properly cleared, so we reset it ourselves
      this.element.find('a').removeClass('ui-btn-active ui-state-persist');
      this.element.find('a[href="' + url + '"]').addClass('ui-btn-active ui-state-persist');
    }
  });

  $(document).on('pagecreate create', function(e) {
    return $(e.target).find(":jqmData(role='tabbar')").tabbar();
  });
  
  $(document).on('pageshow', ":jqmData(role='page')", function(e) {
    // Grab the id of the page that's showing, and select it on the Tab Bar on the page
    var tabBar, id = $(e.target).attr('id');

    tabBar = $.mobile.activePage.find(':jqmData(role="tabbar")');
    if(tabBar.length) {
      tabBar.tabbar('setActive', '#' + id);
    }

    window.CodiqaControls.refresh();
  });

  window.CodiqaControls.init();
  
})(jQuery);

$().ready(function(){
	mseeageNumsOnce();
	
	$("#delete_btn_cancel").click(function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
	
	if ("6.0" == $.browser.version || "7.0" == $.browser.version || "8.0" == $.browser.version) {
		alert('您的web浏览器版本太低，请用网页版浏览');
		window.location.href = baseUrl + "?changeviewtype=web";
	}
});

function mseeageNumsOnce() {
    $.ajax({
        type: "POST",
        url: baseUrl + "ajax/getmessage",
        global: false,
        data: {acquireseconds: 'default'},
        dataType: "json",
        success: function (msg) {
            if (msg.status == 'ok') {
                var acquiremilliseconds = msg.data.acquireSeconds;
                var totalmsgnums = parseInt(msg.data.messageSystemNums) + parseInt(msg.data.messageCommentNums) + parseInt(msg.data.messageAtNums) + parseInt(msg.data.messageTalkNums);
                if (totalmsgnums != 0) {
                	$('#footer_mobile_message').find('.ui-btn-text').append(" <span class='reb_back'>"+totalmsgnums+"</span>");
                }
            }
        },
        timeout: 10000,
        error: function () {
            //alert('圈圈亲，系统检测到您断网了!');
            setTimeout('mseeageNumsOnce()', 1000);
        }
    });
}

function ajaxInfo(htmlobj, infotype , deletevalue) {
	if (deletevalue != 0) {
		$("#delete_btn_cancel").find('.ui-btn-text').html("取消");
		$("#delete_btn_yes").show().val(deletevalue);
		$("#delete_btn_yes").attr({'infotype' : infotype});
	} else {
		$("#delete_btn_yes").hide();
		$("#delete_btn_cancel").find('.ui-btn-text').html("确定");
	}
    $("#ajax_info_div_outer").show();
    $("#ajax_info_div").fadeIn('fast');
    $("#ajax_info_div_msg_in").fadeIn('fast').html(htmlobj);
}
