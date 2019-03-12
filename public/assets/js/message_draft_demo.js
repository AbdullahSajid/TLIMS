var handleTooltips = function () {
    // Initialize tooltip component
     $('[data-toggle="tooltip"]').tooltip(); 
         
};

var Draft = function () {
    "use strict";
    return {
        init: function () {
            handleTooltips();
        }
    }
}();